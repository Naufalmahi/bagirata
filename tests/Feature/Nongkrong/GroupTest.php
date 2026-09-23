<?php

namespace Tests\Feature\Nongkrong;

use App\Enums\Permission;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    private function apiAs(User $user): User
    {
        Sanctum::actingAs($user);

        return $user;
    }

    public function test_owner_bisa_buat_group_dan_dapet_invite_link(): void
    {
        $owner = $this->apiAs(User::factory()->create());

        $response = $this->postJson('/api/v1/groups', [
            'name' => 'Geng Nongkrong',
            'description' => 'Jadi bebas di sini.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Geng Nongkrong');

        $this->assertDatabaseHas('groups', ['name' => 'Geng Nongkrong', 'user_id' => $owner->id]);
        $this->assertSame(Permission::all(), $response->json('data.my_permissions'));

        // Role default: Owner (full) + Member (create_patungan)
        $group = Group::where('name', 'Geng Nongkrong')->firstOrFail();
        $this->assertCount(2, $group->roles);
        $this->assertTrue($group->activeInvite()->exists());
        $this->assertCount(1, $group->roles()->where('is_system', true)->where('name', 'Owner')->get());

        $memberRole = $group->roles()->where('name', 'Member')->firstOrFail();
        $this->assertSame([Permission::CREATE_PATUNGAN->value], $memberRole->permissions);
    }

    public function test_member_bisa_join_via_invite_link_dengan_role_default(): void
    {
        $owner = User::factory()->create();
        $invitee = $this->apiAs(User::factory()->create());

        $group = \App\Services\GroupService::create($owner, ['name' => 'Geng Rawon']);
        $invite = $group->activeInvite;

        $response = $this->postJson("/api/v1/groups/join/{$invite->token}");

        $response->assertOk()
            ->assertJsonPath('data.is_owner', false);

        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $invitee->id,
        ]);

        $member = GroupMember::where('group_id', $group->id)->where('user_id', $invitee->id)->firstOrFail();
        $this->assertSame('Member', $member->role->name);

        $this->assertDatabaseHas('group_invites', ['id' => $invite->id, 'used_count' => 1]);
    }

    public function test_join_link_sudah_revoked_gagal(): void
    {
        $owner = User::factory()->create();
        $invitee = $this->apiAs(User::factory()->create());

        $group = Group::factory()->for($owner, 'owner')->create();
        $invite = $group->invites()->create([
            'token' => 'kode-revoked',
            'created_by' => $owner->id,
        ]);
        $invite->revoke($owner->id);

        $response = $this->postJson("/api/v1/groups/join/{$invite->token}");

        $response->assertStatus(410);
        $this->assertDatabaseMissing('group_members', ['group_id' => $group->id, 'user_id' => $invitee->id]);
    }

    public function test_orang_luar_tidak_bisa_melihat_group(): void
    {
        $owner = User::factory()->create();
        $outsider = $this->apiAs(User::factory()->create());

        $group = Group::factory()->for($owner, 'owner')->create();

        $this->getJson("/api/v1/groups/{$group->id}")->assertForbidden();

        $this->getJson('/api/v1/groups')->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_owner_bisa_bikin_role_custom_dan_assign_ke_member(): void
    {
        $owner = $this->apiAs(User::factory()->create());
        $group = GroupServiceTestHelper::createWithMember($owner);
        $member = $group->members()->where('user_id', '!=', $owner->id)->firstOrFail()->user;

        $roleResponse = $this->postJson("/api/v1/groups/{$group->id}/roles", [
            'name' => 'Bendahara',
            'permissions' => [Permission::INVITE_MEMBERS->value, Permission::CREATE_CHANNEL->value],
        ]);

        $roleResponse->assertCreated()->assertJsonPath('data.name', 'Bendahara');

        $roleId = $roleResponse->json('data.id');

        $assign = $this->patchJson("/api/v1/groups/{$group->id}/members/{$member->id}/role", [
            'user_id' => $member->id,
            'role_id' => $roleId,
        ]);

        $assign->assertOk();
        $this->assertDatabaseHas('group_members', [
            'group_id' => $group->id,
            'user_id' => $member->id,
            'role_id' => $roleId,
        ]);
    }

    public function test_non_owner_tidak_bisa_kelola_role(): void
    {
        $owner = User::factory()->create();
        $member = $this->apiAs(User::factory()->create());

        $group = GroupServiceTestHelper::createWithMember($owner, $member);

        $this->postJson("/api/v1/groups/{$group->id}/roles", [
            'name' => 'Ntah',
            'permissions' => [],
        ])->assertForbidden();

        $this->patchJson("/api/v1/groups/{$group->id}/members/{$owner->id}/role", [
            'user_id' => $owner->id,
            'role_id' => $group->roles()->first()->id,
        ])->assertForbidden();
    }

    public function test_member_tanpa_permission_tidak_bisa_bikin_channel(): void
    {
        $owner = User::factory()->create();
        $member = $this->apiAs(User::factory()->create());

        $group = GroupServiceTestHelper::createWithMember($owner, $member);

        $this->postJson("/api/v1/groups/{$group->id}/channels", ['name' => 'Foodie'])
            ->assertForbidden();
    }

    public function test_member_yang_dikasih_permission_bisa_bikin_channel(): void
    {
        $owner = $this->apiAs(User::factory()->create());
        $group = GroupServiceTestHelper::createWithMember($owner);
        $member = $group->members()->where('user_id', '!=', $owner->id)->firstOrFail()->user;

        $customRole = $group->roles()->create([
            'name' => 'Pengurus Channel',
            'permissions' => [Permission::CREATE_CHANNEL->value],
            'created_by' => $owner->id,
        ]);
        $group->members()->where('user_id', $member->id)->update(['role_id' => $customRole->id]);

        Sanctum::actingAs($member);

        $response = $this->postJson("/api/v1/groups/{$group->id}/channels", [
            'name' => 'Makan Bareng',
            'description' => 'Warkop tiap Jumat',
        ]);

        $response->assertCreated()->assertJsonPath('data.name', 'Makan Bareng');
        $this->assertDatabaseHas('channels', ['group_id' => $group->id, 'name' => 'Makan Bareng']);
    }

    public function test_hapus_channel_pindahkan_patungan_ke_group_level(): void
    {
        $owner = $this->apiAs(User::factory()->create());
        $group = GroupServiceTestHelper::createWithMember($owner);

        $channel = $group->channels()->create([
            'name' => 'Kedai Kopi',
            'created_by' => $owner->id,
        ]);

        $session = \App\Models\NongkrongSession::factory()->create(['group_id' => $group->id, 'channel_id' => $channel->id]);

        $this->deleteJson("/api/v1/groups/{$group->id}/channels/{$channel->id}")->assertOk();

        $this->assertSoftDeleted('channels', ['id' => $channel->id]);
        $this->assertDatabaseHas('nongkrong_sessions', ['id' => $session->id, 'channel_id' => null]);
    }

    public function test_invite_link_bisa_di_generate_ulang_dan_yang_lama_ke_revoke(): void
    {
        $owner = $this->apiAs(User::factory()->create());
        $group = Group::factory()->for($owner, 'owner')->create();
        $oldInvite = $group->invites()->create(['token' => 'invite-lama', 'created_by' => $owner->id]);

        $response = $this->postJson("/api/v1/groups/{$group->id}/invites");

        $response->assertCreated();
        $newToken = $response->json('data.token');
        $this->assertNotSame('invite-lama', $newToken);

        $this->assertFalse($oldInvite->refresh()->active);
        $this->assertTrue($group->invites()->where('token', $newToken)->where('active', true)->exists());
    }

    public function test_invite_bisa_diatur_masa_berlaku_dan_batas_pemakaian(): void
    {
        $owner = $this->apiAs(User::factory()->create());
        $group = Group::factory()->for($owner, 'owner')->create();

        $response = $this->postJson("/api/v1/groups/{$group->id}/invites", [
            'expires_days' => 3,
            'max_uses' => 5,
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.usable', true)
            ->assertJsonPath('data.max_uses', 5)
            ->assertJsonPath('data.uses_label', '0/5 dipakai');

        $this->assertNotNull($response->json('data.expires_at'));
        $this->assertNotNull($response->json('data.url'));

        $invite = $group->activeInvite;
        $this->assertSame(5, $invite->max_uses);
        $this->assertNotNull($invite->expires_at);
        $this->assertTrue($invite->expires_at->gt(now()));
        $this->assertTrue($invite->usable());

        $this->postJson('/api/v1/groups/'.$group->id.'/invites', ['expires_days' => 0])->assertUnprocessable();
    }

    public function test_invite_masih_bisa_dipakai_sampai_batas_kuota(): void
    {
        $owner = User::factory()->create();
        $group = Group::factory()->for($owner, 'owner')->create();

        $invite = \App\Services\GroupService::makeInvite($group, $owner->id, ['max_uses' => 2]);
        $this->assertTrue($invite->usable());

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Sanctum::actingAs($userA);
        $this->postJson("/api/v1/groups/join/{$invite->token}")->assertOk();
        $this->assertTrue($invite->refresh()->usable());

        Sanctum::actingAs($userB);
        $this->postJson("/api/v1/groups/join/{$invite->token}")->assertOk();

        $this->assertFalse($invite->refresh()->usable());
        $this->assertSame(2, $invite->used_count);
        $this->assertTrue($invite->usageLimitReached());

        // Kuota abis → siapapun yang nyoba join kena 410
        $userC = User::factory()->create();
        Sanctum::actingAs($userC);
        $this->postJson("/api/v1/groups/join/{$invite->token}")->assertStatus(410);

        $this->assertDatabaseMissing('group_members', ['group_id' => $group->id, 'user_id' => $userC->id]);
    }

    public function test_invite_expired_gagal_dipake_join(): void
    {
        $owner = User::factory()->create();
        $invitee = $this->apiAs(User::factory()->create());

        $group = Group::factory()->for($owner, 'owner')->create();
        $invite = \App\Services\GroupService::makeInvite($group, $owner->id, ['expires_days' => 7]);
        $invite->update(['expires_at' => now()->subHour()]);

        $this->assertTrue($invite->isExpired());
        $this->assertFalse($invite->usable());

        $this->postJson("/api/v1/groups/join/{$invite->token}")->assertStatus(410);
        $this->assertDatabaseMissing('group_members', ['group_id' => $group->id, 'user_id' => $invitee->id]);
    }

    public function test_invite_revoke_dan_generate_dengan_opsi_punya_riwayat_lengkap(): void
    {
        $owner = $this->apiAs(User::factory()->create());
        $group = GroupServiceTestHelper::createWithMember($owner);

        $response = $this->postJson("/api/v1/groups/{$group->id}/invites", ['max_uses' => 2]);
        $invite = $group->activeInvite;
        $this->assertTrue($invite->usable());
        $this->assertNotNull($response->json('data.url'));

        $this->postJson("/api/v1/groups/{$group->id}/invites/{$invite->id}/revoke")->assertOk();
        $this->assertFalse($invite->refresh()->active);

        // Setelah revoke, getInvite balikin null (nggak ada link aktif lagi buat dishare)
        $get = $this->getJson("/api/v1/groups/{$group->id}/invite");
        $get->assertOk()->assertJsonPath('data', null);
    }
}

class GroupServiceTestHelper
{
    /**
     * Bikin group milik $owner + otomatis tambah 1 member lain (pakai GroupService biar seed role & invite berlaku).
     */
    public static function createWithMember(User $owner, ?User $member = null): Group
    {
        if (! $member) {
            $member = User::factory()->create();
        }

        $group = \App\Services\GroupService::create($owner, [
            'name' => 'Group '.$owner->id.'-'.$member->id,
        ]);

        $memberRole = $group->roles()->where('is_system', true)->where('name', 'Member')->firstOrFail();
        $group->members()->create(['user_id' => $member->id, 'role_id' => $memberRole->id]);

        return $group;
    }
}
