<?php

namespace App\Services\Analysis\DTO;

readonly class AnalysisRuleDTO
{
    /**
     * @param  array<string, mixed>|null  $config
     */
    public function __construct(
        public int $id,
        public string $slug,
        public string $ruleType,
        public string $name,
        public ?string $description,
        public string $defaultSeverity,
        public string $defaultSeverityLabel,
        public bool $isEnabled,
        public bool $isSystem,
        public ?array $config,
        public string $typeName,
        public string $typeDescription,
    ) {}
}
