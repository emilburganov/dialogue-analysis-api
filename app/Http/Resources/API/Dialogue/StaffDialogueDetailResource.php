<?php

namespace App\Http\Resources\API\Dialogue;

use App\Http\Resources\API\Dialogue\MessageResource;
use App\Services\Dialogue\DTO\DialogueDetailDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DialogueDetailDTO */
class StaffDialogueDetailResource extends JsonResource
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
            'can_send_messages' => $this->canSendMessages,
            'messages' => MessageResource::collection($this->messages),
            'result' => $this->result->value,
            'result_label' => $this->resultLabel,
            'deleted_by_client' => $this->deletedByClient,
        ];
    }
}
