<?php

namespace App\Models;

use App\Services\Dialogue\Enums\DialogueResultType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dialogue extends Model
{
    use SoftDeletes;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'manager_id',
        'client_id',
        'result_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * @return BelongsTo<DialogueResult, $this>
     */
    public function result(): BelongsTo
    {
        return $this->belongsTo(DialogueResult::class);
    }

    /**
     * @return HasMany<Message, $this>
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('sent_at');
    }

    public function involvesUser(User $user): bool
    {
        return $this->manager_id === $user->id || $this->client_id === $user->id;
    }

    public function resolveResultType(): DialogueResultType
    {
        $this->loadMissing('result');

        if ($this->result === null) {
            return DialogueResultType::NotBought;
        }

        return DialogueResultType::from($this->result->slug);
    }

    public function resultLabel(): string
    {
        $this->loadMissing('result');

        return $this->result?->label ?? $this->resolveResultType()->label();
    }

    public function managerName(): string
    {
        return $this->manager?->name ?? 'Неизвестный менеджер';
    }

    public function clientName(): string
    {
        return $this->client?->name ?? 'Неизвестный клиент';
    }
}
