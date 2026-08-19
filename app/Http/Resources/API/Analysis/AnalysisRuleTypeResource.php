<?php

namespace App\Http\Resources\API\Analysis;

use App\Services\Analysis\DTO\AnalysisRuleTypeDTO;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin AnalysisRuleTypeDTO */
class AnalysisRuleTypeResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'description' => $this->description,
            'default_severity' => $this->defaultSeverity,
            'config_schema' => $this->configSchema,
        ];
    }
}
