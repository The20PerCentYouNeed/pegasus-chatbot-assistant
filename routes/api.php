<?php

use App\Http\Controllers\Api\ChatMessageController;
use App\Http\Controllers\Api\ChatSessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/chat/sessions', [ChatSessionController::class, 'store'])
    ->middleware('throttle:chat-init');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/chat/sessions/current', [ChatSessionController::class, 'show']);
    Route::delete('/chat/sessions/current', [ChatSessionController::class, 'destroy']);

    Route::get('/chat/messages', [ChatMessageController::class, 'index']);
    Route::post('/chat/messages', [ChatMessageController::class, 'store'])
        ->middleware('throttle:chat-message');
});
