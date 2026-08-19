<?php

namespace App\Services\Analysis\Exceptions;

use RuntimeException;

class AnalysisDialogueNotFoundException extends RuntimeException
{
    public function __construct(string $message = 'Диалог не найден.')
    {
        parent::__construct($message);
    }
}
