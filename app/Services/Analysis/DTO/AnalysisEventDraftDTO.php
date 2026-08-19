<?php

namespace App\Services\Analysis\DTO;

readonly class AnalysisEventDraftDTO
{
    /**
     * @param  list<int>  $messageIds
     * @param  array<string, mixed>|null  $context
     */
    public function __construct(
        public int $analysisRuleId,
        public string $title,
        public string $description,
        public array $messageIds,
        public ?array $context = null,
    ) {}
}
