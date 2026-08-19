<?php

namespace App\Services\Analysis;

use App\Models\AnalysisRule;
use App\Models\DialogueAnalysisEvent;
use App\Models\User;
use App\Services\Analysis\Contracts\DialogueReaderInterface;
use App\Services\Analysis\DTO\AnalysisEventDTO;
use App\Services\Analysis\DTO\AnalysisResultDTO;
use App\Services\Analysis\Enums\AnalysisSeverity;
use App\Services\Analysis\Exceptions\AnalysisAccessDeniedException;
use App\Services\Analysis\Exceptions\AnalysisDialogueNotFoundException;
use Carbon\Carbon;

class AnalysisService
{
    public function __construct(
        private readonly AnalysisRuleRegistry $registry,
        private readonly DialogueReaderInterface $dialogueReader,
    ) {}

    /**
     * @throws AnalysisDialogueNotFoundException
     * @throws AnalysisAccessDeniedException
     */
    public function analyze(User $user, int $dialogueId): AnalysisResultDTO
    {
        $snapshot = $this->dialogueReader->getDialogueForAnalysis($user, $dialogueId);
        $context = AnalysisContext::fromSnapshot($snapshot);

        $drafts = [];

        $rules = AnalysisRule::query()
            ->where('is_enabled', true)
            ->orderBy('id')
            ->get();

        foreach ($rules as $rule) {
            $executor = $this->registry->makeExecutor($rule);
            $drafts = array_merge($drafts, $executor->analyze($context, $rule));
        }

        DialogueAnalysisEvent::query()
            ->where('dialogue_id', $snapshot->id)
            ->delete();

        $now = now();

        foreach ($drafts as $draft) {
            DialogueAnalysisEvent::query()->create([
                'dialogue_id' => $snapshot->id,
                'rule_slug' => $draft->ruleSlug,
                'severity' => $draft->severity->value,
                'title' => $draft->title,
                'description' => $draft->description,
                'message_ids' => $draft->messageIds,
                'context' => $draft->context,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $events = DialogueAnalysisEvent::query()
            ->with('rule')
            ->where('dialogue_id', $snapshot->id)
            ->get()
            ->sortBy(
                fn (DialogueAnalysisEvent $event) => AnalysisSeverity::from($event->severity)->getSeverityWeight()
            )
            ->map(
                fn (DialogueAnalysisEvent $event) => AnalysisEventDTO::fromModel($event)
            )
            ->values()
            ->all();

        return new AnalysisResultDTO(
            dialogueId: $snapshot->id,
            total: count($events),
            analyzedAt: $now,
            events: $events,
        );
    }

    private function severityWeight(string $severity): int
    {
        return match ($severity) {
            AnalysisSeverity::High->value => 0,
            AnalysisSeverity::Medium->value => 1,
            AnalysisSeverity::Low->value => 2,
        };
    }
}
