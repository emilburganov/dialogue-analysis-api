<?php

namespace App\Http\Resources\API\Analysis;

use App\Services\Analysis\DTO\AnalysisRuleDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AnalysisRuleDTO */
class AnalysisRuleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rule_type_id' => $this->ruleTypeId,
            'name' => $this->name,
            'description' => $this->description,
            'default_severity' => $this->defaultSeverity,
            'default_severity_label' => $this->defaultSeverityLabel,
            'is_enabled' => $this->isEnabled,
            'is_system' => $this->isSystem,
            'config' => $this->config,
            'type_name' => $this->typeName,
            'type_description' => $this->typeDescription,
        ];
    }
}
