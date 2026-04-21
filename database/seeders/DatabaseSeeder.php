<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use App\Models\Role;
use App\Models\SelectionMethod;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::insert([
            ['title' => 'Campista'],
            ['title' => 'Coordenador geral'],
            ['title' => 'Coordenador de equipe'],
        ]);

        MaritalStatus::insert([
            ['title' => 'Solteiro'],
            ['title' => 'Namorando'],
            ['title' => 'União estável'],
            ['title' => 'Casado'],
            ['title' => 'Viúvo'],
            ['title' => 'Separado'],
            ['title' => 'Divorciado'],
            ['title' => 'Segunda união'],
        ]);

        SelectionMethod::insert([
            [
                'method' => 'Sorteio',
                'description' => 'Selecionado por meio de sorteio',
            ],
            [
                'method' => 'Forania',
                'description' => 'Indicado pelo páraco',
            ],
            [
                'method' => 'Conselho',
                'description' => 'Indicado pelo conselho',
            ],
            [
                'method' => 'Base',
                'description' => 'Indicado pelo coordenador de setor',
            ],
        ]);

        $this->call([
            CampingSeeder::class,
            UserSeeder::class,
        ]);
    }
}
