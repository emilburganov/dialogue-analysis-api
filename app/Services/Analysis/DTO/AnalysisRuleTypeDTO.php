<?php

namespace App\Services\Analysis\DTO;

readonly class AnalysisRuleTypeDTO
{
    /**
     * @param  list<array<string, mixed>>  $configSchema
     */
    public function __construct(
        public string $type,
        public string $name,
        public string $description,
        public string $defaultSeverity,
        public array $configSchema,
    ) {}
}
