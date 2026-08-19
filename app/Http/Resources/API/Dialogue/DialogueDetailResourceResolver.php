<?php

namespace App\Http\Resources\API\Dialogue;

use App\Http\Resources\API\Dialogue\ClientDialogueDetailResource;
use App\Http\Resources\API\Dialogue\StaffDialogueDetailResource;
use App\Models\User;
use App\Services\Auth\Enums\UserRole;
use App\Services\Dialogue\DTO\DialogueDetailDTO;

class DialogueDetailResourceResolver
{
    public static function resolve(DialogueDetailDTO $dialogue, User $user)
    {
        $userRole = UserRole::from($user->role->slug);

        return match ($userRole) {
            UserRole::Client => ClientDialogueDetailResource::make($dialogue),
            UserRole::Admin => StaffDialogueDetailResource::make($dialogue),
            UserRole::Manager => StaffDialogueDetailResource::make($dialogue),
        };
    }
}
