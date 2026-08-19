<?php

namespace App\Services\Dialogue\DTO;

use App\Models\Message;
use App\Services\Dialogue\Enums\MessageSenderType;
use Illuminate\Support\Carbon;

readonly class MessageDTO
{
    public function __construct(
        public int $id,
        public MessageSenderType $sender,
        public string $senderLabel,
        public string $body,
        public Carbon $sentAt,
    ) {}

    public static function fromModel(Message $message): self
    {
        $messageSenderType = MessageSenderType::from($message->sender->slug);

        return new self(
            id: $message->id,
            sender: $messageSenderType,
            senderLabel: $messageSenderType->label(),
            body: $message->body,
            sentAt: $message->sent_at,
        );
    }
}
