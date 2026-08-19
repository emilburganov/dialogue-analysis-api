<?php

namespace App\Services\Dialogue\DTO;

readonly class SendMessageDTO
{
    public function __construct(
        public string $body,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(body: $data['body']);
    }
}
