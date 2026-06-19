<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use App\Models\SelectionMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {

        MaritalStatus::insert([
            ['title' => 'Solteiro'],
            ['title' => 'Casado'],
            ['title' => 'Namorando'],
            ['title' => 'União estável'],
            ['title' => 'Viúvo'],
            ['title' => 'Divorciado'],
        ]);

        SelectionMethod::insert([
            [
                'method' => 'Sorteio',
                'description' => 'Selecionado por meio de sorteio',
            ],
            [
                'method' => 'Indicação',
                'description' => 'Indicado por um conselheiro',
            ],
            [
                'method' => 'Sorteio de Remanescentes',
                'description' => 'Selecionado por meio de sorteio',
            ],
        ]);

        $this->call([
            // CampingSeeder::class,
            UserSeeder::class,
        ]);
    }
}
