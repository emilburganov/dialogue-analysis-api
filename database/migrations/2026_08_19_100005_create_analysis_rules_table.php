<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_rules', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('rule_type_id')
                ->constrained('analysis_rule_types')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->string('default_severity')->default('medium');
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_system')->default(false);
            $table->json('config')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_rules');
    }
};
