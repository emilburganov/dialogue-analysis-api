<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('analysis_rules', function (Blueprint $table) {
            $table->string('rule_type')->nullable()->after('slug');
            $table->string('default_severity')->default('medium')->after('description');
            $table->boolean('is_enabled')->default(true)->after('default_severity');
            $table->json('config')->nullable()->after('is_enabled');
        });

        $defaults = [
            'slow_response' => ['threshold_minutes' => 30],
            'client_silence' => [],
            'unanswered_client' => [],
            'objection_detected' => [
                'keywords' => [
                    'дорого', 'дороговато', 'откаж', 'не нужн', 'не интерес',
                    'подума', 'не готов', 'не подойд', 'бюджет', 'слишком',
                    'вернусь позже', 'пока откаж',
                ],
            ],
            'client_escalation' => ['min_consecutive' => 3],
        ];

        $severities = [
            'slow_response' => 'medium',
            'client_silence' => 'high',
            'unanswered_client' => 'high',
            'objection_detected' => 'low',
            'client_escalation' => 'medium',
        ];

        foreach ($defaults as $slug => $config) {
            DB::table('analysis_rules')
                ->where('slug', $slug)
                ->update([
                    'rule_type' => $slug,
                    'default_severity' => $severities[$slug],
                    'is_enabled' => true,
                    'config' => json_encode($config),
                ]);
        }

        Schema::table('analysis_rules', function (Blueprint $table) {
            $table->string('rule_type')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('analysis_rules', function (Blueprint $table) {
            $table->dropColumn(['rule_type', 'default_severity', 'is_enabled', 'config']);
        });
    }
};
