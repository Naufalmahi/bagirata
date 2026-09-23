<?php

namespace Database\Factories;

use App\Enums\ExpenseCategory;
use App\Models\NongkrongSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = \App\Models\Expense::class;

    public function definition(): array
    {
        return [
            'nongkrong_session_id' => NongkrongSession::factory(),
            'name' => $this->faker->words(2, true),
            'amount' => $this->faker->numberBetween(10000, 500000),
            'paid_by_user_id' => User::factory(),
            'category' => ExpenseCategory::MAKAN->value,
            'note' => $this->faker->sentence(),
            'discount_type' => 'fixed',
            'discount_value' => 0,
            'service_rate' => 0,
            'tax_rate' => 0,
            'created_by' => User::factory(),
        ];
    }
}
