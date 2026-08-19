<?php

namespace App\Services\Analysis\Rules;

use App\Models\AnalysisRule;
use App\Services\Analysis\AnalysisContext;
use App\Services\Analysis\AnalysisRuleInterface;
use App\Services\Analysis\DTO\AnalysisEventDraftDTO;

class ClientSilenceRule implements AnalysisRuleInterface
{
    public function type(): string
    {
        return 'client_silence';
    }

    public function analyze(AnalysisContext $context, AnalysisRule $rule): array
    {
        $lastMessage = $context->messages->last();

        if ($lastMessage === null || ! $context->isFromManager($lastMessage)) {
            return [];
        }

        return [
            new AnalysisEventDraftDTO(
                analysisRuleId: $rule->id,
                title: sprintf('Клиент не ответил на сообщение №%d', $lastMessage->id),
                description: 'Последнее сообщение в диалоге отправил менеджер — клиент перестал отвечать.',
                messageIds: [$lastMessage->id],
                context: [
                    'last_message_sender' => 'manager',
                ],
            ),
        ];
    }
}
