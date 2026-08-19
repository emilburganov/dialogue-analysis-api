<?php

namespace App\Services\Dialogue\DTO;

use App\Models\Dialogue;
use App\Services\Dialogue\Enums\DialogueResultType;
use Illuminate\Support\Carbon;

readonly class DialogueListItemDTO
{
    public function __construct(
        public int $id,
        public string $managerName,
        public string $clientName,
        public DialogueResultType $result,
        public string $resultLabel,
        public Carbon $lastMessageAt,
        public string $preview,
        public bool $deletedByClient,
    ) {}

    public static function fromModel(Dialogue $dialogue): self
    {
        $lastMessage = $dialogue
            ->messages()
            ->get()
            ->last();

        return new self(
            id: $dialogue->id,
            managerName: $dialogue->managerName(),
            clientName: $dialogue->clientName(),
            result: $dialogue->resolveResultType(),
            resultLabel: $dialogue->resultLabel(),
            lastMessageAt: $lastMessage?->sent_at ?? $dialogue->updated_at,
            preview: $lastMessage?->body ?? '',
            deletedByClient: $dialogue->trashed(),
        );
    }
}
