<?php

namespace Database\Seeders;

use App\Models\AnalysisRule;
use App\Models\AnalysisRuleType;
use App\Services\Analysis\Rules\ClientEscalationRule;
use App\Services\Analysis\Rules\ClientSilenceRule;
use App\Services\Analysis\Rules\ObjectionDetectedRule;
use App\Services\Analysis\Rules\SlowManagerResponseRule;
use App\Services\Analysis\Rules\UnansweredClientRule;
use Illuminate\Database\Seeder;

class AnalysisRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'executor_class' => SlowManagerResponseRule::class,
                'name' => 'Долгий ответ менеджера',
                'description' => 'Менеджер ответил клиенту позже установленного порога.',
            ],
            [
                'executor_class' => ClientSilenceRule::class,
                'name' => 'Клиент не ответил',
                'description' => 'После сообщения менеджера клиент не продолжил переписку.',
            ],
            [
                'executor_class' => UnansweredClientRule::class,
                'name' => 'Клиент без ответа',
                'description' => 'Клиент написал, но менеджер не ответил на последнее сообщение.',
            ],
            [
                'executor_class' => ObjectionDetectedRule::class,
                'name' => 'Возражение клиента',
                'description' => 'В сообщении клиента обнаружены признаки возражения или сомнения.',
            ],
            [
                'executor_class' => ClientEscalationRule::class,
                'name' => 'Серия сообщений клиента',
                'description' => 'Клиент отправил несколько сообщений подряд без ответа менеджера.',
            ],
        ];

        foreach ($rows as $row) {
            $type = AnalysisRuleType::query()
                ->where('executor_class', $row['executor_class'])
                ->firstOrFail();

            AnalysisRule::query()->updateOrCreate(
                [
                    'rule_type_id' => $type->id,
                    'is_system' => true,
                ],
                [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'default_severity' => $type->default_severity,
                    'is_enabled' => true,
                    'config' => $type->defaultConfig(),
                ],
            );
        }
    }
}
