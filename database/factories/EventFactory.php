<?php

namespace Database\Factories;

use App\Models\Camping;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'image' => fake()->imageUrl(),
            'place' => fake()->city(),
            'year' => (int) date('Y'),
            'start_date' => now()->addMonth(),
            'duration_days' => fake()->numberBetween(1, 10),
            'total_vacancies' => fake()->numberBetween(50, 500),
            'eventable_id' => Camping::factory(),
            'eventable_type' => Camping::class,
        ];
    }
}
