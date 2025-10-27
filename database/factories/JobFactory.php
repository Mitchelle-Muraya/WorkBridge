<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class JobFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['pending', 'in_progress', 'completed'];
        $categories = ['Plumbing', 'Electrical', 'Carpentry', 'Cleaning', 'Painting'];

        return [
            'client_id' => User::where('role', 'client')->inRandomOrder()->first()?->id ?? User::factory(),
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->sentence(10),
            'location' => $this->faker->city(),
            'status' => $this->faker->randomElement($statuses),
            'created_at' => now()->subDays(rand(1, 60)),
            'deadline' => $this->faker->dateTimeBetween('+3 days', '+1 month'),
        ];
    }
}
