<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Services\Auth\DTO\AuthResponseDTO;
use App\Services\Auth\DTO\LoginDTO;
use App\Services\Auth\DTO\UserDTO;
use App\Services\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * @throws InvalidCredentialsException
     */
    public function login(LoginDTO $dto): AuthResponseDTO
    {
        $user = User::query()->firstWhere('email', $dto->email);

        if ($user === null || ! Hash::check($dto->password, $user->password)) {
            throw new InvalidCredentialsException;
        }

        $token = $user->createToken('api')->plainTextToken;

        return new AuthResponseDTO(
            user: UserDTO::fromModel($user),
            token: $token,
        );
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function me(User $user): UserDTO
    {
        return UserDTO::fromModel($user);
    }
}
