<?php

namespace App\Http\Resources\API\Dialogue;

use App\Http\Resources\API\Dialogue\ClientDialogueListItemResource;
use App\Http\Resources\API\Dialogue\StaffDialogueListItemResource;
use App\Models\User;
use App\Services\Auth\Enums\UserRole;

class DialogueListItemResourceResolver
{
    public static function resolve($dialogues, User $user)
    {
        $userRole = UserRole::from($user->role->slug);

        return match ($userRole) {
            UserRole::Client => ClientDialogueListItemResource::collection($dialogues),
            UserRole::Admin => StaffDialogueListItemResource::collection($dialogues),
            UserRole::Manager => StaffDialogueListItemResource::collection($dialogues),
        };
    }
}
