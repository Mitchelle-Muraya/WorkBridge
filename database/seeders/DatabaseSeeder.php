<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // remove test user since DemoDataSeeder creates its own users
        // remove RoleSeeder because role table does not exist

        $this->call([
            SkillRatesSeeder::class,

        ]);
    }
}
