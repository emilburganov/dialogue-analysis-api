<?php

namespace App\Services\Analysis\Rules;

use App\Models\AnalysisRule;
use App\Services\Analysis\AnalysisContext;
use App\Services\Analysis\AnalysisRuleInterface;
use App\Services\Analysis\DTO\AnalysisEventDraftDTO;
use App\Services\Analysis\DTO\MessageSnapshot;
use App\Services\Analysis\Rules\Concerns\ResolvesRuleSeverity;

class ObjectionDetectedRule implements AnalysisRuleInterface
{
    use ResolvesRuleSeverity;

    public function type(): string
    {
        return 'objection_detected';
    }

    public function analyze(AnalysisContext $context, AnalysisRule $rule): array
    {
        $events = [];
        $keywords = $this->keywords($rule);

        foreach ($context->messages as $message) {
            if (! $context->isFromClient($message)) {
                continue;
            }

            $keyword = $this->matchKeyword($message, $keywords);

            if ($keyword === null) {
                continue;
            }

            $events[] = new AnalysisEventDraftDTO(
                ruleSlug: $rule->slug,
                severity: $this->severity($rule),
                title: 'Обнаружено возможное возражение клиента',
                description: sprintf(
                    'В сообщении №%d найдена ключевая фраза «%s».',
                    $message->id,
                    $keyword,
                ),
                messageIds: [$message->id],
                context: [
                    'keyword' => $keyword,
                    'excerpt' => mb_substr($message->body, 0, 120),
                ],
            );
        }

        return $events;
    }

    /**
     * @return list<string>
     */
    private function keywords(AnalysisRule $rule): array
    {
        $configured = $rule->config['keywords'] ?? [];

        if (is_string($configured)) {
            $configured = array_map('trim', explode(',', $configured));
        }

        return array_values(array_filter($configured, fn ($keyword) => is_string($keyword) && $keyword !== ''));
    }

    /**
     * @param  list<string>  $keywords
     */
    private function matchKeyword(MessageSnapshot $message, array $keywords): ?string
    {
        $body = mb_strtolower($message->body);

        foreach ($keywords as $keyword) {
            if (str_contains($body, mb_strtolower($keyword))) {
                return $keyword;
            }
        }

        return null;
    }
}
