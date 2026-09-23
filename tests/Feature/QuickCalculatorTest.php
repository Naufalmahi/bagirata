<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class QuickCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_calculate_split_equally()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/calculate-split', [
            'subtotal' => 100000,
            'discount_type' => 'fixed',
            'discount_value' => 10000,
            'service_rate' => 5,
            'tax_rate' => 10,
            'people_count' => 3,
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'subtotal',
                'discount_amount',
                'net_subtotal',
                'service_charge',
                'taxable_base',
                'tax',
                'grand_total',
                'split_type',
                'shares',
            ]
        ]);

        // Net: 90000
        // Service (5%): 4500
        // Taxable: 94500
        // Tax (10%): 9450
        // Grand: 90000 + 4500 + 9450 = 103950
        $this->assertEquals(103950, $response->json('data.grand_total'));
        $this->assertCount(3, $response->json('data.shares'));
        $this->assertEquals(103950, array_sum($response->json('data.shares')));
    }
}
