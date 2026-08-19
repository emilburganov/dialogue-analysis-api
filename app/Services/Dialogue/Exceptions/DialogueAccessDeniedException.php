<?php

namespace App\Services\Dialogue\Exceptions;

use Exception;

class DialogueAccessDeniedException extends Exception
{
    public function __construct(string $message = 'Нет доступа к этому диалогу.')
    {
        parent::__construct($message);
    }
}
