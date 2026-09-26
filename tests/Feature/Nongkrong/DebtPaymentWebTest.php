<?php

namespace Tests\Feature\Nongkrong;

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\NongkrongSession;
use App\Models\User;
use App\Services\SessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * routes/web.php mendaftarkan debts.payments.store / confirm / reject ke
 * Web\DebtController, tapi ketiga method-nya tidak pernah ada. Setiap submit
 * dari resources/views/debts/show.blade.php berakhir BadMethodCallException,
 * jadi seluruh UI pembayaran utang di web mati total sementara API-nya jalan.
 */
class DebtPaymentWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_lapor_bayar_lewat_web_disimpan(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->actingAs($b)
            ->from(route('debts.show', [$session, $debt]))
            ->post(route('debts.payments.store', [$session, $debt]), [
                'amount' => 50_000,
                'method' => 'qris',
                'note' => 'udah transfer yaa',
            ])
            ->assertRedirect(route('debts.show', [$session, $debt]))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('debt_payments', [
            'debt_id' => $debt->id,
            'amount' => 50_000,
            'method' => 'qris',
            'status' => 'pending',
            'reported_by_user_id' => $b->id,
        ]);

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'status' => 'payment_reported',
        ]);
    }

    public function test_kreditur_konfirmasi_lewat_web_bereskan_utang(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();
        $payment = $this->laporViaWeb($b, $session, $debt, 50_000);

        $this->actingAs($a)
            ->post(route('debts.payments.confirm', [$session, $debt, $payment]), [
                'decision' => 'confirmed',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'status' => 'settled',
            'paid_amount' => 50_000,
            'settled_by_user_id' => $a->id,
        ]);
    }

    public function test_kreditur_tolak_lewat_web(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();
        $payment = $this->laporViaWeb($b, $session, $debt, 50_000);

        $this->actingAs($a)
            ->post(route('debts.payments.reject', [$session, $debt, $payment]), [
                'decision' => 'rejected',
                'review_note' => 'Buktinya belum keliatan',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('debt_payments', [
            'id' => $payment->id,
            'status' => 'rejected',
            'review_note' => 'Buktinya belum keliatan',
        ]);

        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'rejected']);
    }

    public function test_tolak_wajib_pakai_alasan(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();
        $payment = $this->laporViaWeb($b, $session, $debt, 50_000);

        $this->actingAs($a)
            ->from(route('debts.show', [$session, $debt]))
            ->post(route('debts.payments.reject', [$session, $debt, $payment]), [
                'decision' => 'rejected',
                'review_note' => '',
            ])
            ->assertRedirect(route('debts.show', [$session, $debt]))
            ->assertSessionHasErrors('review_note');

        $this->assertDatabaseHas('debt_payments', ['id' => $payment->id, 'status' => 'pending']);
    }

    public function test_bukan_debtor_gak_bisa_lapor_bayar(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->actingAs($a)
            ->post(route('debts.payments.store', [$session, $debt]), [
                'amount' => 50_000,
                'method' => 'qris',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('debt_payments', 0);
    }

    public function test_debtor_gak_bisa_konfirmasi_pembayarannya_sendiri(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();
        $payment = $this->laporViaWeb($b, $session, $debt, 50_000);

        $this->actingAs($b)
            ->post(route('debts.payments.confirm', [$session, $debt, $payment]), [
                'decision' => 'confirmed',
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'payment_reported']);
    }

    public function test_luar_grup_gak_bisa_lapor_bayar(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();
        $orangLuar = User::factory()->create();

        $this->actingAs($orangLuar)
            ->post(route('debts.payments.store', [$session, $debt]), [
                'amount' => 50_000,
                'method' => 'qris',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('debt_payments', 0);
    }

    public function test_nominal_lebih_besar_dari_sisa_utang_ditolak(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->actingAs($b)
            ->from(route('debts.show', [$session, $debt]))
            ->post(route('debts.payments.store', [$session, $debt]), [
                'amount' => 999_999,
                'method' => 'qris',
            ])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('debt_payments', 0);
    }

    public function test_gak_bisa_lapor_lagi_sambil_ada_yang_pending(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();
        $this->laporViaWeb($b, $session, $debt, 50_000);

        $this->actingAs($b)
            ->from(route('debts.show', [$session, $debt]))
            ->post(route('debts.payments.store', [$session, $debt]), [
                'amount' => 10_000,
                'method' => 'qris',
            ])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('debt_payments', 1);
    }

    public function test_payment_dari_utang_lain_gak_bisa_dikonfirmasi(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();
        $payment = $this->laporViaWeb($b, $session, $debt, 50_000);

        // Debt fabricated yang nggak nyambung ke session ini.
        $debtLain = Debt::factory()->create([
            'nongkrong_session_id' => $session->id,
            'from_user_id' => $b->id,
            'to_user_id' => $a->id,
            'amount' => 10_000,
        ]);

        $this->actingAs($a)
            ->post(route('debts.payments.confirm', [$session, $debtLain, $payment]), [
                'decision' => 'confirmed',
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('debt_payments', ['id' => $payment->id, 'status' => 'pending']);
    }

    public function test_duit_nya_bener_bener_bergeser_setelah_konfirmasi(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->assertSame(50_000, $debt->outstanding());

        // Cicilan 20rb.
        $cicilan1 = $this->laporViaWeb($b, $session, $debt, 20_000);
        $this->actingAs($a)->post(route('debts.payments.confirm', [$session, $debt, $cicilan1]), [
            'decision' => 'confirmed',
        ])->assertRedirect();

        $debt->refresh();
        $this->assertSame('confirmed', $debt->status);
        $this->assertSame(20_000, $debt->paid_amount);
        $this->assertSame(30_000, $debt->outstanding());

        // Cicilan sisanya.
        $cicilan2 = $this->laporViaWeb($b, $session, $debt, 30_000);
        $this->actingAs($a)->post(route('debts.payments.confirm', [$session, $debt, $cicilan2]), [
            'decision' => 'confirmed',
        ])->assertRedirect();

        $debt->refresh();
        $this->assertSame('settled', $debt->status);
        $this->assertSame(50_000, $debt->paid_amount);
        $this->assertSame(0, $debt->outstanding());
    }

    public function test_halaman_detail_utang_bisa_dirender(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->actingAs($b)
            ->get(route('debts.show', [$session, $debt]))
            ->assertOk();
    }

    private function laporViaWeb(User $user, NongkrongSession $session, Debt $debt, int $amount): DebtPayment
    {
        $this->actingAs($user)
            ->post(route('debts.payments.store', [$session, $debt]), [
                'amount' => $amount,
                'method' => 'transfer',
            ])
            ->assertRedirect();

        return DebtPayment::where('debt_id', $debt->id)->latest('id')->firstOrFail();
    }

    private function sessionDenganUtang(int $amount): array
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = SessionService::create($a, [
            'name' => 'Hitung '.\uniqid(),
            'date' => now()->format('Y-m-d'),
            'member_ids' => [$b->id],
        ]);

        $this->actingAs($a)->post(route('expenses.store', $session), [
            'name' => 'Nasi rame',
            'amount' => $amount,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ])->assertRedirect();

        return [$a, $b, $session];
    }
}
