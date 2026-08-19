<?php

namespace App\Services\Analysis\DTO;

use App\Models\DialogueAnalysisEvent;
use App\Services\Analysis\Enums\AnalysisSeverity;
use Illuminate\Support\Carbon;

readonly class AnalysisEventDTO
{
    /**
     * @param  list<int>  $messageIds
     * @param  array<string, mixed>|null  $context
     */
    public function __construct(
        public int $id,
        public int $analysisRuleId,
        public string $ruleName,
        public AnalysisSeverity $severity,
        public string $severityLabel,
        public string $title,
        public string $description,
        public array $messageIds,
        public ?array $context,
        public Carbon $detectedAt,
    ) {}

    public static function fromModel(DialogueAnalysisEvent $event): self
    {
        $severity = AnalysisSeverity::from($event->severity);
        $event->loadMissing('rule');

        return new self(
            id: $event->id,
            analysisRuleId: $event->analysis_rule_id,
            ruleName: $event->rule?->name ?? '',
            severity: $severity,
            severityLabel: $severity->label(),
            title: $event->title,
            description: $event->description,
            messageIds: $event->message_ids,
            context: $event->context,
            detectedAt: $event->created_at,
        );
    }
}
