<?php

namespace Database\Factories;

use App\Enums\SubscriptionType;
use App\Models\Event;
use App\Models\SelectionMethod;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subscription_date' => now(),
            'subscription_type' => fake()->randomElement(SubscriptionType::cases()),
            'was_selected' => fake()->boolean(),
            'substitute_position' => fake()->numberBetween(0, 100),
            'paid_the_fee' => fake()->boolean(),
            'is_quitter' => false,
            'payment_code' => fake()->bothify('PAY-########'),
            'qrcode_data' => fake()->uuid(),
            'used_qrcode' => false,
            'selection_method_id' => SelectionMethod::factory(),
            'user_id' => User::factory(),
            'spouse_id' => null,
            'event_id' => Event::factory(),
            'sector_id' => null,
        ];
    }
}
