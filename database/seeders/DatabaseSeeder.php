<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DialogueResultSeeder::class,
            MessageSenderSeeder::class,
            AnalysisRuleSeeder::class,
            UserSeeder::class,
            DialogueSeeder::class,
        ]);
    }
}
