<?php

namespace App\Services\Dialogue\DTO;

use App\Models\Dialogue;
use App\Services\Dialogue\Enums\DialogueResultType;

readonly class DialogueDetailDTO
{
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
        $dialogueResultType = DialogueResultType::from($dialogue->result->slug);

        return new self(
            id: $dialogue->id,
            managerName: $dialogue->manager->name,
            clientName: $dialogue->client->name,
            result: $dialogueResultType,
            resultLabel: $dialogueResultType->label(),
            canSendMessages: $canSendMessages,
            deletedByClient: $dialogue->trashed(),
            messages: $dialogue->messages
                ->map(fn ($message) => MessageDTO::fromModel($message))
                ->values()
                ->all(),
        );
    }
}
