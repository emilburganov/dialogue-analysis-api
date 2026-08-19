<?php

use App\Http\Controllers\API\AnalysisController;
use App\Http\Controllers\API\AnalysisRuleController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DialogueController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/dialogues', [DialogueController::class, 'index']);
    Route::post('/dialogues', [DialogueController::class, 'store']);
    Route::get('/dialogues/{id}', [DialogueController::class, 'show']);
    Route::get('/dialogues/{id}/analysis', [AnalysisController::class, 'show']);
    Route::delete('/dialogues/{id}', [DialogueController::class, 'destroy']);
    Route::patch('/dialogues/{id}/result', [DialogueController::class, 'updateResult']);
    Route::post('/dialogues/{id}/messages', [DialogueController::class, 'sendMessage']);

    Route::get('/analysis-rules/types', [AnalysisRuleController::class, 'types']);
    Route::get('/analysis-rules', [AnalysisRuleController::class, 'index']);
    Route::post('/analysis-rules', [AnalysisRuleController::class, 'store']);
    Route::put('/analysis-rules/{id}', [AnalysisRuleController::class, 'update']);
    Route::patch('/analysis-rules/{id}/toggle', [AnalysisRuleController::class, 'toggle']);
    Route::delete('/analysis-rules/{id}', [AnalysisRuleController::class, 'destroy']);
});
