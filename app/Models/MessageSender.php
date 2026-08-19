<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MessageSender extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'label',
    ];

    // TODO: Remove me
    // /**
    //  * @return HasMany<Message, $this>
    //  */
    // public function messages(): HasMany
    // {
    //     return $this->hasMany(Message::class, 'sender_id');
    // }
}
