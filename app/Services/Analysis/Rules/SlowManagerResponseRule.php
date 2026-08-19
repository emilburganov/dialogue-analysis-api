<?php

namespace App\Services\Analysis\Rules;

use App\Models\AnalysisRule;
use App\Services\Analysis\AnalysisContext;
use App\Services\Analysis\AnalysisRuleInterface;
use App\Services\Analysis\DTO\AnalysisEventDraftDTO;
use App\Services\Analysis\Rules\Concerns\ResolvesRuleSeverity;

class SlowManagerResponseRule implements AnalysisRuleInterface
{
    use ResolvesRuleSeverity;

    public function type(): string
    {
        return 'slow_response';
    }

    public function analyze(AnalysisContext $context, AnalysisRule $rule): array
    {
        $events = [];
        $messages = $context->messages;
        $thresholdMinutes = (int) ($rule->config['threshold_minutes'] ?? 30);

        for ($index = 0; $index < $messages->count() - 1; $index++) {
            $current = $messages[$index];
            $next = $messages[$index + 1];

            if (! $context->isFromClient($current) || ! $context->isFromManager($next)) {
                continue;
            }

            $delayMinutes = $current->sentAt->diffInMinutes($next->sentAt);

            if ($delayMinutes <= $thresholdMinutes) {
                continue;
            }

            $events[] = new AnalysisEventDraftDTO(
                analysisRuleId: $rule->id,
                severity: $this->severity($rule),
                title: sprintf('Менеджер ответил через %d мин.', $delayMinutes),
                description: sprintf(
                    'Клиент написал сообщение №%d, менеджер ответил через %d минут (порог — %d мин.).',
                    $current->id,
                    $delayMinutes,
                    $thresholdMinutes,
                ),
                messageIds: [$current->id, $next->id],
                context: [
                    'delay_minutes' => $delayMinutes,
                    'threshold_minutes' => $thresholdMinutes,
                ],
            );
        }

        return $events;
    }
}
