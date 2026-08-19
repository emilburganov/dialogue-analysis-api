<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dialogue_analysis_events', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('dialogue_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('analysis_rule_id')->constrained('analysis_rules');
            $table->string('severity');
            $table->string('title');
            $table->text('description');
            $table->json('message_ids');
            $table->json('context')->nullable();
            $table->timestamps();

            $table->index(['dialogue_id', 'severity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dialogue_analysis_events');
    }
};
