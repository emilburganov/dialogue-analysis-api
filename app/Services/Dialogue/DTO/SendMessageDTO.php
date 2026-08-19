<?php

namespace App\Services\Dialogue\DTO;

readonly class SendMessageDTO
{
    public function __construct(
        public string $body,
    ) {}

    /**
     * @param  array{body: string}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(body: $data['body']);
    }
}
