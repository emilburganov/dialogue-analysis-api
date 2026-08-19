<?php

namespace App\Services\Analysis;

use App\Models\AnalysisRule;
use App\Models\AnalysisRuleType;

class AnalysisRuleRegistry
{
    private ?array $typesCache = null;

    public function types(): array
    {
        if ($this->typesCache !== null) {
            return $this->typesCache;
        }

        $this->typesCache = AnalysisRuleType::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (AnalysisRuleType $type) => [
                $type->slug => [
                    'name' => $type->name,
                    'description' => $type->description,
                    'class' => $type->executor_class,
                    'default_severity' => $type->default_severity,
                    'config_schema' => $type->config_schema,
                ],
            ])
            ->all();

        return $this->typesCache;
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
        $type = AnalysisRuleType::query()->where('slug', $ruleType)->first();

        if ($type === null) {
            return [];
        }

        return $type->defaultConfig();
    }

    public function makeExecutor(AnalysisRule $rule): AnalysisRuleInterface
    {
        $type = $this->types()[$rule->rule_type] ?? null;

        if ($type === null) {
            throw new \InvalidArgumentException("Unknown rule type: {$rule->rule_type}");
        }

        $class = $type['class'];

        if (! is_subclass_of($class, AnalysisRuleInterface::class)) {
            throw new \InvalidArgumentException("Invalid executor class for rule type: {$rule->rule_type}");
        }

        return app($class);
    }
}
