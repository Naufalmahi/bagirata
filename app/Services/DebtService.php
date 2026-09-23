<?php

namespace App\Services;

use App\Enums\DebtStatus;
use App\Models\ActivityLog;
use App\Models\Debt;
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
     * Total yang masih wajib dibayar (semua debt aktif, termasuk outstanding cicilan).
     */
    public static function activeOutstandingTotal(NongkrongSession $session): int
    {
        return $session->debts()
            ->where('status', '!=', DebtStatus::SETTLED->value)
            ->get()
            ->sum(fn (Debt $debt) => $debt->outstanding());
    }

    /**
     * Ganti debt PENDING/REJECTED dengan hasil hitung terbaru.
     *
     * Aturan:
     * - Debt in-flight (PAYMENT_REPORTED / CONFIRMED) di-LOCK: nggak disentuh,
     *   dan sisanya jadi pengurang balance biar total tetap konsisten.
     * - Debt SETTLED juga jadi pengurang balance (transfer beneran udah terjadi),
     *   supaya debt yang udah lunas nggak "hidup lagi" pas dihitung ulang.
     * - Debt PENDING/REJECTED yang punya histori pembayaran di-soft-delete
     *   (histori payment tetap kekirim). Yang tanpa histori di-hard-delete.
     */
    public static function regenerateFor(NongkrongSession $session): void
    {
        $balances = self::netBalances($session);

        // 1. Lock in-flight + settled, kurangi balance dari yang udah beres/sedang berjalan.
        $locked = $session->debts()
            ->whereIn('status', [
                DebtStatus::PAYMENT_REPORTED->value,
                DebtStatus::CONFIRMED->value,
                DebtStatus::SETTLED->value,
            ])
            ->get();

        foreach ($locked as $debt) {
            $balances[$debt->from_user_id] = ($balances[$debt->from_user_id] ?? 0) + $debt->amount;
            $balances[$debt->to_user_id] = ($balances[$debt->to_user_id] ?? 0) - $debt->amount;
        }

        // 2. Bersihkan debt yang bisa diregenerasi (menjaga histori payment).
        $redeemable = $session->debts()
            ->whereIn('status', [DebtStatus::PENDING->value, DebtStatus::REJECTED->value])
            ->get();

        foreach ($redeemable as $debt) {
            $debt->payments()->exists()
                ? $debt->delete()
                : $debt->forceDelete();
        }

        // 3. Greedy residual → debt PENDING baru.
        $newDebts = self::debtsFromBalances($balances);
        foreach ($newDebts as $debt) {
            $session->debts()->create([
                'from_user_id' => $debt['from'],
                'to_user_id' => $debt['to'],
                'amount' => $debt['amount'],
                'status' => DebtStatus::PENDING->value,
            ]);
        }

        // 4. Audit ringkas: debt_created (kebetulan baru) atau debt_adjusted.
        self::auditRegeneration($session, $redeemable->count(), count($newDebts));
    }

    private static function auditRegeneration(NongkrongSession $session, int $before, int $after): void
    {
        $event = $before === 0 ? 'debt_created' : 'debt_adjusted';

        ActivityLog::create([
            'user_id' => auth()->id(),
            'event' => $event,
            'description' => $event === 'debt_created'
                ? 'Debt dibuat ('.$after.' kewajiban aktif).'
                : 'Debt disesuaikan ulang setelah pengeluaran berubah ('.$before.' → '.$after.').',
            'auditable_type' => NongkrongSession::class,
            'auditable_id' => $session->id,
            'properties' => ['before' => $before, 'after' => $after],
        ]);
    }
}
