<?php

namespace Tests\Feature\Nongkrong;

use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use App\Services\SessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_jugok_ke_landing(): void
    {
        $this->get('/')->assertOk()->assertSee('BagiRata');
    }

    public function test_dashboard_nerima_login(): void
    {
        $this->get('/dashboard')->assertRedirect('login');
        $this->get('/login')->assertOk();
    }

    public function test_register_langsung_masuk_dashboard(): void
    {
        $response = $this->post('/register', [
            'name' => 'Udin Petot',
            'email' => 'udin@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'udin@example.com']);
        $this->assertAuthenticated();
    }

    public function test_login_flow(): void
    {
        $user = User::factory()->create(['password' => bcrypt('rahasia123')]);

        $this->post('/login', ['email' => $user->email, 'password' => 'rahasia123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_bikin_group_dari_web(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/groups', [
            'name' => 'Geng Baus',
            'description' => 'ngumpul tiap jumat',
        ]);

        $group = Group::where('name', 'Geng Baus')->firstOrFail();

        $response->assertRedirect(route('groups.show', $group));
        $this->assertNotNull($group->activeInvite);
        $this->assertDatabaseHas('group_roles', ['group_id' => $group->id, 'name' => 'Owner']);
        $this->assertDatabaseHas('group_roles', ['group_id' => $group->id, 'name' => 'Member']);
    }

    public function test_kelompok_semuanya_pas_view_yang_ada(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $group = GroupService::create($owner, ['name' => 'Geng Warung']);

        $memberRole = $group->roles()->where('is_system', true)->where('name', 'Member')->firstOrFail();
        $group->members()->create(['user_id' => $member->id, 'role_id' => $memberRole->id]);

        // Owner: index, show, manage
        $this->actingAs($owner)->get('/groups')->assertOk()->assertSee('Geng Warung');
        $this->actingAs($owner)->get(route('groups.show', $group))->assertOk()->assertSee(route('join.show', $group->activeInvite->token));
        $this->actingAs($owner)->get(route('groups.manage', $group))->assertOk()->assertSee('Role + permission');

        // Member: show keliatan, manage nggak
        $this->actingAs($member)->get(route('groups.show', $group))->assertOk();
        $this->actingAs($member)->get(route('groups.manage', $group))->assertForbidden();

        // Patungan + expense page + preview
        $session = SessionService::create($owner, [
            'name' => 'Bakso Mantap',
            'date' => now()->format('Y-m-d'),
            'group_id' => $group->id,
            'member_ids' => [$member->id],
        ]);

        $this->actingAs($owner)->get(route('nongkrong.show', $session))->assertOk()->assertSee('Bakso Mantap');
        $this->actingAs($owner)->get(route('expenses.create', $session))->assertOk()->assertSee('Catat pengeluaran');

        $this->actingAs($owner)->postJson(route('expenses.preview', $session), [
            'name' => 'Preview',
            'amount' => 100_000,
            'paid_by_user_id' => $owner->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$owner->id, $member->id],
        ])->assertOk()->assertJsonPath('data.grand_total', 100_000);

        // Catat expense lewat form web → redirect + page show pending
        $add = $this->actingAs($owner)->post(route('expenses.store', $session), [
            'name' => 'Dua mangkok',
            'amount' => 50_000,
            'paid_by_user_id' => $owner->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$owner->id, $member->id],
        ]);

        $add->assertRedirect(route('nongkrong.show', $session));

        $this->actingAs($owner)->get(route('nongkrong.show', $session))
            ->assertOk()
            ->assertSee('Utang-piutang')
            ->assertSee('Dua mangkok');
    }

    public function test_join_via_link_buat_member_baru(): void
    {
        $owner = User::factory()->create();
        $group = GroupService::create($owner, ['name' => 'Geng Soto']);
        $token = $group->activeInvite->token;

        $this->get(route('join.show', $token))->assertOk()->assertSee('Geng Soto');

        $newbie = User::factory()->create();
        $this->actingAs($newbie)->post(route('join.store', $token))
            ->assertRedirect(route('groups.show', $group));

        $this->assertDatabaseHas('group_members', ['group_id' => $group->id, 'user_id' => $newbie->id]);
    }

    public function test_halaman_join_menampilkan_state_sesuai_kondisi(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $group = GroupService::create($owner, ['name' => 'Geng Pecel']);
        $token = $group->activeInvite->token;

        $memberRole = $group->roles()->where('is_system', true)->where('name', 'Member')->firstOrFail();
        $group->members()->create(['user_id' => $member->id, 'role_id' => $memberRole->id]);

        // Guest: lihat info group, belum ada tombol gabung (login/daftar)
        $this->get(route('join.show', $token))
            ->assertOk()
            ->assertSee('Geng Pecel')
            ->assertSee('orang udah di dalam')
            ->assertSee('Daftar');

        // Member: tombol gabung, message "udah jadi member"
        $this->actingAs($member)->get(route('join.show', $token))
            ->assertOk()
            ->assertSee('udah jadi member group ini');
    }

    public function test_join_link_expired_di_web_nampilin_alasan_spesifik(): void
    {
        $owner = User::factory()->create();
        $group = GroupService::create($owner, ['name' => 'Geng Expired']);
        $invite = $group->activeInvite;
        $invite->update(['expires_at' => now()->subDay()]);
        $token = $invite->token;

        $this->get(route('join.show', $token))
            ->assertOk()
            ->assertSee('kedaluwarsa')
            ->assertDontSee('Gabung group ini');
    }

    public function test_generate_invite_via_web_bisa_atur_masa_berlaku_dan_kuota(): void
    {
        $owner = User::factory()->create();
        $group = GroupService::create($owner, ['name' => 'Geng Limit']);

        $this->actingAs($owner)->post(route('groups.invite.generate', $group), [
            'expires_days' => 7,
            'max_uses' => 4,
        ])->assertRedirect();

        $invite = $group->activeInvite;
        $this->assertSame(4, $invite->max_uses);
        $this->assertTrue($invite->expires_at->gt(now()->addDays(6)));
        $this->assertTrue($invite->expires_at->lt(now()->addDays(8)));

        // Opsi invalid kena validation
        $this->actingAs($owner)->post(route('groups.invite.generate', $group), [
            'expires_days' => 0,
        ])->assertSessionHasErrors('expires_days');
    }

    public function test_login_dari_halaman_invite_balik_lagi_ke_invite(): void
    {
        $owner = User::factory()->create();
        $group = GroupService::create($owner, ['name' => 'Geng Balik']);
        $token = $group->activeInvite->token;
        $joinPath = '/join/'.$token;

        // Guest buka halaman invite → link login/daftar bawa redirect balik
        $this->get($joinPath)
            ->assertOk()
            ->assertSee('redirect=')
            ->assertSee('Login atau daftar');

        // Login dari join page → balik ke halaman invite (bukan dashboard)
        $newbie = User::factory()->create(['password' => bcrypt('rahasia123')]);
        $this->post('/login', [
            'email' => $newbie->email,
            'password' => 'rahasia123',
            'redirect' => $joinPath,
        ])->assertRedirect($joinPath);
    }

    public function test_register_dari_invite_balik_lagi_ke_invite(): void
    {
        $owner = User::factory()->create();
        $group = GroupService::create($owner, ['name' => 'Geng Balik Dua']);
        $joinPath = '/join/'.$group->activeInvite->token;

        // Register (guest) dengan redirect → balik ke halaman invite
        $this->post('/register', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'password' => 'rahasia123',
            'password_confirmation' => 'rahasia123',
            'redirect' => $joinPath,
        ])->assertRedirect($joinPath);

        $this->assertAuthenticated();
    }

