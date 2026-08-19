<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DialogueAnalysisEvent extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'dialogue_id',
        'rule_slug',
        'severity',
        'title',
        'description',
        'message_ids',
        'context',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'message_ids' => 'array',
            'context' => 'array',
        ];
    }

    /**
     * @return BelongsTo<Dialogue, $this>
     */
    public function dialogue(): BelongsTo
    {
        return $this->belongsTo(Dialogue::class);
    }

    /**
     * @return BelongsTo<AnalysisRule, $this>
     */
    public function rule(): BelongsTo
    {
        return $this->belongsTo(AnalysisRule::class, 'rule_slug', 'slug');
    }
}
