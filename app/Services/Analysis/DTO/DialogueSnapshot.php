<?php

namespace App\Services\Analysis\DTO;

use Illuminate\Support\Collection;

readonly class DialogueSnapshot
{
    /**
     * @param  Collection<int, MessageSnapshot>  $messages
     */
    public function __construct(
        public int $id,
        public Collection $messages,
    ) {}
}
