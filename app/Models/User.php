<?php

namespace App\Models;

use App\Services\Auth\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * @return BelongsTo<Role, $this>
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        $userRole = UserRole::from($this->role->slug);

        return $userRole === UserRole::Admin;
    }

    /**
     * @return HasMany<Dialogue, $this>
     */
    public function managedDialogues(): HasMany
    {
        return $this->hasMany(Dialogue::class, 'manager_id');
    }

    /**
     * @return HasMany<Dialogue, $this>
     */
    public function clientDialogues(): HasMany
    {
        return $this->hasMany(Dialogue::class, 'client_id');
    }
}
