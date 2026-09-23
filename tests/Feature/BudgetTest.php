<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_budget()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/budgets', [
            'category' => 'nongkrong',
            'amount' => 500000,
            'period_type' => 'monthly',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'category' => 'nongkrong',
            'amount' => 500000,
        ]);
    }

    public function test_budget_summary_and_alerts()
    {
        $user = User::factory()->create();
        $budget = Budget::create([
            'user_id' => $user->id,
            'category' => 'nongkrong',
            'amount' => 100000,
            'period_start' => Carbon::now()->startOfMonth(),
            'period_end' => Carbon::now()->endOfMonth(),
        ]);

        $summary = \App\Services\BudgetService::summary($budget);

        $this->assertEquals(0, $summary['spent']);
        $this->assertEquals(0, $summary['utilization']);
        $this->assertNull($summary['alert']);
    }
}
