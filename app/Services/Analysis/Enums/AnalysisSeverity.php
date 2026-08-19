<?php

namespace App\Services\Analysis\Enums;

enum AnalysisSeverity: string
{
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';

    public function label(): string
    {
        return match ($this) {
            self::High => 'Высокая',
            self::Medium => 'Средняя',
            self::Low => 'Низкая',
        };
    }
}
