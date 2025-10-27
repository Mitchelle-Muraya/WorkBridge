<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Job;

class ApplicationFactory extends Factory
{
    public function definition(): array
    {
        $statuses = ['pending', 'accepted', 'rejected'];

        return [
            'user_id' => User::where('role', 'worker')->inRandomOrder()->first()?->id ?? User::factory(),
            'job_id' => Job::inRandomOrder()->first()?->id ?? Job::factory(),
            'status' => $this->faker->randomElement($statuses),
            'applied_at' => now()->subDays(rand(1, 30)),
        ];
    }
}
