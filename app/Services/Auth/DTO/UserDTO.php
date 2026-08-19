<?php

namespace App\Services\Auth\DTO;

use App\Services\Auth\Enums\UserRole;
use App\Models\User;

readonly class UserDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public UserRole $role,
        public string $roleLabel,
    ) {}

    public static function fromModel(User $user): self
    {
        $role = UserRole::from($user->role->slug);

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $role,
            roleLabel: $role->label(),
        );
    }

    /**
     * @return array{
     *     id: int,
     *     name: string,
     *     email: string,
     *     role: string,
     *     role_label: string
     * }
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
            'role_label' => $this->roleLabel,
        ];
    }
}
