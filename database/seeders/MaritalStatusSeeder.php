<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run()
    {
        \App\Models\MaritalStatus::create(['nome' => 'Solteiro(a)']);
        \App\Models\MaritalStatus::create(['nome' => 'Casado(a)']);
        \App\Models\MaritalStatus::create(['nome' => 'Divorciado(a)']);
    }
}
