<?php

namespace Database\Factories;

use App\Models\MaritalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaritalStatus>
 */
class MaritalStatusFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->word(),
        ];
    }
}
