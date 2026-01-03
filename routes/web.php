<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use Inertia\Inertia;
use Illuminate\Http\Request;

// -----------------------------
// Ask (mini chat actuel)
Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
Route::post('/ask', [AskController::class, 'ask'])->name('ask.send');
Route::get('/ask/models', [AskController::class, 'getModels']);

// -----------------------------
// Chat type ChatGPT (Inertia)
Route::get('/chat', [ConversationController::class, 'index'])->name('chat.index');
Route::get('/chat/{conversation}', [ConversationController::class, 'show'])->name('chat.show');

// -----------------------------
// API JSON pour le chat (Axios)
Route::prefix('api/chat')->group(function () {
    Route::get('/', [ConversationController::class, 'listJson']);
    Route::get('/{conversation}', [ConversationController::class, 'showJson']);
    Route::post('/', [ConversationController::class, 'storeJson']);
    Route::post('/{conversation}/messages', [MessageController::class, 'store']);
    Route::patch('/{conversation}/model', [ConversationController::class, 'updateModel']);
    Route::post('/{conversation}/generate-title', [ConversationController::class, 'generateTitle']);
});

// -----------------------------
// Streaming SSE pour token par token
Route::get('/chat/{conversation}/stream', function($conversationId, Request $request) {
    $userMessage = $request->query('message', '');
    $botMessageId = $request->query('messageId'); // <-- ID du message assistant

    $ai = new \App\Services\OpenAIService();

    return response()->stream(function() use ($ai, $botMessageId, $userMessage) {
        foreach ($ai->streamResponse($botMessageId, $userMessage) as $token) {
            // ⚡ Met à jour le message assistant en base
            \App\Models\Message::where('id', $botMessageId)
                ->update(['content' => \DB::raw("CONCAT(content, '" . addslashes($token) . "')")]);

            // ⚡ Envoie le token SSE au frontend
            echo "data: " . json_encode(['token' => $token]) . "\n\n";
            ob_flush();
            flush();
        }
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection' => 'keep-alive',
    ]);
});

// -----------------------------
// Landing page + Legal
Route::get('/', function () { return Inertia::render('Landing'); })->name('landing');
Route::get('/legal', function () { return Inertia::render('Legal'); })->name('legal');

// -----------------------------
// Test Inertia
Route::get('/hello', function () {
    return Inertia::render('Hello', ['message' => 'Hello World from Laravel + Inertia!']);
});
