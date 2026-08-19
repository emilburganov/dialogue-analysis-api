<?php

namespace App\Services\Dialogue\DTO;

use App\Models\Dialogue;
use App\Services\Dialogue\Enums\DialogueResultType;

readonly class DialogueDetailDTO
{
    /**
     * @param  list<MessageDTO>  $messages
     */
    public function __construct(
        public int $id,
        public string $managerName,
        public string $clientName,
        public DialogueResultType $result,
        public string $resultLabel,
        public bool $canSendMessages,
        public bool $deletedByClient,
        public array $messages,
    ) {}

    public static function fromModel(Dialogue $dialogue, bool $canSendMessages): self
    {
        return new self(
            id: $dialogue->id,
            managerName: $dialogue->managerName(),
            clientName: $dialogue->clientName(),
            result: $dialogue->resolveResultType(),
            resultLabel: $dialogue->resultLabel(),
            canSendMessages: $canSendMessages,
            deletedByClient: $dialogue->trashed(),
            messages: $dialogue->messages
                ->map(fn ($message) => MessageDTO::fromModel($message))
                ->values()
                ->all(),
        );
    }
}
