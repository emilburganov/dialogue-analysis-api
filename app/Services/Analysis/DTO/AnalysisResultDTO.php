<?php

namespace App\Services\Analysis\DTO;

use Illuminate\Support\Carbon;

readonly class AnalysisResultDTO
{
    /**
     * @param  list<AnalysisEventDTO>  $events
     */
    public function __construct(
        public int $dialogueId,
        public int $total,
        public Carbon $analyzedAt,
        public array $events,
    ) {}
}
