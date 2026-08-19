<?php

namespace App\Services\Analysis;

use App\Models\AnalysisRule;

interface AnalysisRuleInterface
{
    public function type(): string;

    /**
     * @return list<\App\Services\Analysis\DTO\AnalysisEventDraftDTO>
     */
    public function analyze(AnalysisContext $context, AnalysisRule $rule): array;
}
