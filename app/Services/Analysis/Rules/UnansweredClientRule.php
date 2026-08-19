<?php

namespace App\Services\Analysis\Rules;

use App\Models\AnalysisRule;
use App\Services\Analysis\AnalysisContext;
use App\Services\Analysis\AnalysisRuleInterface;
use App\Services\Analysis\DTO\AnalysisEventDraftDTO;

class UnansweredClientRule implements AnalysisRuleInterface
{
    public function type(): string
    {
        return 'unanswered_client';
    }

    public function analyze(AnalysisContext $context, AnalysisRule $rule): array
    {
        $lastMessage = $context->messages->last();

        if ($lastMessage === null || ! $context->isFromClient($lastMessage)) {
            return [];
        }

        return [
            new AnalysisEventDraftDTO(
                analysisRuleId: $rule->id,
                title: sprintf('Клиент ждёт ответ на сообщение №%d', $lastMessage->id),
                description: 'Последнее сообщение в диалоге отправил клиент — менеджер ещё не ответил.',
                messageIds: [$lastMessage->id],
                context: [
                    'last_message_sender' => 'client',
                ],
            ),
        ];
    }
}
