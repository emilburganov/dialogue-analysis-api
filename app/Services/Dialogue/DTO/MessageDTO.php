<?php

namespace App\Services\Dialogue\DTO;

use App\Models\Message;
use App\Services\Dialogue\Enums\MessageSenderType;

readonly class MessageDTO
{
    public function __construct(
        public int $id,
        public MessageSenderType $sender,
        public string $senderLabel,
        public string $body,
        public string $sentAt,
    ) {}

    public static function fromModel(Message $message): self
    {
        return new self(
            id: $message->id,
            sender: $message->resolveSenderType(),
            senderLabel: $message->senderLabel(),
            body: $message->body,
            sentAt: $message->sent_at->toIso8601String(),
        );
    }
}
