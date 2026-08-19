<?php

namespace App\Services\Analysis\DTO;

readonly class AnalysisResultDTO
{
    /**
     * @param  list<AnalysisEventDTO>  $events
     */
    public function __construct(
        public int $dialogueId,
        public int $total,
        public string $analyzedAt,
        public array $events,
    ) {}
}
