<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MessageSenderSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('message_senders')->upsert([
            [
                'slug' => 'manager',
                'label' => 'Менеджер',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'client',
                'label' => 'Клиент',
                'created_at' => $now,
                'updated_at' => $now],
        ], uniqueBy: ['slug'], update: ['label', 'updated_at']);
    }
}
