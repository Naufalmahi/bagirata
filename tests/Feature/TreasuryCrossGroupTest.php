<?php

namespace Tests\Feature;

use App\Models\GroupMember;
use App\Models\User;
use App\Services\GroupService;
use App\Services\TreasuryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Entry treasury di-resolve dari {entry} secara global, jadi controller wajib
 * memastikan entry itu milik wallet group yang ada di URL. Kalau tidak, admin
 * group A bisa menyetujui transaksi kas group B.
 */
class TreasuryCrossGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_tidak_bisa_approve_entry_group_lain(): void
    {
        $f = $this->duaGroup();

        $this->actingAs($f['attacker'])
            ->post(route('groups.treasury.approve', [$f['attackerGroup'], $f['entry']]))
            ->assertNotFound();

        $this->assertSame('pending', $f['entry']->fresh()->status);
        $this->assertSame(0, $f['victimWallet']->fresh()->balance);
    }

    public function test_admin_tidak_bisa_reject_entry_group_lain(): void
    {
        $f = $this->duaGroup();

        $this->actingAs($f['attacker'])
            ->post(route('groups.treasury.reject', [$f['attackerGroup'], $f['entry']]))
            ->assertNotFound();

        $this->assertSame('pending', $f['entry']->fresh()->status);
        $this->assertSame(0, $f['victimWallet']->fresh()->balance);
    }

    public function test_admin_tetap_bisa_approve_entry_group_sendirinya(): void
    {
        $f = $this->duaGroup();

        $this->actingAs($f['victimOwner'])
            ->post(route('groups.treasury.approve', [$f['victimGroup'], $f['entry']]))
            ->assertRedirect();

        $entry = $f['entry']->fresh();
        $this->assertSame('approved', $entry->status);
        $this->assertSame($f['victimOwner']->id, $entry->approved_by);
        $this->assertSame(75_000, $f['victimWallet']->fresh()->balance);
    }

    public function test_admin_tetap_bisa_reject_entry_group_sendirinya(): void
    {
        $f = $this->duaGroup();

        $this->actingAs($f['victimOwner'])
            ->post(route('groups.treasury.reject', [$f['victimGroup'], $f['entry']]))
            ->assertRedirect();

        $this->assertSame('rejected', $f['entry']->fresh()->status);
        $this->assertSame(0, $f['victimWallet']->fresh()->balance);
    }

    public function test_policy_menolak_approve_entry_group_lain(): void
    {
        $f = $this->duaGroup();

        $this->assertFalse(
            Gate::forUser($f['attacker'])->allows('approveEntry', [$f['entry']])
        );
        $this->assertTrue(
            Gate::forUser($f['victimOwner'])->allows('approveEntry', [$f['entry']])
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function duaGroup(): array
    {
        $attacker = User::factory()->create();
        $victimOwner = User::factory()->create();
        $victimMember = User::factory()->create();

        $attackerGroup = GroupService::create($attacker, ['name' => 'Geng Attacker']);
        $victimGroup = GroupService::create($victimOwner, ['name' => 'Geng Victim']);

        GroupMember::create([
            'group_id' => $victimGroup->id,
            'user_id' => $victimMember->id,
            'role' => 'member',
        ]);

        $victimWallet = TreasuryService::getOrCreateWallet($victimGroup);

        // Anggota (bukan owner) => pending, saldo belum bergerak.
        $entry = TreasuryService::recordEntry($victimWallet, $victimMember, [
            'type' => 'in',
            'category' => 'iuran',
            'amount' => 75_000,
            'description' => 'Iuran bulan ini',
        ]);

        $this->assertSame('pending', $entry->status);
        $this->assertSame(0, $victimWallet->fresh()->balance);

        return [
            'attacker' => $attacker,
            'attackerGroup' => $attackerGroup,
            'victimOwner' => $victimOwner,
            'victimMember' => $victimMember,
            'victimGroup' => $victimGroup,
            'victimWallet' => $victimWallet,
            'entry' => $entry,
        ];
    }
}
