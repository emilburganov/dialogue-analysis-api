<?php

namespace App\Http\Resources\API\Dialogue;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sender' => $this->sender->value,
            'sender_label' => $this->senderLabel,
            'body' => $this->body,
            'sent_at' => $this->sentAt,
        ];
    }
}
