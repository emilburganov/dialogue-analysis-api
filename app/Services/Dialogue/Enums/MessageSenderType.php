<?php

namespace App\Services\Dialogue\Enums;

enum MessageSenderType: string
{
    case Manager = 'manager';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Manager => 'Менеджер',
            self::Client => 'Клиент',
        };
    }
}
