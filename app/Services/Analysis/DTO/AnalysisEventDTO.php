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
        public string $ruleSlug,
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

        return new self(
            id: $event->id,
            ruleSlug: $event->rule_slug,
            ruleName: $event->rule?->name ?? $event->rule_slug,
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
