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
}
