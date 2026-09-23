<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\User;
use App\Services\GroupService;
use App\Services\TreasuryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TreasuryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_group_treasury()
    {
        $user = User::factory()->create();
        $group = GroupService::create($user, ['name' => 'Geng Kopi']);

        $response = $this->actingAs($user)->get(route('groups.treasury.show', $group));

        $response->assertOk();
        $response->assertSee('Kas Grup: Geng Kopi');
        $response->assertSee('Recorded Balance');
    }

    public function test_cash_in_increases_balance_when_approved()
    {
        $user = User::factory()->create();
        $group = GroupService::create($user, ['name' => 'Geng Kopi']);
        $wallet = TreasuryService::getOrCreateWallet($group);

        $response = $this->actingAs($user)->post(route('groups.treasury.store', $group), [
            'type' => 'in',
            'category' => 'iuran',
            'amount' => 50000,
            'description' => 'Iuran pertama',
        ]);

        $response->assertRedirect();
        $this->assertEquals(50000, $wallet->fresh()->balance);
        $this->assertDatabaseHas('wallet_entries', [
            'amount' => 50000,
            'type' => 'in',
            'status' => 'approved',
        ]);
    }

    public function test_cash_out_decreases_balance_when_approved()
    {
        $user = User::factory()->create();
        $group = GroupService::create($user, ['name' => 'Geng Kopi']);
        $wallet = TreasuryService::getOrCreateWallet($group);
        $wallet->update(['balance' => 100000]);

        $response = $this->actingAs($user)->post(route('groups.treasury.store', $group), [
            'type' => 'out',
            'category' => 'konsumsi',
            'amount' => 40000,
            'description' => 'Beli gorengan',
        ]);

        $response->assertRedirect();
        $this->assertEquals(60000, $wallet->fresh()->balance);
    }
}
