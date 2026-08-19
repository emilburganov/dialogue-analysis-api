<?php

namespace App\Services\Dialogue\Enums;

enum DialogueResultType: string
{
    case Bought = 'bought';
    case NotBought = 'not_bought';

    public function label(): string
    {
        return match ($this) {
            self::Bought => 'Купил',
            self::NotBought => 'Не купил',
        };
    }
}
