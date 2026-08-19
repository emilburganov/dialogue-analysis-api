<?php

namespace App\Services\Analysis\DTO;

use App\Services\Analysis\Enums\AnalysisSeverity;

readonly class AnalysisEventDraftDTO
{
    /**
     * @param  list<int>  $messageIds
     * @param  array<string, mixed>|null  $context
     */
    public function __construct(
        public string $ruleSlug,
        public AnalysisSeverity $severity,
        public string $title,
        public string $description,
        public array $messageIds,
        public ?array $context = null,
    ) {}
}
