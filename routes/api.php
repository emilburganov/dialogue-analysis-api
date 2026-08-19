<?php

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
    Route::delete('/dialogues/{id}', [DialogueController::class, 'destroy']);
    Route::post('/dialogues/{id}/messages', [DialogueController::class, 'sendMessage']);
});
