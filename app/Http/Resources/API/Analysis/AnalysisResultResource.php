<?php

namespace App\Http\Resources\API\Analysis;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnalysisResultResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'dialogue_id' => $this->dialogueId,
            'total' => $this->total,
            'analyzed_at' => $this->analyzedAt,
            'data' => AnalysisEventResource::collection($this->events),
        ];
    }
}
