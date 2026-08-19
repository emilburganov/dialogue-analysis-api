<?php

namespace App\Models;

use App\Services\Dialogue\Enums\MessageSenderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'dialogue_id',
        'sender_id',
        'body',
        'sent_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // TODO: Remove me
    // /**
    //  * @return BelongsTo<Dialogue, $this>
    //  */
    // public function dialogue(): BelongsTo
    // {
    //     return $this->belongsTo(Dialogue::class);
    // }

    /**
     * @return BelongsTo<MessageSender, $this>
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(MessageSender::class);
    }

    public function resolveSenderType(): MessageSenderType
    {
        $this->loadMissing('sender');

        if ($this->sender === null) {
            return MessageSenderType::Client;
        }

        return MessageSenderType::from($this->sender->slug);
    }
}
