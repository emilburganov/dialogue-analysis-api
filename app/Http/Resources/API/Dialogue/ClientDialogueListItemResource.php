<?php

namespace App\Http\Resources\API\Dialogue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientDialogueListItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'manager_name' => $this->managerName,
            'client_name' => $this->clientName,
            'last_message_at' => $this->lastMessageAt,
            'preview' => $this->preview,
        ];
    }
}
