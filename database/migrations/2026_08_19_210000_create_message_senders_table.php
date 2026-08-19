<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_senders', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('label');
            $table->timestamps();
        });

        $now = now();

        DB::table('message_senders')->insert([
            ['slug' => 'manager', 'label' => 'Менеджер', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'client', 'label' => 'Клиент', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('message_senders');
    }
};
