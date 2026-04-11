<?php

namespace Database\Factories;

use App\Models\Festival;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Festival>
 */
class FestivalFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'minimal_age' => 16,
            'is_paid_festival' => fake()->boolean(),
            'ticket_price' => fake()->numberBetween(0, 100),
            'sale_start_date' => now(),
            'payment_link' => fake()->numberBetween(1, 9999),
        ];
    }
}
