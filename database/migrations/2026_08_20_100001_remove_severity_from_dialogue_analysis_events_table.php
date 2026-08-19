<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('dialogue_analysis_events', 'severity')) {
            return;
        }

        Schema::table('dialogue_analysis_events', function (Blueprint $table) {
            $table->dropIndex(['dialogue_id', 'severity']);
            $table->dropColumn('severity');
        });

        Schema::table('dialogue_analysis_events', function (Blueprint $table) {
            if (! $this->hasDialogueIdIndex()) {
                $table->index('dialogue_id');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('dialogue_analysis_events', 'severity')) {
            return;
        }

        Schema::table('dialogue_analysis_events', function (Blueprint $table) {
            if ($this->hasDialogueIdIndex()) {
                $table->dropIndex(['dialogue_id']);
            }
        });

        Schema::table('dialogue_analysis_events', function (Blueprint $table) {
            $table->string('severity')->default('medium');
            $table->index(['dialogue_id', 'severity']);
        });
    }

    private function hasDialogueIdIndex(): bool
    {
        $indexes = Schema::getIndexes('dialogue_analysis_events');

        foreach ($indexes as $index) {
            if ($index['name'] === 'dialogue_analysis_events_dialogue_id_index') {
                return true;
            }
        }

        return false;
    }
};
