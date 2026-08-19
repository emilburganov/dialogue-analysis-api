<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DialogueResult extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'label',
    ];

    /**
     * @return HasMany<Dialogue, $this>
     */
    public function dialogues(): HasMany
    {
        return $this->hasMany(Dialogue::class, 'result_id');
    }
}
