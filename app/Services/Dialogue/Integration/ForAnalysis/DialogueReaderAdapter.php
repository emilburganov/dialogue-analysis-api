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
        $userRole = UserRole::from($user->role->slug);

        if ($userRole === UserRole::Client) {
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
                ->map(function (Message $message) {
                    $author = match (MessageSenderType::from($message->sender->slug)) {
                        MessageSenderType::Client => MessageAuthor::Client,
                        MessageSenderType::Manager => MessageAuthor::Manager,
                    };

                    return new MessageSnapshot(
                        id: $message->id,
                        body: $message->body,
                        sentAt: $message->sent_at,
                        author: $author,
                    );
                })
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
        $userRole = UserRole::from($user->role->slug);

        return match ($userRole) {
            UserRole::Admin => true,
            UserRole::Manager => $dialogue->manager_id === $user->id,
            UserRole::Client => false,
        };
    }
}
