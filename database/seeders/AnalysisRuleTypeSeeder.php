<?php

namespace Database\Seeders;

use App\Services\Analysis\Rules\ClientEscalationRule;
use App\Services\Analysis\Rules\ClientSilenceRule;
use App\Services\Analysis\Rules\ObjectionDetectedRule;
use App\Services\Analysis\Rules\SlowManagerResponseRule;
use App\Services\Analysis\Rules\UnansweredClientRule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AnalysisRuleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('analysis_rule_types')->upsert([
            [
                'slug' => 'slow_response',
                'name' => 'Долгий ответ менеджера',
                'description' => 'Срабатывает, если менеджер ответил позже заданного порога.',
                'default_severity' => 'medium',
                'config_schema' => json_encode([
                    [
                        'key' => 'threshold_minutes',
                        'label' => 'Порог ответа (мин.)',
                        'type' => 'integer',
                        'default' => 30,
                        'min' => 1,
                    ],
                ]),
                'executor_class' => SlowManagerResponseRule::class,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'client_silence',
                'name' => 'Клиент не ответил',
                'description' => 'Последнее сообщение в диалоге отправил менеджер.',
                'default_severity' => 'high',
                'config_schema' => json_encode([]),
                'executor_class' => ClientSilenceRule::class,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'unanswered_client',
                'name' => 'Клиент без ответа',
                'description' => 'Последнее сообщение в диалоге отправил клиент.',
                'default_severity' => 'high',
                'config_schema' => json_encode([]),
                'executor_class' => UnansweredClientRule::class,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'objection_detected',
                'name' => 'Возражение клиента',
                'description' => 'Ищет ключевые слова возражений в сообщениях клиента.',
                'default_severity' => 'low',
                'config_schema' => json_encode([
                    [
                        'key' => 'keywords',
                        'label' => 'Ключевые слова (через запятую)',
                        'type' => 'keywords',
                        'default' => 'дорого, дороговато, откаж, не нужн, не интерес, подума, не готов',
                    ],
                ]),
                'executor_class' => ObjectionDetectedRule::class,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'client_escalation',
                'name' => 'Серия сообщений клиента',
                'description' => 'Клиент отправил несколько сообщений подряд без ответа менеджера.',
                'default_severity' => 'medium',
                'config_schema' => json_encode([
                    [
                        'key' => 'min_consecutive',
                        'label' => 'Мин. сообщений подряд',
                        'type' => 'integer',
                        'default' => 3,
                        'min' => 2,
                    ],
                ]),
                'executor_class' => ClientEscalationRule::class,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], uniqueBy: ['slug'], update: [
            'name',
            'description',
            'default_severity',
            'config_schema',
            'executor_class',
            'updated_at',
        ]);
    }
}
