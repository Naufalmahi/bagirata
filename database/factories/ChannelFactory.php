<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Channel>
 */
class ChannelFactory extends Factory
{
    protected $model = \App\Models\Channel::class;

    public function definition(): array
    {
        return [
            'group_id' => Group::factory(),
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'sort_order' => 0,
            'created_by' => User::factory(),
        ];
    }
}
