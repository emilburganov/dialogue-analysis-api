<?php

namespace App\Services\Analysis\DTO;

use App\Services\Analysis\Enums\MessageAuthor;
use Illuminate\Support\Carbon;

readonly class MessageSnapshot
{
    public function __construct(
        public int $id,
        public string $body,
        public Carbon $sentAt,
        public MessageAuthor $author,
    ) {}
}
