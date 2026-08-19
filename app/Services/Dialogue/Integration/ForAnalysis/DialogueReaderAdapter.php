<?php

namespace App\Services\Dialogue\Integration\ForAnalysis;

use App\Models\Dialogue;
use App\Models\Message;
use App\Models\User;
use App\Services\Analysis\Contracts\DialogueReaderInterface;
use App\Services\Analysis\DTO\DialogueSnapshot;
use App\Services\Analysis\DTO\MessageSnapshot;
use App\Services\Analysis\Enums\MessageAuthor;
use App\Services\Analysis\Exceptions\AnalysisAccessDeniedException;
use App\Services\Analysis\Exceptions\AnalysisDialogueNotFoundException;
use App\Services\Auth\Enums\UserRole;
use App\Services\Dialogue\Enums\MessageSenderType;

class DialogueReaderAdapter implements DialogueReaderInterface
{
    /**
     * @throws AnalysisDialogueNotFoundException
     * @throws AnalysisAccessDeniedException
     */
    public function getDialogueForAnalysis(User $user, int $dialogueId): DialogueSnapshot
    {
        if ($user->resolveRole() === UserRole::Client) {
            throw new AnalysisAccessDeniedException(
                'Анализ диалогов доступен только менеджерам и администраторам.',
            );
        }

        $dialogue = $this->findDialogue($dialogueId);

        if ($dialogue === null) {
            throw new AnalysisDialogueNotFoundException;
        }

        if (! $this->canAccess($user, $dialogue)) {
            throw new AnalysisAccessDeniedException;
        }

        return new DialogueSnapshot(
            id: $dialogue->id,
            messages: $dialogue->messages
                ->map(fn (Message $message) => $this->mapMessage($message))
                ->values(),
        );
    }

    private function findDialogue(int $dialogueId): ?Dialogue
    {
        return Dialogue::query()
            ->with(['messages.sender'])
            ->withTrashed()
            ->find($dialogueId);
    }

    private function canAccess(User $user, Dialogue $dialogue): bool
    {
        return match ($user->resolveRole()) {
            UserRole::Admin => true,
            UserRole::Manager => $dialogue->manager_id === $user->id,
            UserRole::Client => false,
        };
    }

    private function mapMessage(Message $message): MessageSnapshot
    {
        return new MessageSnapshot(
            id: $message->id,
            body: $message->body,
            sentAt: $message->sent_at,
            author: $this->mapAuthor($message->resolveSenderType()),
        );
    }

    private function mapAuthor(MessageSenderType $senderType): MessageAuthor
    {
        return match ($senderType) {
            MessageSenderType::Client => MessageAuthor::Client,
            MessageSenderType::Manager => MessageAuthor::Manager,
        };
    }
}
