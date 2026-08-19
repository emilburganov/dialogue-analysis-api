<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Analysis\AnalysisEventResource;
use App\Services\Analysis\AnalysisService;
use App\Services\Dialogue\Exceptions\DialogueAccessDeniedException;
use App\Services\Dialogue\Exceptions\DialogueNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    public function __construct(
        private readonly AnalysisService $analysisService,
    ) {}

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $result = $this->analysisService->analyze($request->user(), $id);
        } catch (DialogueNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (DialogueAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json([
            'dialogue_id' => $result->dialogueId,
            'total' => $result->total,
            'analyzed_at' => $result->analyzedAt,
            'data' => array_map(
                fn ($event) => (new AnalysisEventResource($event))->toArray($request),
                $result->events,
            ),
        ]);
    }
}
