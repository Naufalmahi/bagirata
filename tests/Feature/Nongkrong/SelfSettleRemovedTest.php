<?php

namespace Tests\Feature\Nongkrong;

use App\Exceptions\BusinessException;
use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\User;
use App\Services\DebtPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * Debt HANYA boleh jadi 'settled' lewat DebtPaymentService, yaitu setelah
 * payment terkonfirmasi. Debtor tidak boleh menandai utangnya lunas sendiri.
 */
class SelfSettleRemovedTest extends TestCase
{
    use RefreshDatabase;

    public function test_route_settle_tidak_lagi_terdaftar(): void
    {
        $names = collect(app('router')->getRoutes()->getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter()
            ->values();

        $this->assertFalse($names->contains('debts.settle'));

        $uris = collect(app('router')->getRoutes()->getRoutes())
            ->map(fn ($route) => $route->uri())
            ->filter(fn ($uri) => str_contains($uri, 'debts/{debt}/settle'))
            ->values();

        $this->assertTrue($uris->isEmpty(), 'Endpoint settle masih ada: '.$uris->implode(', '));
    }

    public function test_policy_settle_sudah_dihapus(): void
    {
        $this->assertFalse(
            method_exists(\App\Policies\DebtPolicy::class, 'settle'),
            'DebtPolicy::settle masih ada dan mengizinkan self-settle.'
        );
    }

    public function test_endpoint_api_settle_menggolongkan_404(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang();

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        Sanctum::actingAs($b);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/settle")
            ->assertNotFound();

        $debt->refresh();
        $this->assertSame('pending', $debt->status);
        $this->assertNull($debt->settled_at);
    }

    public function test_endpoint_web_settle_menggolongkan_404(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang();

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->actingAs($b)
            ->post("/nongkrong/{$session->id}/debts/{$debt->id}/settle")
            ->assertNotFound();

        $debt->refresh();
        $this->assertSame('pending', $debt->status);
    }

    public function test_mark_settled_menolak_kalau_belum_dibayar_penuh(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang();

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();
        $this->assertSame(0, $debt->paid_amount);

        $this->expectException(BusinessException::class);

        $debt->markSettled($b);
    }

    public function test_mark_settled_menolak_saat_baru_dibayar_sebagian(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang();

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $payment = DebtPaymentService::report($b, $debt, [
            'amount' => 10_000,
            'method' => 'cash',
        ]);
        DebtPaymentService::confirm($a, $debt->fresh(), $payment);

        $debt->refresh();
        $this->assertSame(10_000, $debt->paid_amount);
        $this->assertSame(30_000, $debt->outstanding());

        $this->expectException(BusinessException::class);

        $debt->markSettled($a);
    }

    public function test_masih_bisa_lunas_lewat_konfirmasi_pembayaran(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang();

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $payment = DebtPaymentService::report($b, $debt, [
            'amount' => $debt->amount,
            'method' => 'cash',
        ]);
        DebtPaymentService::confirm($a, $debt->fresh(), $payment);

        $debt->refresh();
        $this->assertSame('settled', $debt->status);
        $this->assertSame($a->id, $debt->settled_by_user_id);
        $this->assertNotNull($debt->settled_at);
        $this->assertSame(
            1,
            DebtPayment::where('debt_id', $debt->id)->where('status', 'confirmed')->count()
        );
    }

    public function test_mark_settled_idempoten_tidak_menimpa_catat_lunas(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang();

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $payment = DebtPaymentService::report($b, $debt, [
            'amount' => $debt->amount,
            'method' => 'cash',
        ]);
        DebtPaymentService::confirm($a, $debt->fresh(), $payment);

        $debt->refresh();
        $settledAt = $debt->settled_at;
        $settledBy = $debt->settled_by_user_id;

        // Debtor telat-telat manggil markSettled lagi nggak boleh menimpa catatan.
        $this->travel(5)->minutes();
        $debt->markSettled($b);
        $debt->refresh();

        $this->assertTrue($settledAt->equalTo($debt->settled_at));
        $this->assertSame($settledBy, $debt->settled_by_user_id);
        $this->assertSame($a->id, $debt->settled_by_user_id);
    }

    private function sessionDenganUtang(): array
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        Sanctum::actingAs($a);

        $response = $this->postJson('/api/v1/sessions', [
            'name' => 'Session #'.uniqid(),
            'date' => '2026-09-23',
            'member_ids' => [$b->id],
        ]);
        $response->assertCreated();

        $session = \App\Models\NongkrongSession::findOrFail($response->json('data.id'));

        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Makan rame',
            'amount' => 80_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ])->assertCreated();

        $this->assertDatabaseHas('debts', [
            'nongkrong_session_id' => $session->id,
            'from_user_id' => $b->id,
            'to_user_id' => $a->id,
            'amount' => 40_000,
            'status' => 'pending',
        ]);

        return [$a, $b, $session];
    }
}
