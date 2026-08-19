<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DialogueResultSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('dialogue_results')->upsert([
            [
                'slug' => 'bought',
                'label' => 'Купил',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'not_bought',
                'label' => 'Не купил',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], uniqueBy: ['slug'], update: ['label', 'updated_at']);
    }
}
