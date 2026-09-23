<?php

namespace Tests\Feature\Nongkrong;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DebtPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_lapor_bayar_lalu_kreditur_konfirmasi_debt_settled(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        // Debtor lapor bayar
        $this->apiAs($b);
        $report = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'transfer',
            'note' => 'sudah ku kirim yaa',
        ]);

        $report->assertCreated()
            ->assertJsonPath('data.amount', 50_000)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'payment_reported']);
        $this->assertDatabaseHas('debt_payments', ['debt_id' => $debt->id, 'status' => 'pending']);

        // Kreditur konfirmasi → lunas
        $this->apiAs($a);
        $confirm = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/{$report->json('data.id')}/confirm", [
            'decision' => 'confirmed',
        ]);

        $confirm->assertOk()
            ->assertJsonPath('data.status', 'settled')
            ->assertJsonPath('data.settled_by.id', $a->id)
            ->assertJsonPath('data.remaining_amount', 0);

        $this->assertDatabaseHas('debts', [
            'id' => $debt->id,
            'status' => 'settled',
            'paid_amount' => 50_000,
            'settled_by_user_id' => $a->id,
        ]);
    }

    public function test_lapor_ditolak_lalu_lapor_ulang(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->apiAs($b);
        $report = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'cash',
        ])->assertCreated();

        $paymentId = $report->json('data.id');

        // Kreditur tolak
        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/{$paymentId}/reject", [
            'decision' => 'rejected',
            'review_note' => 'Buktinya mana?',
        ])->assertOk()->assertJsonPath('data.status', 'rejected');

        $this->assertDatabaseHas('debt_payments', [
            'id' => $paymentId,
            'status' => 'rejected',
            'review_note' => 'Buktinya mana?',
        ]);

        // Debtor lapor ulang → status payment_reported lagi, report baru dibuat
        $this->apiAs($b);
        $reReport = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'transfer',
            'note' => 'ini transfer beneran',
        ])->assertCreated();

        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'payment_reported']);

        // Konfirmasi report kedua → lunas
        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/{$reReport->json('data.id')}/confirm", [
            'decision' => 'confirmed',
        ])->assertOk()->assertJsonPath('data.status', 'settled');

        // Histori tetap dua report
        $this->assertDatabaseCount('debt_payments', 2);
    }

    public function test_cicilan_partial_lalu_lunas(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        // Cicil 20k
        $this->apiAs($b);
        $partial = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 20_000,
            'method' => 'e_wallet',
        ])->assertCreated();

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/{$partial->json('data.id')}/confirm", [
            'decision' => 'confirmed',
        ])->assertOk()
            ->assertJsonPath('data.status', 'confirmed')
            ->assertJsonPath('data.paid_amount', 20_000)
            ->assertJsonPath('data.remaining_amount', 30_000);

        // Lunasin sisa 30k
        $this->apiAs($b);
        $rest = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 30_000,
            'method' => 'transfer',
        ])->assertCreated();

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/{$rest->json('data.id')}/confirm", [
            'decision' => 'confirmed',
        ])->assertOk()->assertJsonPath('data.status', 'settled');

        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'settled', 'paid_amount' => 50_000]);
    }

    public function test_nominal_lebih_besar_dari_sisa_utang_ditolak(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->apiAs($b);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 999_999,
            'method' => 'cash',
        ])->assertStatus(422);
    }

    public function test_hanya_debtor_yang_bisa_lapor_bayar(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);
        $c = User::factory()->create();
        $session->members()->attach($c);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        // Kreditur (a) pengen lapor sendiri → nggak boleh
        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'cash',
        ])->assertForbidden();

        // Teman lain (c) juga nggak boleh
        $this->apiAs($c);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'cash',
        ])->assertForbidden();
    }

    public function test_debtor_tidak_bisa_konfirmasi_pembayaran_sendiri(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->apiAs($b);
        $report = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'cash',
        ])->assertCreated();

        // B (debtor) konfirmasi bayar sendiri → 403
        $this->apiAs($b);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/{$report->json('data.id')}/confirm", [
            'decision' => 'confirmed',
        ])->assertForbidden();

        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'payment_reported']);
    }

    public function test_non_member_tidak_bisa_akses_dashboard_debt(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);
        $outsider = $this->apiAs(User::factory()->create());

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        // Ada report beneran biar yang dicek role-nya, bukan 404 ke data.
        $this->apiAs($b);
        $report = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'cash',
        ])->assertCreated();
        $paymentId = $report->json('data.id');

        $this->apiAs($outsider);
        $this->getJson("/api/v1/sessions/{$session->id}/debts")->assertForbidden();
        $this->getJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}")->assertForbidden();
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/$paymentId/confirm", [
            'decision' => 'confirmed',
        ])->assertForbidden();
    }

    public function test_payment_history_keawet_saat_expense_diregen(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        // Lapor + konfirmasi partial (20k) → debt CONFIRMED (in-flight, di-lock)
        $this->apiAs($b);
        $report = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 20_000,
            'method' => 'transfer',
        ])->assertCreated();

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/{$report->json('data.id')}/confirm", [
            'decision' => 'confirmed',
        ])->assertOk()->assertJsonPath('data.status', 'confirmed');

        // Edit expense → regenerate; debt confirmed harus kekunci tetep CONFIRMED
        $expense = $session->expenses()->firstOrFail();
        $this->apiAs($a);
        $this->patchJson("/api/v1/sessions/{$session->id}/expenses/{$expense->id}", [
            'name' => 'Makan lagi',
            'amount' => 100_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ])->assertOk();

        $debt->refresh();

        // Debt yang lagi jalan nggak boleh berubah / ilang
        $this->assertSame('confirmed', $debt->status);
        $this->assertSame(20_000, $debt->paid_amount);
        $this->assertSame(1, $debt->payments()->count());

        // Terus terang: nggak ada debt pending baru duplikat yang muncul karena sisa yang sama
        $duplicate = $session->debts()->where('from_user_id', $b->id)->where('status', 'pending')->where('id', '!=', $debt->id)->get();
        $this->assertEmpty($duplicate);
    }

    public function test_audit_event_tercatat_lengkap(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->apiAs($b);
        $report = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'cash',
        ])->assertCreated();

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/{$report->json('data.id')}/confirm", [
            'decision' => 'confirmed',
        ])->assertOk();

        $events = ActivityLog::where('auditable_type', \App\Models\Debt::class)
            ->where('auditable_id', $debt->id)
            ->pluck('event')
            ->all();

        $this->assertContains('payment_reported', $events);
        $this->assertContains('payment_confirmed', $events);
        $this->assertContains('debt_settled', $events);

        // debt_created tercatat di level session waktu expense dibuat
        $this->assertDatabaseHas('activity_logs', [
            'auditable_type' => \App\Models\NongkrongSession::class,
            'auditable_id' => $session->id,
            'event' => 'debt_created',
        ]);
    }

    public function test_dashboard_statistik_benar(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $this->apiAs($b);

        $this->getJson("/api/v1/sessions/{$session->id}/debts")
            ->assertOk()
            ->assertJsonPath('data.total_owed', 50_000)
            ->assertJsonPath('data.total_receivable', 0)
            ->assertJsonPath('data.active_count', 1)
            ->assertJsonPath('data.settled_count', 0);

        $this->apiAs($a);
        $this->getJson("/api/v1/sessions/{$session->id}/debts")
            ->assertOk()
            ->assertJsonPath('data.total_owed', 0)
            ->assertJsonPath('data.total_receivable', 50_000);
    }

    public function test_kreditur_tidak_bisa_konfirmasi_report_yang_sudah_diproses(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->apiAs($b);
        $report = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'cash',
        ])->assertCreated();
        $paymentId = $report->json('data.id');

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/$paymentId/confirm", [
            'decision' => 'confirmed',
        ])->assertOk();

        // Konfirmasi dua kali → ditolak
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments/$paymentId/confirm", [
            'decision' => 'confirmed',
        ])->assertStatus(422);
    }

    public function test_tidak_bisa_lapor_saat_masih_ada_report_pending(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->apiAs($b);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'cash',
        ])->assertCreated();

        // Coba lapor lagi selagi masih nunggu konfirmasi → ditolak
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/payments", [
            'amount' => 50_000,
            'method' => 'transfer',
        ])->assertStatus(422);
    }

    public function test_endpoint_settle_masih_bisa_dipakai(): void
    {
        [$a, $b, $session] = $this->sessionDenganUtang(100_000);

        $debt = $session->debts()->where('from_user_id', $b->id)->firstOrFail();

        $this->apiAs($b);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/settle")->assertOk();
        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'settled']);
    }

    private function sessionDenganUtang(int $amount): array
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b]);

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Makan rame',
            'amount' => $amount,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ])->assertCreated();

        return [$a, $b, $session];
    }

    private function createSession(array $membersByKey): \App\Models\NongkrongSession
    {
        $creator = $membersByKey['a'];
        $ids = array_map(fn ($user) => $user->id, array_diff_key($membersByKey, ['a' => true]));

        Sanctum::actingAs($creator);

        $response = $this->postJson('/api/v1/sessions', [
            'name' => 'Session #'.uniqid(),
            'date' => '2026-09-23',
            'member_ids' => array_values($ids),
        ]);

        $response->assertCreated();

        return \App\Models\NongkrongSession::findOrFail($response->json('data.id'));
    }

    private function apiAs(User $user): User
    {
        Sanctum::actingAs($user);

        return $user;
    }
}
