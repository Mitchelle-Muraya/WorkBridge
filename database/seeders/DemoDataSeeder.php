<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Worker;
use App\Models\Job;
use App\Models\Application;
use App\Models\Rating;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "🇰🇪 Seeding Kenyan demo data for WorkBridge...\n";

        // 1️⃣ Create Clients
        $clients = collect([
            ['name' => 'John Mwangi', 'email' => 'johnmwangi@example.com'],
            ['name' => 'Grace Wambui', 'email' => 'gracewambui@example.com'],
            ['name' => 'Brian Otieno', 'email' => 'brianotieno@example.com'],
        ])->map(function ($client) {
            return User::create([
                'name' => $client['name'],
                'email' => $client['email'],
                'password' => Hash::make('password'),
                'role' => 'client',
                'is_profile_complete' => true,
                'created_at' => now(),
            ]);
        });

        // 2️⃣ Create Workers (also in 'workers' table)
        $workers = collect([
            ['name' => 'Peter Kamau', 'email' => 'peterkamau@example.com', 'skill' => 'Plumber'],
            ['name' => 'Mary Achieng', 'email' => 'maryachieng@example.com', 'skill' => 'Tailor'],
            ['name' => 'Kevin Kiptoo', 'email' => 'kevinkiptoo@example.com', 'skill' => 'Electrician'],
        ])->map(function ($worker) {
            $user = User::create([
                'name' => $worker['name'],
                'email' => $worker['email'],
                'password' => Hash::make('password'),
                'role' => 'worker',
                'is_profile_complete' => true,
                'created_at' => now(),
            ]);

            return Worker::create([
                'user_id' => $user->id,
                'skills' => $worker['skill'],
                'experience' => '3 years',
                'availability' => 'available',
            ]);
        });

        // 3️⃣ Create Jobs
        $jobs = [
            ['client_id' => $clients[0]->id, 'title' => 'Fix leaking taps', 'description' => 'Repair bathroom plumbing in Westlands.', 'location' => 'Nairobi', 'status' => 'completed', 'deadline' => '2025-10-30'],
            ['client_id' => $clients[0]->id, 'title' => 'Sew school uniforms', 'description' => 'Tailoring work for uniforms.', 'location' => 'Thika', 'status' => 'in_progress', 'deadline' => '2025-11-05'],
            ['client_id' => $clients[1]->id, 'title' => 'Wire new house', 'description' => 'Install wiring in a new 3-bedroom house.', 'location' => 'Kisumu', 'status' => 'completed', 'deadline' => '2025-10-28'],
            ['client_id' => $clients[1]->id, 'title' => 'Repair metal gate', 'description' => 'Welding and hinge fixing needed.', 'location' => 'Nakuru', 'status' => 'pending', 'deadline' => '2025-11-08'],
            ['client_id' => $clients[2]->id, 'title' => 'Paint living room', 'description' => 'Repainting the walls and ceiling.', 'location' => 'Eldoret', 'status' => 'completed', 'deadline' => '2025-10-27'],
            ['client_id' => $clients[2]->id, 'title' => 'Assemble kitchen shelves', 'description' => 'Carpentry work for cabinets.', 'location' => 'Nairobi', 'status' => 'pending', 'deadline' => '2025-11-12'],
        ];

        foreach ($jobs as $job) {
            Job::create($job);
        }

        // 4️⃣ Applications
        $applications = [
            ['user_id' => $workers[0]->user_id, 'job_id' => 1, 'status' => 'approved'],
            ['user_id' => $workers[1]->user_id, 'job_id' => 2, 'status' => 'pending'],
            ['user_id' => $workers[2]->user_id, 'job_id' => 3, 'status' => 'approved'],
            ['user_id' => $workers[0]->user_id, 'job_id' => 4, 'status' => 'pending'],
            ['user_id' => $workers[1]->user_id, 'job_id' => 5, 'status' => 'approved'],
            ['user_id' => $workers[2]->user_id, 'job_id' => 6, 'status' => 'pending'],
        ];

        foreach ($applications as $app) {
            Application::create([
                'user_id' => $app['user_id'],
                'job_id' => $app['job_id'],
                'status' => $app['status'],
                'applied_at' => Carbon::now()->subDays(rand(1, 5)),
            ]);
        }

        // 5️⃣ Ratings
        $ratings = [
            ['worker_id' => $workers[0]->id, 'client_id' => $clients[0]->id, 'job_id' => 1, 'rating' => 4.8, 'review' => 'Peter did an excellent plumbing job.'],
            ['worker_id' => $workers[1]->id, 'client_id' => $clients[1]->id, 'job_id' => 2, 'rating' => 4.5, 'review' => 'Mary’s tailoring was neat and timely.'],
            ['worker_id' => $workers[2]->id, 'client_id' => $clients[2]->id, 'job_id' => 3, 'rating' => 4.7, 'review' => 'Kevin did a perfect wiring job.'],
        ];

        foreach ($ratings as $rating) {
            Rating::create($rating);
        }

        echo "✅ Kenyan demo data seeded successfully!\n";
    }
}