<<<<<<< HEAD
    public function test_lapor_dan_konfirmasi_debt_lewat_web(): void
=======
    public function test_settle_debt_lewat_web(): void
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = SessionService::create($a, [
            'name' => 'Hitung',
            'date' => now()->format('Y-m-d'),
            'member_ids' => [$b->id],
        ]);

        $this->actingAs($a)->post(route('expenses.store', $session), [
            'name' => 'Nasi rame',
            'amount' => 40_000,
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

<<<<<<< HEAD
        $this->actingAs($b)->post(route('debts.payments.store', [$session, $debt]), [
            'amount' => 20_000,
            'method' => 'cash',
        ])->assertRedirect();

        $payment = $debt->payments()->firstOrFail();

        $this->actingAs($a)->post(route('debts.payments.confirm', [$session, $debt, $payment]), [
            'decision' => 'confirmed',
        ])->assertRedirect();

        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'settled']);
    }

    public function test_debt_section_di_halaman_session(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $session = SessionService::create($a, [
            'name' => 'Hitung',
            'date' => now()->format('Y-m-d'),
            'member_ids' => [$b->id],
        ]);

        $this->actingAs($a)->post(route('expenses.store', $session), [
            'name' => 'Nasi rame',
            'amount' => 40_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ]);

        $this->actingAs($b)->get(route('nongkrong.show', $session))
            ->assertOk()
            ->assertSee('Utang-piutang')
            ->assertSee(route('debts.index', $session));

        $this->actingAs($b)->get(route('debts.index', $session))
            ->assertOk()
            ->assertSee('Kamu harus bayar');

        $this->actingAs($b)->get(route('debts.show', [$session, $session->debts()->first()]))
            ->assertOk()
            ->assertSee('Udah bayar');
    }

    public function test_halaman_overview_menampilkan_utang_semua_session(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $c = User::factory()->create();
        $outsider = User::factory()->create();

        // Session 1: A bayar 40k equal 2 orang → B utang 20k ke A
        $satu = SessionService::create($a, [
            'name' => 'Makan Rawon',
            'date' => now()->format('Y-m-d'),
            'member_ids' => [$b->id],
        ]);
        $this->actingAs($a)->post(route('expenses.store', $satu), [
            'name' => 'Rawon rame',
            'amount' => 40_000,
            'paid_by_user_id' => $a->id,
            'category' => 'makan',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$a->id, $b->id],
        ]);

        // Session 2: C bayar 30k equal 2 orang → A utang 15k ke C
        $dua = SessionService::create($c, [
            'name' => 'Ngopi Dulu',
            'date' => now()->format('Y-m-d'),
            'member_ids' => [$a->id],
        ]);
        $this->actingAs($c)->post(route('expenses.store', $dua), [
            'name' => 'Kopi susu',
            'amount' => 30_000,
            'paid_by_user_id' => $c->id,
            'category' => 'minum',
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'split_type' => 'equal',
            'participant_ids' => [$c->id, $a->id],
        ]);

        // A: terima 20k dari session 1, bayar 15k di session 2 → dua-duanya keliatan
        $this->actingAs($a)->get(route('debts.overview'))
            ->assertOk()
            ->assertSee('Kamu harus bayar')
            ->assertSee('20.000', false)
            ->assertSee('15.000', false)
            ->assertSee('Makan Rawon')
            ->assertSee('Ngopi Dulu');

        // Outsider (nggak di session mana pun) → kosong
        $this->actingAs($outsider)->get(route('debts.overview'))
            ->assertOk()
            ->assertSee('Nggak ada utang aktif')
            ->assertDontSee('Makan Rawon')
            ->assertDontSee('Ngopi Dulu');
    }
=======
        $this->actingAs($b)->post(route('debts.settle', [$session, $debt]))
            ->assertRedirect();

        $this->assertDatabaseHas('debts', ['id' => $debt->id, 'status' => 'settled']);
    }
>>>>>>> 6561da739345e3ff0fdab546ef4f928a872f067a
}
