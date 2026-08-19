<?php

namespace App\Services\Analysis\Contracts;

use App\Models\User;
use App\Services\Analysis\DTO\DialogueSnapshot;
use App\Services\Analysis\Exceptions\AnalysisAccessDeniedException;
use App\Services\Analysis\Exceptions\AnalysisDialogueNotFoundException;

interface DialogueReaderInterface
{
    /**
     * @throws AnalysisDialogueNotFoundException
     * @throws AnalysisAccessDeniedException
     */
    public function getDialogueForAnalysis(User $user, int $dialogueId): DialogueSnapshot;
}
