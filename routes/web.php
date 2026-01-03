<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AskController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use Inertia\Inertia;

// -----------------------------
// Ask (mini chat actuel)
// -----------------------------
Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
Route::post('/ask', [AskController::class, 'ask'])->name('ask.send');
Route::get('/ask/models', [AskController::class, 'getModels']);

// -----------------------------
// Chat type ChatGPT (Inertia)
// -----------------------------
Route::get('/chat', [ConversationController::class, 'index'])
    ->name('chat.index');

Route::get('/chat/{conversation}', [ConversationController::class, 'show'])
    ->name('chat.show');

// -----------------------------
// API JSON pour le chat (Axios)
// -----------------------------
Route::prefix('api/chat')->group(function () {

    // Liste toutes les conversations
    Route::get('/', [ConversationController::class, 'listJson']);

    // Affiche une conversation spécifique avec ses messages
    Route::get('/{conversation}', [ConversationController::class, 'showJson']);

    // Crée une nouvelle conversation
    Route::post('/', [ConversationController::class, 'storeJson']);

    // Envoie un message dans une conversation
    Route::post('/{conversation}/messages', [MessageController::class, 'store']);

    // Met à jour le modèle utilisé pour une conversation
    Route::patch('/{conversation}/model', [ConversationController::class, 'updateModel']);

    // ✅ Génération automatique du titre
    Route::post('/{conversation}/generate-title', [ConversationController::class, 'generateTitle']);
});

// -----------------------------
// Page par défaut
// -----------------------------
Route::get('/', function () {
    return redirect()->route('chat.index');
});

// -----------------------------
// Test Inertia
// -----------------------------
Route::get('/hello', function () {
    return Inertia::render('Hello', [
        'message' => 'Hello World from Laravel + Inertia!'
    ]);
});
