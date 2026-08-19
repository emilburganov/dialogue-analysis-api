<?php

namespace App\Services\Dialogue;

use App\Models\Dialogue;
use App\Models\DialogueResult;
use App\Models\Message;
use App\Models\MessageSender;
use App\Models\User;
use App\Services\Auth\Enums\UserRole;
use App\Services\Dialogue\DTO\DialogueDetailDTO;
use App\Services\Dialogue\DTO\DialogueListItemDTO;
use App\Services\Dialogue\DTO\MessageDTO;
use App\Services\Dialogue\DTO\SendMessageDTO;
use App\Services\Dialogue\Enums\DialogueResultType;
use App\Services\Dialogue\Enums\MessageSenderType;
use App\Services\Dialogue\Exceptions\DialogueAccessDeniedException;
use App\Services\Dialogue\Exceptions\DialogueLimitReachedException;
use App\Services\Dialogue\Exceptions\DialogueNotFoundException;
use App\Services\Dialogue\Exceptions\NoManagersAvailableException;

class DialogueService
{
    public const MAX_ACTIVE_DIALOGUES_PER_CLIENT = 5;

    /**
     * @return list<DialogueListItemDTO>
     */
    public function list(User $user): array
    {
        $userRole = UserRole::from($user->role->slug);

        $query = match ($userRole) {
            UserRole::Admin => Dialogue::withTrashed(),
            UserRole::Manager => Dialogue::withTrashed()->where('manager_id', $user->id),
            UserRole::Client => Dialogue::query()->where('client_id', $user->id),
        };

        return $query
            ->whereHas('manager')
            ->whereHas('client')
            ->with([
                'manager',
                'client',
                'result',
                'messages' => fn ($query) => $query->with('sender')->orderByDesc('sent_at')->limit(1),
            ])
            ->get()
            ->sortByDesc(fn (Dialogue $dialogue) => $dialogue->messages->first()?->sent_at ?? $dialogue->updated_at)
            ->map(fn (Dialogue $dialogue) => DialogueListItemDTO::fromModel($dialogue))
            ->values()
            ->all();
    }

    /**
     * @throws DialogueAccessDeniedException
     * @throws DialogueLimitReachedException
     * @throws NoManagersAvailableException
     */
    public function create(User $user): DialogueDetailDTO
    {
        $userRole = UserRole::from($user->role->slug);

        if ($userRole !== UserRole::Client) {
            throw new DialogueAccessDeniedException('Только клиенты могут начинать новые диалоги.');
        }

        $activeCount = Dialogue::query()
            ->where('client_id', $user->id)
            ->count();

        if ($activeCount >= self::MAX_ACTIVE_DIALOGUES_PER_CLIENT) {
            throw new DialogueLimitReachedException;
        }

        $manager = User::query()
            ->whereHas('role', fn ($query) => $query->where('slug', UserRole::Manager->value))
            ->inRandomOrder()
            ->first();

        if ($manager === null) {
            throw new NoManagersAvailableException;
        }

        $dialogue = Dialogue::query()->create([
            'manager_id' => $manager->id,
            'client_id' => $user->id,
            'result_id' => DialogueResult::query()
                ->where('slug', DialogueResultType::NotBought->value)
                ->value('id'),
        ]);

        $dialogue->load(['manager', 'client', 'result', 'messages']);

        return DialogueDetailDTO::fromModel(
            dialogue: $dialogue,
            canSendMessages: true,
        );
    }

    /**
     * @throws DialogueNotFoundException
     * @throws DialogueAccessDeniedException
     */
    public function get(User $user, int $id): DialogueDetailDTO
    {
        $dialogue = $this->findDialogue($id, $user);

        if ($dialogue === null) {
            throw new DialogueNotFoundException;
        }

        $userRole = UserRole::from($user->role->slug);

        $canAccess = match ($userRole) {
            UserRole::Admin => true,
            UserRole::Manager => $dialogue->manager_id === $user->id,
            UserRole::Client => $dialogue->client_id === $user->id && ! $dialogue->trashed(),
        };

        if (! $canAccess) {
            throw new DialogueAccessDeniedException;
        }

        return DialogueDetailDTO::fromModel(
            dialogue: $dialogue,
            canSendMessages: $this->canSendMessages($user, $dialogue),
        );
    }

    /**
     * @throws DialogueNotFoundException
     * @throws DialogueAccessDeniedException
     */
    public function sendMessage(User $user, int $id, SendMessageDTO $dto): MessageDTO
    {
        $dialogue = $this->findDialogue($id, $user);

        if ($dialogue === null) {
            throw new DialogueNotFoundException;
        }

        if (! $this->canSendMessages($user, $dialogue)) {
            throw new DialogueAccessDeniedException('Вы не можете отправлять сообщения в этом диалоге.');
        }

        $senderType = match (true) {
            $user->id === $dialogue->manager_id => MessageSenderType::Manager,
            $user->id === $dialogue->client_id => MessageSenderType::Client,
        };

        $message = Message::query()->create([
            'dialogue_id' => $dialogue->id,
            'sender_id' => MessageSender::query()
                ->where('slug', $senderType->value)
                ->value('id'),
            'body' => $dto->body,
            'sent_at' => now(),
        ]);

        $message->load('sender');

        return MessageDTO::fromModel($message);
    }

    /**
     * @throws DialogueNotFoundException
     * @throws DialogueAccessDeniedException
     */
    public function updateResult(User $user, int $id, DialogueResultType $result): DialogueDetailDTO
    {
        $userRole = UserRole::from($user->role->slug);

        if ($userRole !== UserRole::Admin) {
            throw new DialogueAccessDeniedException('Менять результат диалога может только администратор.');
        }

        $dialogue = $this->findDialogue($id, $user);

        if ($dialogue === null) {
            throw new DialogueNotFoundException;
        }

        $resultId = DialogueResult::query()
            ->where('slug', $result->value)
            ->value('id');

        if ($resultId === null) {
            throw new DialogueNotFoundException;
        }

        $dialogue->result_id = $resultId;
        $dialogue->save();
        $dialogue->load(['manager', 'client', 'result', 'messages.sender']);

        return DialogueDetailDTO::fromModel(
            dialogue: $dialogue,
            canSendMessages: $this->canSendMessages($user, $dialogue),
        );
    }

    /**
     * @throws DialogueNotFoundException
     * @throws DialogueAccessDeniedException
     */
    public function delete(User $user, int $id): void
    {
        $dialogue = Dialogue::query()->find($id);

        if ($dialogue === null) {
            throw new DialogueNotFoundException;
        }

        $userRole = UserRole::from($user->role->slug);

        if ($userRole !== UserRole::Client || $dialogue->client_id !== $user->id) {
            throw new DialogueAccessDeniedException('Только клиент может удалить свой диалог.');
        }

        $dialogue->delete();
    }

    private function findDialogue(int $id, User $user): ?Dialogue
    {
        $query = Dialogue::query()->with(['manager', 'client', 'result', 'messages.sender']);

        $userRole = UserRole::from($user->role->slug);

        if ($userRole !== UserRole::Client) {
            $query->withTrashed();
        }

        return $query->find($id);
    }

    private function canSendMessages(User $user, Dialogue $dialogue): bool
    {
        if ($dialogue->trashed()) {
            return false;
        }

        $userRole = UserRole::from($user->role->slug);

        return match ($userRole) {
            UserRole::Admin => false,
            UserRole::Manager => $dialogue->manager_id === $user->id,
            UserRole::Client => $dialogue->client_id === $user->id,
        };
    }
}
