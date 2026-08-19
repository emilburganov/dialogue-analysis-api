<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roleIds = Role::query()->pluck('id', 'slug');

        $users = [
            ['name' => 'Администратор', 'email' => 'admin@example.com', 'role_slug' => 'admin'],
            ['name' => 'Анна Петрова', 'email' => 'anna@example.com', 'role_slug' => 'manager'],
            ['name' => 'Максим Орлов', 'email' => 'maxim@example.com', 'role_slug' => 'manager'],
            ['name' => 'Сергей Никитин', 'email' => 'sergey@example.com', 'role_slug' => 'manager'],
            ['name' => 'Игорь Смирнов', 'email' => 'igor@example.com', 'role_slug' => 'client'],
            ['name' => 'Елена Кузнецова', 'email' => 'elena@example.com', 'role_slug' => 'client'],
            ['name' => 'Дмитрий Волков', 'email' => 'dmitry@example.com', 'role_slug' => 'client'],
            ['name' => 'Ольга Морозова', 'email' => 'olga@example.com', 'role_slug' => 'client'],
            ['name' => 'Артём Лебедев', 'email' => 'artem@example.com', 'role_slug' => 'client'],
            ['name' => 'Марина Соколова', 'email' => 'marina@example.com', 'role_slug' => 'client'],
        ];

        foreach ($users as $user) {
            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make('password'),
                    'role_id' => $roleIds[$user['role_slug']],
                    'email_verified_at' => now(),
                ],
            );
        }
    }
}
