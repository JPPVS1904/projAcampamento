<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        $sectors = [
            'Cozinha',
            'Limpeza',
            'Intercessão',
            'Música',
            'Animação',
            'Segurança',
            'Acolhida'
        ];

        $createdSectors = [];
        foreach ($sectors as $sector) {
            $createdSectors[] = Sector::create(['name' => $sector]);
        }

        // Attach sectors to all categories
        $categories = \App\Models\Category::all();
        foreach ($categories as $category) {
            foreach ($createdSectors as $sector) {
                $category->sectors()->attach($sector->id, [
                    'base_vacancies' => 10,
                    'raffle_vacancies' => 5
                ]);
            }
        }
    }
}
