<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        $roles = ['client', 'worker'];

        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),

            'password' => bcrypt('password'), // same password for testing
            'role' => $this->faker->randomElement($roles),

        ];
    }
}
