<?php

namespace App\Services\Analysis;

use App\Services\Analysis\DTO\DialogueSnapshot;
use App\Services\Analysis\DTO\MessageSnapshot;
use App\Services\Analysis\Enums\MessageAuthor;
use Illuminate\Support\Collection;

readonly class AnalysisContext
{
    /**
     * @param  Collection<int, MessageSnapshot>  $messages
     */
    public function __construct(
        public int $dialogueId,
        public Collection $messages,
    ) {}

    public static function fromSnapshot(DialogueSnapshot $snapshot): self
    {
        return new self(
            dialogueId: $snapshot->id,
            messages: $snapshot->messages,
        );
    }

    public function isFromClient(MessageSnapshot $message): bool
    {
        return $message->author === MessageAuthor::Client;
    }

    public function isFromManager(MessageSnapshot $message): bool
    {
        return $message->author === MessageAuthor::Manager;
    }
}
