<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogue_results', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('label');
            $table->timestamps();
        });

        $now = now();

        DB::table('dialogue_results')->insert([
            ['slug' => 'bought', 'label' => 'Купил', 'created_at' => $now, 'updated_at' => $now],
            ['slug' => 'not_bought', 'label' => 'Не купил', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogue_results');
    }
};
