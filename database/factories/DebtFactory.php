<?php

namespace Database\Factories;

use App\Models\NongkrongSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    protected $model = \App\Models\Debt::class;

    public function definition(): array
    {
        return [
            'nongkrong_session_id' => NongkrongSession::factory(),
            'from_user_id' => User::factory(),
            'to_user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(5000, 200000),
            'status' => 'pending',
        ];
    }
}
