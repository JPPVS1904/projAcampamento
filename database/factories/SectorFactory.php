<?php

namespace Database\Factories;

use App\Enums\Place;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sector>
 */
class SectorFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->company(),
            'place' => fake()->randomElement(Place::cases()),
            'base_vacancies' => fake()->numberBetween(1, 50),
            'raffle_vacancies' => fake()->numberBetween(1, 50),
        ];
    }
}
