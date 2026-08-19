<?php

namespace App\Services\Dialogue\Exceptions;

use Exception;

class DialogueNotFoundException extends Exception
{
    public function __construct(string $message = 'Диалог не найден.')
    {
        parent::__construct($message);
    }
}
