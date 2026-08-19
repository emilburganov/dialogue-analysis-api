<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Dialogue\SendMessageRequest;
use App\Http\Resources\API\Dialogue\MessageResource;
use App\Services\Dialogue\DialogueService;
use App\Services\Dialogue\Exceptions\DialogueAccessDeniedException;
use App\Services\Dialogue\Exceptions\DialogueLimitReachedException;
use App\Services\Dialogue\Exceptions\DialogueNotFoundException;
use App\Services\Dialogue\Exceptions\NoManagersAvailableException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DialogueController extends Controller
{
    public function __construct(
        private readonly DialogueService $dialogueService,
    ) {}

    public function store(Request $request): JsonResponse
    {
        try {
            $dialogue = $this->dialogueService->create($request->user());
        } catch (DialogueAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        } catch (DialogueLimitReachedException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (NoManagersAvailableException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(
            $this->dialogueService->presentDetail($dialogue, $request->user(), $request),
            201,
        );
    }

    public function index(Request $request): JsonResponse
    {
        $dialogues = $this->dialogueService->list($request->user());

        return response()->json([
            'data' => $this->dialogueService->presentListCollection($dialogues, $request->user(), $request),
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $dialogue = $this->dialogueService->get($request->user(), $id);
        } catch (DialogueNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (DialogueAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(
            $this->dialogueService->presentDetail($dialogue, $request->user(), $request),
        );
    }

    public function destroy(Request $request, int $id): Response|JsonResponse
    {
        try {
            $this->dialogueService->delete($request->user(), $id);
        } catch (DialogueNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (DialogueAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->noContent();
    }

    public function sendMessage(SendMessageRequest $request, int $id): JsonResponse
    {
        try {
            $message = $this->dialogueService->sendMessage(
                user: $request->user(),
                id: $id,
                dto: $request->toDto(),
            );
        } catch (DialogueNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        } catch (DialogueAccessDeniedException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

        return response()->json(
            (new MessageResource($message))->toArray($request),
            201,
        );
    }
}
