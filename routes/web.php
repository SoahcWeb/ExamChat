<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CustomInstructionController;
use Inertia\Inertia;
use Illuminate\Http\Request;

// -----------------------------
// Ask (mini chat actuel)
Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
Route::post('/ask', [AskController::class, 'ask'])->name('ask.send');
Route::get('/ask/models', [AskController::class, 'getModels']);

// -----------------------------
// Sélection du modèle (page intermédiaire Inertia)
Route::get('/chat/models', function () {
    return Inertia::render('Chat/Models');
})->name('chat.models');

// -----------------------------
// Nouvelle conversation depuis un modèle sélectionné
Route::get('/chat/new', function () {
    $model = session('selectedModel', 'CoachCréativité'); // par défaut
    $conversation = \App\Models\Conversation::create([
        'title' => null,
        'model_used' => $model,
    ]);

    return redirect()->route('chat.show', ['conversation' => $conversation->id]);
})->name('chat.new');

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

    // 🔹 Nouvelle route pour supprimer une conversation
    Route::delete('/{conversation}', [ConversationController::class, 'destroy'])->name('chat.destroy');

    // 🔹 Nouvelle route pour sauvegarder un message assistant
    Route::post('/{conversation}/messages/{message}/save', [MessageController::class, 'save']);
});

// -----------------------------
// Streaming SSE pour token par token avec 3 assistants
Route::get('/chat/{conversation}/stream', function ($conversationId, Request $request) {
    $userMessage = $request->query('message', '');
    $botMessageId = $request->query('messageId');
    $model = $request->query('model', 'CoachCréativité');

    $systemPrompts = [
        'PhilosopheModerne' => "🧠 Tu es Nethra Philosophe. Calme, réflexion profonde, peu d’emojis, analyse et structure les idées.",
        'CoachCréativité'   => "🎨 Tu es Nethra Créatif. Idées originales, métaphores, énergie, propositions multiples.",
        'custom'            => "🧩 Tu suis les instructions personnalisées de l’utilisateur."
    ];

    $systemPrompt = $systemPrompts[$model] ?? $systemPrompts['CoachCréativité'];
    $finalMessage = $systemPrompt . "\n" . $userMessage;

    $ai = new \App\Services\OpenAIService();

    return response()->stream(function () use ($ai, $botMessageId, $finalMessage) {
        foreach ($ai->streamResponse($botMessageId, $finalMessage) as $token) {
            \App\Models\Message::where('id', $botMessageId)
                ->update(['content' => \DB::raw("CONCAT(content, '" . addslashes($token) . "')")]);

            echo "data: " . json_encode(['token' => $token]) . "\n\n";
            ob_flush();
            flush();
        }

        // ✅ Envoie un event de fin pour que le frontend ferme proprement l'EventSource
        echo "event: end\n";
        echo "data: {}\n\n";
        ob_flush();
        flush();
    }, 200, [
        'Content-Type' => 'text/event-stream',
        'Cache-Control' => 'no-cache',
        'Connection'   => 'keep-alive',
    ]);
});

// -----------------------------
// Instructions personnalisées (accessibles sans login)
Route::get('/settings/instructions', [CustomInstructionController::class, 'edit']);
Route::post('/settings/instructions', [CustomInstructionController::class, 'update']);

// -----------------------------
// Landing page + Legal
Route::get('/', function () {
    return Inertia::render('Landing');
})->name('landing');

Route::get('/legal', function () {
    return Inertia::render('Legal');
})->name('legal');

// -----------------------------
// Test Inertia
Route::get('/hello', function () {
    return Inertia::render('Hello', ['message' => 'Hello World from Laravel + Inertia!']);
});
