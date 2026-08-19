<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\Analysis\AnalysisResultResource;
use App\Services\Analysis\AnalysisService;
use App\Services\Analysis\Exceptions\AnalysisAccessDeniedException;
use App\Services\Analysis\Exceptions\AnalysisDialogueNotFoundException;
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
        } catch (AnalysisDialogueNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (AnalysisAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(AnalysisResultResource::make($result), 200);
    }
}
