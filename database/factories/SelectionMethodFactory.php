<?php

namespace Database\Factories;

use App\Models\SelectionMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SelectionMethod>
 */
class SelectionMethodFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'method' => fake()->unique()->word(),
            'description' => fake()->sentence(),
        ];
    }
}
