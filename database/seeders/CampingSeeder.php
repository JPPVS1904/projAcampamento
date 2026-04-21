<?php

namespace Database\Seeders;

use App\Models\Camping;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CampingSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Acampamento Mirim', 'minimal_age' => 10, 'maximal_age' => 12],
            ['name' => 'Acampamento Juvenil', 'minimal_age' => 13, 'maximal_age' => 17],
            ['name' => 'FAC', 'minimal_age' => 14, 'maximal_age' => 17],
            ['name' => 'Acampamento Sênior', 'minimal_age' => 18, 'maximal_age' => 100],
            ['name' => 'Acampamento Casais', 'minimal_age' => 18, 'maximal_age' => 100],
        ];

        $yesterday = Carbon::yesterday();
        $tomorrow = Carbon::tomorrow();

        foreach ($categories as $category) {
            $camping = Camping::create([
                'notice' => 'Edital ' . $category['name'],
                'term' => 'Termo ' . $category['name'],
                'image' => 'default.jpg',
                'minimal_age' => $category['minimal_age'],
                'maximal_age' => $category['maximal_age'],
                'camper_fee' => 150.00,
                'servant_fee' => 100.00,
                'planned_man_vacancies' => 20,
                'planned_woman_vacancies' => 20,
                'planned_couple_vacancies' => 0,
                'raffle_man_vacancies' => 10,
                'raffle_woman_vacancies' => 10,
                'raffle_couple_vacancies' => 0,
                'raffle_total_vacancies' => 20,
                'raffle_camper_subscription_start_date' => $yesterday,
                'raffle_camper_subscription_end_date' => $tomorrow,
                'raffle_camper_date' => $tomorrow->copy()->addDays(2),
                'raffle_servant_subscription_start_date' => $yesterday,
                'raffle_servant_subscription_end_date' => $tomorrow,
                'raffle_servant_date' => $tomorrow->copy()->addDays(2),
                'camper_registration_start_date' => $yesterday,
                'camper_registration_end_date' => $tomorrow,
                'camper_payment_link' => $tomorrow->copy()->addDays(5),
                'camper_payment_date' => $tomorrow->copy()->addDays(5),
                'servant_registration_start_date' => $yesterday,
                'servant_registration_end_date' => $tomorrow,
                'servant_payment_link' => $tomorrow->copy()->addDays(5),
                'servant_payment_date' => $tomorrow->copy()->addDays(5),
            ]);

            $camping->event()->create([
                'name' => $category['name'],
                'image' => 'default.jpg',
                'place' => 'Sítio Recanto',
                'year' => date('Y'),
                'start_date' => Carbon::now()->addMonth(),
                'duration_days' => 4,
                'total_vacancies' => 100,
            ]);
        }
    }
}
