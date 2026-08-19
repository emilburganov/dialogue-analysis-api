<?php

namespace App\Services\Analysis\Exceptions;

use RuntimeException;

class AnalysisRuleNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Правило анализа не найдено.');
    }
}
