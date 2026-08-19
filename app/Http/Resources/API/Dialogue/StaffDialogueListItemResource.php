<?php

namespace App\Http\Resources\API\Dialogue;

use App\Services\Dialogue\DTO\DialogueListItemDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DialogueListItemDTO */
class StaffDialogueListItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'manager_name' => $this->managerName,
            'client_name' => $this->clientName,
            'last_message_at' => $this->lastMessageAt,
            'preview' => $this->preview,
            'result' => $this->result->value,
            'result_label' => $this->resultLabel,
            'deleted_by_client' => $this->deletedByClient,
        ];
    }
}
