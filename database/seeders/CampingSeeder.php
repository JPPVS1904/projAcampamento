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

        // Dates for an open camping
        $open_yesterday = Carbon::yesterday();
        $open_tomorrow = Carbon::tomorrow();
        $open_next_month = Carbon::now()->addMonth();

        // Dates for a closed camping
        $closed_start = Carbon::now()->subMonths(3);
        $closed_end = Carbon::now()->subMonths(3)->addDays(5);
        $closed_event_start = Carbon::now()->subMonths(2);

        foreach ($categories as $categoryData) {
            $category = \App\Models\Category::firstOrCreate([
                'name' => $categoryData['name'],
                'type' => 'Acampamento',
            ]);

            // Open Camping
            $openCamping = Camping::create([
                'notice' => 'Edital ' . $categoryData['name'],
                'term' => 'Termo ' . $categoryData['name'],
                'image' => 'default.jpg',
                'minimal_age' => $categoryData['minimal_age'],
                'maximal_age' => $categoryData['maximal_age'],
                'camper_fee' => 150.00,
                'servant_fee' => 100.00,
                'planned_man_vacancies' => 20,
                'planned_woman_vacancies' => 20,
                'planned_couple_vacancies' => 10,
                'raffle_man_vacancies' => 10,
                'raffle_woman_vacancies' => 10,
                'raffle_couple_vacancies' => 5,
                'raffle_total_vacancies' => 25,
                'raffle_camper_subscription_start_date' => $open_yesterday,
                'raffle_camper_subscription_end_date' => $open_tomorrow,
                'raffle_camper_date' => $open_tomorrow->copy()->addDays(2),
                'raffle_servant_subscription_start_date' => $open_yesterday,
                'raffle_servant_subscription_end_date' => $open_tomorrow,
                'raffle_servant_date' => $open_tomorrow->copy()->addDays(2),
                'camper_registration_start_date' => $open_yesterday,
                'camper_registration_end_date' => $open_tomorrow,
                'camper_payment_link' => $open_tomorrow->copy()->addDays(5),
                'camper_payment_date' => $open_tomorrow->copy()->addDays(5),
                'servant_registration_start_date' => $open_yesterday,
                'servant_registration_end_date' => $open_tomorrow,
                'servant_payment_link' => $open_tomorrow->copy()->addDays(5),
                'servant_payment_date' => $open_tomorrow->copy()->addDays(5),
            ]);

            $openCamping->activity()->create([
                'name' => $categoryData['name'] . ' - Aberto',
                'image' => 'default.jpg',
                'place' => 'Sítio Recanto',
                'year' => date('Y'),
                'start_date' => $open_next_month,
                'duration_days' => 4,
                'total_vacancies' => 100,
                'category_id' => $category->id,
            ]);

            // Closed Camping
            $closedCamping = Camping::create([
                'notice' => 'Edital ' . $categoryData['name'],
                'term' => 'Termo ' . $categoryData['name'],
                'image' => 'default.jpg',
                'minimal_age' => $categoryData['minimal_age'],
                'maximal_age' => $categoryData['maximal_age'],
                'camper_fee' => 150.00,
                'servant_fee' => 100.00,
                'planned_man_vacancies' => 20,
                'planned_woman_vacancies' => 20,
                'planned_couple_vacancies' => 10,
                'raffle_man_vacancies' => 10,
                'raffle_woman_vacancies' => 10,
                'raffle_couple_vacancies' => 5,
                'raffle_total_vacancies' => 25,
                'raffle_camper_subscription_start_date' => $closed_start,
                'raffle_camper_subscription_end_date' => $closed_end,
                'raffle_camper_date' => $closed_end->copy()->addDays(2),
                'raffle_servant_subscription_start_date' => $closed_start,
                'raffle_servant_subscription_end_date' => $closed_end,
                'raffle_servant_date' => $closed_end->copy()->addDays(2),
                'camper_registration_start_date' => $closed_start,
                'camper_registration_end_date' => $closed_end,
                'camper_payment_link' => $closed_end->copy()->addDays(5),
                'camper_payment_date' => $closed_end->copy()->addDays(5),
                'servant_registration_start_date' => $closed_start,
                'servant_registration_end_date' => $closed_end,
                'servant_payment_link' => $closed_end->copy()->addDays(5),
                'servant_payment_date' => $closed_end->copy()->addDays(5),
            ]);

            $closedCamping->activity()->create([
                'name' => $categoryData['name'] . ' - Encerrado',
                'image' => 'default.jpg',
                'place' => 'Sítio Recanto',
                'year' => date('Y', strtotime('-1 year')),
                'start_date' => $closed_event_start,
                'duration_days' => 4,
                'total_vacancies' => 100,
                'category_id' => $category->id,
            ]);
        }
    }
}
