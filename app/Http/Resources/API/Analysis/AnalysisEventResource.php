<?php

namespace App\Http\Resources\API\Analysis;

use App\Services\Analysis\DTO\AnalysisEventDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AnalysisEventDTO */
class AnalysisEventResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'analysis_rule_id' => $this->analysisRuleId,
            'rule_name' => $this->ruleName,
            'severity' => $this->severity->value,
            'severity_label' => $this->severityLabel,
            'title' => $this->title,
            'description' => $this->description,
            'message_ids' => $this->messageIds,
            'context' => $this->context,
            'detected_at' => $this->detectedAt,
        ];
    }
}
