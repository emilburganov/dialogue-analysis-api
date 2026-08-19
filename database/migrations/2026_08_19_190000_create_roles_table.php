<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('label');
            $table->timestamps();
        });

        $now = now();

        DB::table('roles')->insert([
            ['slug' => 'admin', 'label' => 'Администратор', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'manager', 'label' => 'Менеджер', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'client', 'label' => 'Клиент', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
