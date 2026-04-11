<?php

namespace Database\Factories;

use App\Models\Camping;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Camping>
 */
class CampingFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = now();

        return [
            'notice' => fake()->sentence(),
            'term' => fake()->sentence(),
            'image' => fake()->imageUrl(),
            'minimal_age' => 18,
            'maximal_age' => 35,
            'camper_fee' => fake()->randomFloat(2, 50, 500),
            'servant_fee' => fake()->randomFloat(2, 50, 500),
            'planned_man_vacancies' => 50,
            'planned_woman_vacancies' => 50,
            'planned_couple_vacancies' => 20,
            'raffle_man_vacancies' => 30,
            'raffle_woman_vacancies' => 30,
            'raffle_couple_vacancies' => 10,
            'raffle_total_vacancies' => 70,
            'raffle_camper_subscription_start_date' => $now,
            'raffle_camper_subscription_end_date' => $now->copy()->addDays(7),
            'raffle_camper_date' => $now->copy()->addDays(8),
            'raffle_servant_subscription_start_date' => $now,
            'raffle_servant_subscription_end_date' => $now->copy()->addDays(7),
            'raffle_servant_date' => $now->copy()->addDays(8),
            'camper_registration_start_date' => $now,
            'camper_registration_end_date' => $now->copy()->addDays(15),
            'camper_payment_link' => $now,
            'camper_payment_date' => $now->copy()->addDays(20),
            'servant_registration_start_date' => $now,
            'servant_registration_end_date' => $now->copy()->addDays(15),
            'servant_payment_link' => $now,
            'servant_payment_date' => $now->copy()->addDays(20),
        ];
    }
}
