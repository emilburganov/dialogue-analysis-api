<?php

namespace Database\Seeders;

use App\Models\AnalysisRuleType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalysisRuleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $rows = [
            [
                'slug' => 'slow_response',
                'rule_type' => 'slow_response',
                'name' => 'Долгий ответ менеджера',
                'description' => 'Менеджер ответил клиенту позже установленного порога.',
            ],
            [
                'slug' => 'client_silence',
                'rule_type' => 'client_silence',
                'name' => 'Клиент не ответил',
                'description' => 'После сообщения менеджера клиент не продолжил переписку.',
            ],
            [
                'slug' => 'unanswered_client',
                'rule_type' => 'unanswered_client',
                'name' => 'Клиент без ответа',
                'description' => 'Клиент написал, но менеджер не ответил на последнее сообщение.',
            ],
            [
                'slug' => 'objection_detected',
                'rule_type' => 'objection_detected',
                'name' => 'Возражение клиента',
                'description' => 'В сообщении клиента обнаружены признаки возражения или сомнения.',
            ],
            [
                'slug' => 'client_escalation',
                'rule_type' => 'client_escalation',
                'name' => 'Серия сообщений клиента',
                'description' => 'Клиент отправил несколько сообщений подряд без ответа менеджера.',
            ],
        ];

        DB::table('analysis_rules')->upsert(
            array_map(function (array $row) use ($now) {
                $type = AnalysisRuleType::query()
                    ->where('slug', $row['rule_type'])
                    ->firstOrFail();

                return [
                    ...$row,
                    'default_severity' => $type->default_severity,
                    'is_enabled' => true,
                    'is_system' => true,
                    'config' => json_encode($type->defaultConfig()),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }, $rows),
            uniqueBy: ['slug'],
            update: [
                'rule_type',
                'name',
                'description',
                'default_severity',
                'is_enabled',
                'is_system',
                'config',
                'updated_at',
            ],
        );
    }
}
