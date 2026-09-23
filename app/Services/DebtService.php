<?php

namespace App\Services;

use App\Enums\DebtStatus;
use App\Models\ActivityLog;
use App\Models\NongkrongSession;

/**
 * Net balance + Debt Simplification (greedy matching) + regenerate.
 * Invariant yang dijaga: sum(balance) == 0, dari != ke, jumlah transaksi <= n-1.
 */
class DebtService
{
    /**
     * Balance per user dalam satu session (CREDITOR = positif, DEBTOR = negatif).
     *
     * @return array<int, int>
     */
    public static function netBalances(NongkrongSession $session): array
    {
        $balances = [];

        foreach ($session->expenses()->with('splits')->get() as $expense) {
            $total = $expense->grandTotal();
            $paidBy = $expense->paid_by_user_id;

            // Orang yang nombok harusnya diterima total...
            $balances[$paidBy] = ($balances[$paidBy] ?? 0) + $total;

            // ...setiap peserta (termasuk payer kalau ikut) punya kewajiban sebesar share-nya.
            foreach ($expense->splits as $split) {
                $balances[$split->user_id] = ($balances[$split->user_id] ?? 0) - $split->share_amount;
            }
        }

        return $balances;
    }

    /**
     * Greedy matching debtor ↔ creditor → daftar kewajiban minimal.
     *
     * @param  array<int, int>  $balances
     * @return array<int, array{from: int, to: int, amount: int}>
     */
    public static function debtsFromBalances(array $balances): array
    {
        $debtors = [];
        $creditors = [];
        foreach ($balances as $userId => $balance) {
            if ($balance < 0) {
                $debtors[$userId] = -$balance;
            } elseif ($balance > 0) {
                $creditors[$userId] = $balance;
            }
        }

        $debtorIds = array_keys($debtors);
        $creditIds = array_keys($creditors);

        // Utang paling gede dulu, tagihan paling gede dulu.
        usort($debtorIds, fn ($a, $b) => $debtors[$b] <=> $debtors[$a]);
        usort($creditIds, fn ($a, $b) => $creditors[$b] <=> $creditors[$a]);

        $debts = [];
        $i = 0;
        $j = 0;
        while ($i < count($debtorIds) && $j < count($creditIds)) {
            $from = $debtorIds[$i];
            $to = $creditIds[$j];
            $amount = min($debtors[$from], $creditors[$to]);

            if ($amount > 0 && $from !== $to) {
                $debts[] = ['from' => $from, 'to' => $to, 'amount' => $amount];
            }

            $debtors[$from] -= $amount;
            $creditors[$to] -= $amount;

            if ($debtors[$from] === 0) {
                $i++;
            }
            if ($creditors[$to] === 0) {
                $j++;
            }
        }

        return $debts;
    }

    /**
     * Ganti semua debt PENDING dengan hasil hitung terbaru.
     * Debt yang udah SETTLED sengaja dipertahankan sebagai histori/audit.
     */
    public static function regenerateFor(NongkrongSession $session): void
    {
        $balances = self::netBalances($session);
        $newDebts = self::debtsFromBalances($balances);

        $session->debts()->where('status', DebtStatus::PENDING->value)->delete();

        foreach ($newDebts as $debt) {
            $session->debts()->create([
                'from_user_id' => $debt['from'],
                'to_user_id' => $debt['to'],
                'amount' => $debt['amount'],
                'status' => DebtStatus::PENDING->value,
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'event' => 'debt_recomputed',
            'description' => 'Debt dihitung ulang ('.count($newDebts).' kewajiban).',
            'auditable_type' => NongkrongSession::class,
            'auditable_id' => $session->id,
            'properties' => ['total' => array_sum(array_column($newDebts, 'amount'))],
        ]);
    }
}
