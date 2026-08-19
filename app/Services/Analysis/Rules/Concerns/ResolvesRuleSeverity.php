<?php

namespace App\Services\Analysis\Rules\Concerns;

use App\Models\AnalysisRule;
use App\Services\Analysis\Enums\AnalysisSeverity;

trait ResolvesRuleSeverity
{
    protected function severity(AnalysisRule $rule): AnalysisSeverity
    {
        return AnalysisSeverity::from($rule->default_severity);
    }
}
