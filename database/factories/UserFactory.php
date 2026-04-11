<?php

namespace Database\Factories;

use App\Enums\Sex;
use App\Models\MaritalStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cpf' => fake()->numerify('###########'),
            'name' => fake()->name(),
            'birthday' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'sex' => fake()->randomElement(Sex::cases()),
            'phone' => fake()->numerify('###########'),
            'email' => fake()->unique()->safeEmail(),
            'is_counselor' => fake()->boolean(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'marital_status_id' => MaritalStatus::factory(),
        ];
    }
}
