<?php

namespace App\Services\Dialogue\Exceptions;

use Exception;

class DialogueLimitReachedException extends Exception
{
    public function __construct(string $message = 'Можно иметь не более 5 активных диалогов.')
    {
        parent::__construct($message);
    }
}
