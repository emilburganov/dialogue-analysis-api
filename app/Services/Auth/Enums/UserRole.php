<?php

namespace App\Services\Auth\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Manager = 'manager';
    case Client = 'client';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Администратор',
            self::Manager => 'Менеджер',
            self::Client => 'Клиент',
        };
    }
}
