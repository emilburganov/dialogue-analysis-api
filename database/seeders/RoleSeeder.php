<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->upsert([
            ['slug' => 'admin', 'label' => 'Администратор', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'manager', 'label' => 'Менеджер', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'client', 'label' => 'Клиент', 'created_at' => $now, 'updated_at' => $now],
        ], uniqueBy: ['slug'], update: ['label', 'updated_at']);
    }
}
