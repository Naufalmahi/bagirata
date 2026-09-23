<?php

namespace Tests\Feature\Nongkrong;

use App\Models\ActivityLog;
use App\Models\Group;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_buat_patungan_adhoc_dengan_anggota(): void
    {
        $creator = $this->apiAs(User::factory()->create());
        $b = User::factory()->create();
        $c = User::factory()->create();

        $response = $this->postJson('/api/v1/sessions', [
            'name' => 'Ngopi Bareng',
            'date' => '2026-09-20',
            'description' => 'Warkop deket kampus',
            'member_ids' => [$b->id, $c->id],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Ngopi Bareng')
            ->assertJsonPath('data.status', 'draft');

        $this->assertCount(3, $response->json('data.members'));
        $this->assertDatabaseHas('nongkrong_sessions', ['user_id' => $creator->id, 'name' => 'Ngopi Bareng']);
    }

    public function test_buat_patungan_di_group_member_bisa(): void
    {
        $owner = User::factory()->create();
        $creator = $this->apiAs(User::factory()->create());

        $group = $this->createGroupWithMember($owner, $creator);
        $channel = $group->channels()->create(['name' => 'Jajan', 'created_by' => $owner->id]);

        $response = $this->postJson('/api/v1/sessions', [
            'name' => 'Bakso 4 Life',
            'date' => '2026-09-21',
            'group_id' => $group->id,
            'channel_id' => $channel->id,
            'member_ids' => [$owner->id],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.group_id', $group->id)
            ->assertJsonPath('data.channel_id', $channel->id);
    }

    public function test_member_tanpa_permission_tidak_bisa_bikin_patungan_di_group(): void
    {
        $owner = User::factory()->create();
        $member = $this->apiAs(User::factory()->create());

        $group = $this->createGroupWithMember($owner, $member);

        $noPermRole = $group->roles()->create([
            'name' => 'Cuma baca',
            'permissions' => [],
            'created_by' => $owner->id,
        ]);
        $group->members()->where('user_id', $member->id)->update(['role_id' => $noPermRole->id]);

        $response = $this->postJson('/api/v1/sessions', [
            'name' => 'Gak boleh',
            'date' => '2026-09-21',
            'group_id' => $group->id,
            'member_ids' => [$owner->id],
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('nongkrong_sessions', ['name' => 'Gak boleh']);
    }

    public function test_split_equal_dan_debt_terbentuk(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $c = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b, 'c' => $c]);

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Makan rame',
            'amount' => 300_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id, $c->id],
        ])->assertCreated();

        $detail = $this->getJson("/api/v1/sessions/{$session->id}");

        $detail->assertOk()
            ->assertJsonPath('data.status', 'active')
            ->assertJsonCount(2, 'data.debts');

        $debts = collect($detail->json('data.debts'));
        $this->assertSame(100_000, $debts->where('from.id', $b->id)->where('to.id', $a->id)->first()['amount']);
        $this->assertSame(100_000, $debts->where('from.id', $c->id)->where('to.id', $a->id)->first()['amount']);
        $this->assertSame(200_000, $detail->json('data.summary.total_pending'));
    }

    public function test_split_equal_dengan_addon_pajak_service_discount(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b]);

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Makan berdua',
            'amount' => 200_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'percent',
            'discount_value' => 10,
            'service_rate' => 5,
            'tax_rate' => 11,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ])->assertCreated();

        // Grand total = 209.790 → masing 104.895 (dibagi rata, sisa utk pertama)
        $expense = $a->sessionsCreated()->find($session->id)->expenses()->first();
        $this->assertSame(209_790, $expense->grandTotal());

        $shares = $expense->splits()->pluck('share_amount', 'user_id')->all();
        $this->assertSame(209_790, array_sum($shares));
    }

    public function test_split_preview_tidak_mempersist_apa_apa(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b]);

        $this->apiAs($a);
        $response = $this->postJson("/api/v1/sessions/{$session->id}/split-preview", [
            'name' => 'Preview doang',
            'amount' => 100_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.grand_total', 100_000)
            ->assertJsonCount(2, 'data.splits');

        $this->assertDatabaseCount('expenses', 0);
        $this->assertDatabaseCount('expense_splits', 0);
    }

    public function test_settle_debt_oleh_debtor_saja(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $c = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b, 'c' => $c]);

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Makan rame',
            'amount' => 150_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id, $c->id],
        ]);

        $debtToB = $session->debts()->where('from_user_id', $b->id)->where('status', 'pending')->firstOrFail();

        // Orang lain (c) nggak boleh settle utang b
        $this->apiAs($c);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debtToB->id}/settle")
            ->assertForbidden();

        // Si b sendiri boleh
        $this->apiAs($b);
        $settle = $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debtToB->id}/settle");
        $settle->assertOk()->assertJsonPath('data.status', 'settled');

        $this->assertDatabaseHas('debts', ['id' => $debtToB->id, 'status' => 'settled', 'settled_by_user_id' => $b->id]);
    }

    public function test_semua_debt_settled_status_beres(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b]);

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Beres-beres',
            'amount' => 100_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ]);

        $debt = $session->debts()->where('status', 'pending')->firstOrFail();

        $this->apiAs($b);
        $this->postJson("/api/v1/sessions/{$session->id}/debts/{$debt->id}/settle");

        $this->getJson("/api/v1/sessions/{$session->id}")
            ->assertJsonPath('data.status', 'settled');
    }

    public function test_edit_expense_masuk_audit_log_dan_debt_berubah(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b]);

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Makan yakult',
            'amount' => 100_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ]);

        $expense = $session->expenses()->firstOrFail();

        $this->apiAs($a);
        $update = $this->patchJson("/api/v1/sessions/{$session->id}/expenses/{$expense->id}", [
            'name' => 'Makan enak',
            'amount' => 200_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ]);

        $update->assertOk()->assertJsonPath('data.name', 'Makan enak');

        // Audit log tercatat + before/after
        $log = ActivityLog::where('auditable_type', \App\Models\Expense::class)
            ->where('auditable_id', $expense->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($log);
        $this->assertSame(100_000, $log->properties['before']['amount']);
        $this->assertSame(200_000, $log->properties['after']['amount']);

        // Debt disesuaikan: sekarang b utang 100k ke a
        $this->getJson("/api/v1/sessions/{$session->id}")
            ->assertJsonPath('data.summary.total_pending', 100_000);
    }

    public function test_cancel_expense_soft_delete_dan_debt_diregen(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b]);

        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Bakso',
            'amount' => 100_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ]);

        $expense = $session->expenses()->firstOrFail();

        $this->apiAs($a);
        $this->deleteJson("/api/v1/sessions/{$session->id}/expenses/{$expense->id}")
            ->assertOk();

        // Soft delete → riwayat tetep ada
        $this->assertSoftDeleted('expenses', ['id' => $expense->id]);

        // Debt kehapus karena expense udah dibatalin → status draft lagi
        $this->getJson("/api/v1/sessions/{$session->id}")
            ->assertJsonPath('data.status', 'draft')
            ->assertJsonCount(0, 'data.debts');
    }

    public function test_validation_nominal_bulat_dan_split_custom(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = $this->createSession(['a' => $a, 'b' => $b]);

        // Uang harus integer: pecahan ditolak
        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Decimal',
            'amount' => 100.5,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ])->assertStatus(422);

        // Custom amount sum harus pas
        $this->apiAs($a);
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Custom gagal',
            'amount' => 100_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'custom',
            'participant_ids' => [$a->id, $b->id],
            'custom_amounts' => [$a->id => 30_000, $b->id => 40_000],
        ])->assertStatus(422);
    }

    public function test_non_member_tidak_bisa_akses_patungan(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $outsider = $this->apiAs(User::factory()->create());

        $session = $this->createSession(['a' => $a, 'b' => $b]);

        $this->apiAs($outsider);

        $this->getJson("/api/v1/sessions/{$session->id}")->assertForbidden();
        $this->postJson("/api/v1/sessions/{$session->id}/expenses", [
            'name' => 'Nembus',
            'amount' => 10_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ])->assertForbidden();
    }

    public function test_anggota_yang_bukan_member_group_tidak_bisa_dijadikan_peserta_patungan_group(): void
    {
        $owner = User::factory()->create();
        $creator = $this->apiAs(User::factory()->create());
        $stranger = User::factory()->create();

        $group = $this->createGroupWithMember($owner, $creator);
        $channel = $group->channels()->create(['name' => 'Jajan', 'created_by' => $owner->id]);

        // Orang luar (stranger) dicoba dimasukin ke patungan group → ditolak
        $this->postJson('/api/v1/sessions', [
            'name' => 'Sok kenal',
            'date' => '2026-09-21',
            'group_id' => $group->id,
            'channel_id' => $channel->id,
            'member_ids' => [$owner->id, $stranger->id],
        ])->assertStatus(422)->assertJsonPath('message', 'Ada peserta yang bukan member group. Ajakin gabung dulu yaa.');
    }

    private function createSession(array $membersByKey): \App\Models\NongkrongSession
    {
        $creator = $membersByKey['a'];
        $ids = array_map(fn ($user) => $user->id, array_diff_key($membersByKey, ['a' => true]));

        Sanctum::actingAs($creator);

        $response = $this->postJson('/api/v1/sessions', [
            'name' => 'Session #'.uniqid(),
            'date' => '2026-09-22',
            'member_ids' => array_values($ids),
        ]);

        $response->assertCreated();

        return \App\Models\NongkrongSession::findOrFail($response->json('data.id'));
    }

    private function createGroupWithMember(User $owner, User $member): Group
    {
        $group = \App\Services\GroupService::create($owner, ['name' => 'Group '.uniqid()]);
        $memberRole = $group->roles()->where('is_system', true)->where('name', 'Member')->firstOrFail();
        $group->members()->create(['user_id' => $member->id, 'role_id' => $memberRole->id]);

        return $group;
    }

    private function apiAs(User $user): User
    {
        Sanctum::actingAs($user);

        return $user;
    }
}
