<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Mirim: 10 to 12 years old (Let's use 11)
        User::factory()->create([
            'cpf' => '11111111111',
            'name' => 'Usuário Mirim',
            'birthday' => Carbon::now()->subYears(11)->format('Y-m-d'),
            'email' => 'mirim@teste.com',
            'marital_status_id' => 1, // Solteiro
        ]);

        // Juvenil: 13 to 17 (Let's use 13)
        User::factory()->create([
            'cpf' => '22222222222',
            'name' => 'Usuário Juvenil',
            'birthday' => Carbon::now()->subYears(13)->format('Y-m-d'),
            'email' => 'juvenil@teste.com',
            'marital_status_id' => 1,
        ]);

        // FAC: 14 to 17 (Let's use 14)
        User::factory()->create([
            'cpf' => '33333333333',
            'name' => 'Usuário FAC',
            'birthday' => Carbon::now()->subYears(14)->format('Y-m-d'),
            'email' => 'fac@teste.com',
            'marital_status_id' => 1,
        ]);

        // JOAM: 15 to 18 (Let's use 16)
        User::factory()->create([
            'cpf' => '44444444444',
            'name' => 'Usuário JOAM',
            'birthday' => Carbon::now()->subYears(16)->format('Y-m-d'),
            'email' => 'joam@teste.com',
            'marital_status_id' => 1,
        ]);

        // Sênior: 18 to 100 (Let's use 30)
        User::factory()->create([
            'cpf' => '55555555555',
            'name' => 'Usuário Sênior',
            'birthday' => Carbon::now()->subYears(30)->format('Y-m-d'),
            'email' => 'senior@teste.com',
            'marital_status_id' => 1,
        ]);

        // Casais: 18 to 100 (Let's use 40, married)
        User::factory()->create([
            'cpf' => '66666666666',
            'name' => 'Usuário Casais',
            'birthday' => Carbon::now()->subYears(40)->format('Y-m-d'),
            'email' => 'casais@teste.com',
            'marital_status_id' => 4, // Casado
        ]);

        // Cônjuge do Usuário Casais
        User::factory()->create([
            'cpf' => '77777777777',
            'name' => 'Cônjuge Casais',
            'birthday' => Carbon::now()->subYears(38)->format('Y-m-d'),
            'email' => 'conjuge_casais@teste.com',
            'marital_status_id' => 4, // Casado
        ]);
        
        // Some random users just in case
        User::factory()->count(10)->create([
            'marital_status_id' => 1,
        ]);
    }
}
