<?php

namespace App\Services\Auth\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct(string $message = 'Неверный Email или Пароль.')
    {
        parent::__construct($message);
    }
}
