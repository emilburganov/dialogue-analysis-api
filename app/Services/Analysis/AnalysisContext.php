<?php

namespace App\Services\Analysis;

use App\Models\Dialogue;
use App\Models\Message;
use Illuminate\Support\Collection;

readonly class AnalysisContext
{
    /** @param Collection<int, Message> $messages */
    public function __construct(
        public Dialogue $dialogue,
        public Collection $messages,
    ) {}

    public static function fromDialogue(Dialogue $dialogue): self
    {
        $dialogue->loadMissing(['messages.sender']);

        return new self(
            dialogue: $dialogue,
            messages: $dialogue->messages->values(),
        );
    }

    public function isFromClient(Message $message): bool
    {
        return $message->resolveSenderType()->value === 'client';
    }

    public function isFromManager(Message $message): bool
    {
        return $message->resolveSenderType()->value === 'manager';
    }
}
