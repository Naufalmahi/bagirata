<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NongkrongSession>
 */
class NongkrongSessionFactory extends Factory
{
    protected $model = \App\Models\NongkrongSession::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->unique()->words(3, true),
            'description' => $this->faker->sentence(),
            'date' => $this->faker->date(),
        ];
    }

    public function withMembers(int $count): static
    {
        return $this->afterCreating(function ($session) use ($count) {
            $members = User::factory()->count($count)->create()->pluck('id')->all();
            $session->members()->sync([...$session->user_id, ...$members]);
        });
    }
}
