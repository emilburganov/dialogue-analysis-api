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
                'name' => 'Клиент не ответил',
                'description' => 'Последнее сообщение в диалоге отправил менеджер.',
                'default_severity' => 'medium',
                'config_schema' => json_encode([]),
                'executor_class' => ClientSilenceRule::class,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Клиент без ответа',
                'description' => 'Последнее сообщение в диалоге отправил клиент.',
                'default_severity' => 'high',
                'config_schema' => json_encode([]),
                'executor_class' => UnansweredClientRule::class,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Возражение клиента',
                'description' => 'Ищет ключевые слова возражений в сообщениях клиента.',
                'default_severity' => 'low',
                'config_schema' => json_encode([
                    [
                        'key' => 'keywords',
                        'label' => 'Ключевые слова:',
                        'type' => 'keywords',
                        'default' => ['дорого', 'дороговато', 'откаж', 'не нужн', 'не интерес', 'подума', 'не готов'],
                    ],
                ]),
                'executor_class' => ObjectionDetectedRule::class,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
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
        ], uniqueBy: ['executor_class'], update: [
            'name',
            'description',
            'default_severity',
            'config_schema',
            'updated_at',
        ]);
    }
}
