<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Analysis\StoreAnalysisRuleRequest;
use App\Http\Requests\API\Analysis\UpdateAnalysisRuleRequest;
use App\Http\Resources\API\Analysis\AnalysisRuleResource;
use App\Http\Resources\API\Analysis\AnalysisRuleTypeResource;
use App\Services\Analysis\AnalysisRuleManagementService;
use App\Services\Analysis\Exceptions\AnalysisRuleAccessDeniedException;
use App\Services\Analysis\Exceptions\AnalysisRuleImmutableException;
use App\Services\Analysis\Exceptions\AnalysisRuleNotFoundException;
use App\Services\Analysis\Exceptions\AnalysisRuleValidationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalysisRuleController extends Controller
{
    public function __construct(
        private readonly AnalysisRuleManagementService $ruleService,
    ) {}

    public function types(Request $request): JsonResponse
    {
        try {
            $types = $this->ruleService->listTypes($request->user());
        } catch (AnalysisRuleAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json([
            'data' => array_map(
                fn ($type) => (new AnalysisRuleTypeResource($type))->toArray($request),
                $types,
            ),
        ]);
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $rules = $this->ruleService->list($request->user());
        } catch (AnalysisRuleAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json([
            'data' => array_map(
                fn ($rule) => (new AnalysisRuleResource($rule))->toArray($request),
                $rules,
            ),
        ]);
    }

    public function store(StoreAnalysisRuleRequest $request): JsonResponse
    {
        try {
            $rule = $this->ruleService->create($request->user(), $request->validated());
        } catch (AnalysisRuleAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (AnalysisRuleValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'data' => (new AnalysisRuleResource($rule))->toArray($request),
        ], 201);
    }

    public function update(UpdateAnalysisRuleRequest $request, int $id): JsonResponse
    {
        try {
            $rule = $this->ruleService->update($request->user(), $id, $request->validated());
        } catch (AnalysisRuleAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (AnalysisRuleNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (AnalysisRuleValidationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (AnalysisRuleImmutableException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'data' => (new AnalysisRuleResource($rule))->toArray($request),
        ]);
    }

    public function toggle(Request $request, int $id): JsonResponse
    {
        try {
            $rule = $this->ruleService->toggle($request->user(), $id);
        } catch (AnalysisRuleAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (AnalysisRuleNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (AnalysisRuleImmutableException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'data' => (new AnalysisRuleResource($rule))->toArray($request),
        ]);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $this->ruleService->delete($request->user(), $id);
        } catch (AnalysisRuleAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (AnalysisRuleNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (AnalysisRuleImmutableException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(null, 204);
    }
}
