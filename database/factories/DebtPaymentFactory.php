<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DebtPayment>
 */
class DebtPaymentFactory extends Factory
{
    protected $model = \App\Models\DebtPayment::class;

    public function definition(): array
    {
        return [
            'debt_id' => Debt::factory(),
            'amount' => $this->faker->numberBetween(5000, 200000),
            'method' => 'transfer',
            'status' => 'pending',
            'reported_by_user_id' => User::factory(),
        ];
    }
}
