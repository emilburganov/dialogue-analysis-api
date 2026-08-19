<?php

namespace App\Services\Analysis;

use App\Models\AnalysisRule;
use App\Models\AnalysisRuleType;

class AnalysisRuleRegistry
{
    /** @var array<string, array<string, mixed>>|null */
    private ?array $typesCache = null;

    /**
     * @return array<string, array{
     *     id: int,
     *     name: string,
     *     description: string,
     *     class: class-string<AnalysisRuleInterface>,
     *     default_severity: string,
     *     config_schema: list<array<string, mixed>>
     * }>
     */
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
                    'id' => $type->id,
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

    public function hasTypeId(int $ruleTypeId): bool
    {
        return AnalysisRuleType::query()->whereKey($ruleTypeId)->exists();
    }

    public function findType(int $ruleTypeId): ?AnalysisRuleType
    {
        return AnalysisRuleType::query()->find($ruleTypeId);
    }

    /**
     * @return array<string, mixed>
     */
    public function defaultConfig(AnalysisRuleType $type): array
    {
        return $type->defaultConfig();
    }

    public function makeExecutor(AnalysisRule $rule): AnalysisRuleInterface
    {
        $rule->loadMissing('type');

        if ($rule->type === null) {
            throw new \InvalidArgumentException("Unknown rule type for rule: {$rule->slug}");
        }

        $class = $rule->type->executor_class;

        if (! is_subclass_of($class, AnalysisRuleInterface::class)) {
            throw new \InvalidArgumentException("Invalid executor class for rule type: {$rule->type->slug}");
        }

        return app($class);
    }
}
