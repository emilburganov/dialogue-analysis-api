<?php

namespace App\Services\Analysis\Enums;

enum MessageAuthor: string
{
    case Client = 'client';
    case Manager = 'manager';
}
