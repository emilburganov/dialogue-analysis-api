<?php

namespace App\Services\Analysis\Exceptions;

use RuntimeException;

class AnalysisAccessDeniedException extends RuntimeException
{
    public function __construct(string $message = 'Нет доступа к анализу этого диалога.')
    {
        parent::__construct($message);
    }
}
