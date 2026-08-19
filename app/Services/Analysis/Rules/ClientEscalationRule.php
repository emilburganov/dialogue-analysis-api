<?php

namespace App\Services\Analysis\Rules;

use App\Models\AnalysisRule;
use App\Services\Analysis\AnalysisContext;
use App\Services\Analysis\AnalysisRuleInterface;
use App\Services\Analysis\DTO\AnalysisEventDraftDTO;
use App\Services\Analysis\Rules\Concerns\ResolvesRuleSeverity;

class ClientEscalationRule implements AnalysisRuleInterface
{
    use ResolvesRuleSeverity;

    public function type(): string
    {
        return 'client_escalation';
    }

    public function analyze(AnalysisContext $context, AnalysisRule $rule): array
    {
        $events = [];
        $streak = [];
        $messages = $context->messages;
        $minConsecutive = (int) ($rule->config['min_consecutive'] ?? 3);

        foreach ($messages as $message) {
            if ($context->isFromClient($message)) {
                $streak[] = $message;

                continue;
            }

            if (count($streak) >= $minConsecutive) {
                $events[] = $this->buildEvent($streak, $rule);
            }

            $streak = [];
        }

        if (count($streak) >= $minConsecutive) {
            $events[] = $this->buildEvent($streak, $rule);
        }

        return $events;
    }

    /**
     * @param  list<\App\Models\Message>  $messages
     */
    private function buildEvent(array $messages, AnalysisRule $rule): AnalysisEventDraftDTO
    {
        $ids = array_map(fn ($message) => $message->id, $messages);

        return new AnalysisEventDraftDTO(
            ruleSlug: $rule->slug,
            severity: $this->severity($rule),
            title: sprintf('Клиент отправил %d сообщения подряд без ответа', count($messages)),
            description: 'Клиент несколько раз подряд написал менеджеру — возможно, вопрос остался без внимания.',
            messageIds: $ids,
            context: [
                'consecutive_count' => count($messages),
            ],
        );
    }
}
