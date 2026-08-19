<?php

namespace App\Services\Analysis;

use App\Models\AnalysisRule;
use App\Services\Analysis\Rules\ClientEscalationRule;
use App\Services\Analysis\Rules\ClientSilenceRule;
use App\Services\Analysis\Rules\ObjectionDetectedRule;
use App\Services\Analysis\Rules\SlowManagerResponseRule;
use App\Services\Analysis\Rules\UnansweredClientRule;

class AnalysisRuleRegistry
{
    /**
     * @return array<string, array{
     *     name: string,
     *     description: string,
     *     class: class-string<AnalysisRuleInterface>,
     *     default_severity: string,
     *     config_schema: list<array<string, mixed>>
     * }>
     */
    public function types(): array
    {
        return [
            'slow_response' => [
                'name' => 'Долгий ответ менеджера',
                'description' => 'Срабатывает, если менеджер ответил позже заданного порога.',
                'class' => SlowManagerResponseRule::class,
                'default_severity' => 'medium',
                'config_schema' => [
                    [
                        'key' => 'threshold_minutes',
                        'label' => 'Порог ответа (мин.)',
                        'type' => 'integer',
                        'default' => 30,
                        'min' => 1,
                    ],
                ],
            ],
            'client_silence' => [
                'name' => 'Клиент не ответил',
                'description' => 'Последнее сообщение в диалоге отправил менеджер.',
                'class' => ClientSilenceRule::class,
                'default_severity' => 'high',
                'config_schema' => [],
            ],
            'unanswered_client' => [
                'name' => 'Клиент без ответа',
                'description' => 'Последнее сообщение в диалоге отправил клиент.',
                'class' => UnansweredClientRule::class,
                'default_severity' => 'high',
                'config_schema' => [],
            ],
            'objection_detected' => [
                'name' => 'Возражение клиента',
                'description' => 'Ищет ключевые слова возражений в сообщениях клиента.',
                'class' => ObjectionDetectedRule::class,
                'default_severity' => 'low',
                'config_schema' => [
                    [
                        'key' => 'keywords',
                        'label' => 'Ключевые слова (через запятую)',
                        'type' => 'keywords',
                        'default' => 'дорого, дороговато, откаж, не нужн, не интерес, подума, не готов',
                    ],
                ],
            ],
            'client_escalation' => [
                'name' => 'Серия сообщений клиента',
                'description' => 'Клиент отправил несколько сообщений подряд без ответа менеджера.',
                'class' => ClientEscalationRule::class,
                'default_severity' => 'medium',
                'config_schema' => [
                    [
                        'key' => 'min_consecutive',
                        'label' => 'Мин. сообщений подряд',
                        'type' => 'integer',
                        'default' => 3,
                        'min' => 2,
                    ],
                ],
            ],
        ];
    }

    public function hasType(string $ruleType): bool
    {
        return array_key_exists($ruleType, $this->types());
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultConfig(string $ruleType): array
    {
        $schema = $this->types()[$ruleType]['config_schema'] ?? [];
        $config = [];

        foreach ($schema as $field) {
            $value = $field['default'] ?? null;

            if ($field['type'] === 'keywords' && is_string($value)) {
                $config[$field['key']] = array_values(array_filter(
                    array_map('trim', explode(',', $value)),
                    fn ($keyword) => $keyword !== '',
                ));

                continue;
            }

            $config[$field['key']] = $value;
        }

        return $config;
    }

    public function makeExecutor(AnalysisRule $rule): AnalysisRuleInterface
    {
        $type = $this->types()[$rule->rule_type] ?? null;

        if ($type === null) {
            throw new \InvalidArgumentException("Unknown rule type: {$rule->rule_type}");
        }

        return app($type['class']);
    }
}
