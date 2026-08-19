<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalysisRule extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'rule_type',
        'name',
        'description',
        'default_severity',
        'is_enabled',
        'is_system',
        'config',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'is_system' => 'boolean',
            'config' => 'array',
        ];
    }

    /**
     * @return HasMany<DialogueAnalysisEvent, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(DialogueAnalysisEvent::class, 'rule_slug', 'slug');
    }
}
