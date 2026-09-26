<?php

namespace Tests\Feature\Nongkrong;

use App\Enums\DebtStatus;
use App\Models\Debt;
use App\Models\NongkrongSession;
use App\Models\User;
use App\Services\DebtPaymentService;
use App\Services\DebtService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Invariant DebtService::regenerateFor().
 *
 * Debt in-flight (payment_reported / confirmed) di-lock supaya siklunya
 * nggak dimakan pembulatan greedy, tapi bagian yang SUDAH dibayar harus
 * tetap mengurangi saldo residual — kalau tidak, kewajiban yang sama
 * dihitung dua kali dan muncul debt-phiantom.
 */
class DebtRegenerationInvariantTest extends TestCase
{
    use RefreshDatabase;

    public function test_regenerate_tidak_menduplikasi_sisa_utang_setelah_dibayar_sebagian(): void
    {
        [$a, $b, $c, $session] = $this->sessionTigaAnggota();

        $this->buatExpense($a, $session, 100_000);
        $this->buatExpense($a, $session, 20_000);

        $debtB = Debt::where('nongkrong_session_id', $session->id)
            ->where('from_user_id', $b->id)
            ->where('to_user_id', $a->id)
            ->firstOrFail();

        $this->assertSame(40_000, $debtB->amount);
        $this->assertSame(79_999, DebtService::activeOutstandingTotal($session));

        // B lapor bayar 10k dan A konfirmasi → debt jadi in-flight, sisa 30k.
        $payment = DebtPaymentService::report($b, $debtB, [
            'amount' => 10_000,
            'method' => 'cash',
        ]);
        DebtPaymentService::confirm($a, $debtB->fresh(), $payment);

        $debtB->refresh();
        $this->assertSame(DebtStatus::CONFIRMED, $debtB->status());
        $this->assertSame(10_000, $debtB->paid_amount);
        $this->assertSame(30_000, $debtB->outstanding());

        // Tambah expense → memicu regenerateFor().
        $this->buatExpense($a, $session, 10_000);

        // Sisa utang B tetap 30k, nggak nambah dan nggak jadi nol.
        $debtB->refresh();
        $this->assertSame(40_000, $debtB->amount);
        $this->assertSame(10_000, $debtB->paid_amount);
        $this->assertSame(30_000, $debtB->outstanding());

        // Nggak boleh ada debt phiantom antar anggota yang sama-sama debitur.
        $this->assertSame(0, Debt::where('from_user_id', $c->id)->where('to_user_id', $b->id)->count());
        $this->assertSame(0, Debt::where('from_user_id', $b->id)->where('to_user_id', $c->id)->count());

        // Total outstanding di DB = sisa kewajiban yang sebenarnya.
        $this->assertSame(
            $this->sisaKewajibanSebenarnya($session),
            DebtService::activeOutstandingTotal($session)
        );
    }

    public function test_debt_settled_tidak_hidup_lagi_setelah_regenerate(): void
    {
        [$a, $b, $c, $session] = $this->sessionTigaAnggota();

        $this->buatExpense($a, $session, 100_000);
        $this->buatExpense($a, $session, 20_000);

        $debtB = Debt::where('nongkrong_session_id', $session->id)
            ->where('from_user_id', $b->id)
            ->where('to_user_id', $a->id)
            ->firstOrFail();

        $payment = DebtPaymentService::report($b, $debtB, [
            'amount' => 40_000,
            'method' => 'cash',
        ]);
        DebtPaymentService::confirm($a, $debtB->fresh(), $payment);

        $debtB->refresh();
        $this->assertSame(DebtStatus::SETTLED, $debtB->status());

        $this->buatExpense($a, $session, 10_000);

        // Yang lunas tetap lunas dan tetap jadi riwayat.
        $debtB->refresh();
        $this->assertSame(DebtStatus::SETTLED, $debtB->status());

        // Oblikasi barunya tetap muncul sebagai debt_pending terpisah.
        $pending = Debt::where('nongkrong_session_id', $session->id)
            ->where('from_user_id', $b->id)
            ->where('to_user_id', $a->id)
            ->pending()
            ->firstOrFail();

        $this->assertSame(3_333, $pending->amount);
        $this->assertSame(0, $pending->paid_amount);

        $this->assertSame(
            $this->sisaKewajibanSebenarnya($session),
            DebtService::activeOutstandingTotal($session)
        );
    }

    /**
     * Sisa kewajiban = total tagihan (positif di netBalances) dikurangi uang
     * yang sudah benar-benar berpindah (confirmed + settled).
     */
    private function sisaKewajibanSebenarnya(NongkrongSession $session): int
    {
        $totalTagihan = 0;
        foreach (DebtService::netBalances($session) as $balance) {
            $totalTagihan += max(0, $balance);
        }

        $sudahBerpindah = (int) Debt::where('nongkrong_session_id', $session->id)
            ->whereIn('status', [
                DebtStatus::CONFIRMED->value,
                DebtStatus::SETTLED->value,
            ])
            ->sum('paid_amount');

        return $totalTagihan - $sudahBerpindah;
    }

    private function buatExpense(User $payer, NongkrongSession $session, int $amount): void
    {
        $response = $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Pengeluaran '.$amount,
            'amount' => $amount,
            'paid_by_user_id' => $payer->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => $session->members()->pluck('users.id')->all(),
        ]);

        $response->assertCreated();
    }

    private function sessionTigaAnggota(): array
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $c = User::factory()->create();

        Sanctum::actingAs($a);

        $response = $this->postJson('/api/v1/sessions', [
            'name' => 'Session #'.uniqid(),
            'date' => '2026-09-23',
            'member_ids' => [$b->id, $c->id],
        ]);
        $response->assertCreated();

        $session = NongkrongSession::findOrFail($response->json('data.id'));

        return [$a, $b, $c, $session];
    }
}
