<?php

namespace App\Services\Analysis;

use App\Models\AnalysisRule;
use App\Models\Dialogue;
use App\Models\DialogueAnalysisEvent;
use App\Models\User;
use App\Services\Analysis\DTO\AnalysisEventDTO;
use App\Services\Analysis\DTO\AnalysisResultDTO;
use App\Services\Analysis\Enums\AnalysisSeverity;
use App\Services\Auth\Enums\UserRole;
use App\Services\Dialogue\Exceptions\DialogueAccessDeniedException;
use App\Services\Dialogue\Exceptions\DialogueNotFoundException;

class AnalysisService
{
    public function __construct(
        private readonly AnalysisRuleRegistry $registry,
    ) {}

    /**
     * @throws DialogueNotFoundException
     * @throws DialogueAccessDeniedException
     */
    public function analyze(User $user, int $dialogueId): AnalysisResultDTO
    {
        if (! $this->canViewAnalysis($user)) {
            throw new DialogueAccessDeniedException('Анализ диалогов доступен только менеджерам и администраторам.');
        }

        $dialogue = $this->findAccessibleDialogue($user, $dialogueId);
        $context = AnalysisContext::fromDialogue($dialogue);

        $drafts = [];

        $rules = AnalysisRule::query()
            ->where('is_enabled', true)
            ->orderBy('id')
            ->get();

        foreach ($rules as $rule) {
            if (! $this->registry->hasType($rule->rule_type)) {
                continue;
            }

            $executor = $this->registry->makeExecutor($rule);
            $drafts = array_merge($drafts, $executor->analyze($context, $rule));
        }

        DialogueAnalysisEvent::query()
            ->where('dialogue_id', $dialogue->id)
            ->delete();

        $now = now();

        foreach ($drafts as $draft) {
            DialogueAnalysisEvent::query()->create([
                'dialogue_id' => $dialogue->id,
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
            ->where('dialogue_id', $dialogue->id)
            ->get()
            ->sortBy(fn (DialogueAnalysisEvent $event) => $this->severityWeight($event->severity))
            ->map(fn (DialogueAnalysisEvent $event) => AnalysisEventDTO::fromModel($event))
            ->values()
            ->all();

        return new AnalysisResultDTO(
            dialogueId: $dialogue->id,
            total: count($events),
            analyzedAt: $now->toIso8601String(),
            events: $events,
        );
    }

    private function canViewAnalysis(User $user): bool
    {
        return $user->resolveRole() !== UserRole::Client;
    }

    /**
     * @throws DialogueNotFoundException
     * @throws DialogueAccessDeniedException
     */
    private function findAccessibleDialogue(User $user, int $dialogueId): Dialogue
    {
        $query = Dialogue::query()->with(['messages.sender']);

        if ($user->resolveRole() !== UserRole::Client) {
            $query->withTrashed();
        }

        $dialogue = $query->find($dialogueId);

        if ($dialogue === null) {
            throw new DialogueNotFoundException;
        }

        if (! $this->canAccessDialogue($user, $dialogue)) {
            throw new DialogueAccessDeniedException;
        }

        return $dialogue;
    }

    private function canAccessDialogue(User $user, Dialogue $dialogue): bool
    {
        return match ($user->resolveRole()) {
            UserRole::Admin => true,
            UserRole::Manager => $dialogue->manager_id === $user->id,
            UserRole::Client => false,
        };
    }

    private function severityWeight(string $severity): int
    {
        return match ($severity) {
            AnalysisSeverity::High->value => 0,
            AnalysisSeverity::Medium->value => 1,
            default => 2,
        };
    }
}
