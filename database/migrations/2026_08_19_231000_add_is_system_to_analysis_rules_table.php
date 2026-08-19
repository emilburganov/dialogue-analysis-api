<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private const SYSTEM_SLUGS = [
        'slow_response',
        'client_silence',
        'unanswered_client',
        'objection_detected',
        'client_escalation',
    ];

    public function up(): void
    {
        Schema::table('analysis_rules', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('is_enabled');
        });

        DB::table('analysis_rules')
            ->whereIn('slug', self::SYSTEM_SLUGS)
            ->update(['is_system' => true]);
    }

    public function down(): void
    {
        Schema::table('analysis_rules', function (Blueprint $table) {
            $table->dropColumn('is_system');
        });
    }
};
