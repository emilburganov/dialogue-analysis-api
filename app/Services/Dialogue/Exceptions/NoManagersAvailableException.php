<?php

namespace App\Services\Dialogue\Exceptions;

use Exception;

class NoManagersAvailableException extends Exception
{
    public function __construct(string $message = 'Нет доступных менеджеров.')
    {
        parent::__construct($message);
    }
}
