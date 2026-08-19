<?php

namespace App\Services\Auth\DTO;

readonly class AuthResponseDTO
{
    public function __construct(
        public UserDTO $user,
        public string $token,
    ) {}

    /**
     * @return array{user: array{id: int, name: string, email: string}, token: string}
     */
    public function toArray(): array
    {
        return [
            'user' => $this->user->toArray(),
            'token' => $this->token,
        ];
    }
}
