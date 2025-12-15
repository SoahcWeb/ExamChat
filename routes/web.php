<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AskController;
use Inertia\Inertia;
use App\Services\SimpleAskService;
use Illuminate\Http\Request;

// Route existante vers ton contrôleur principal
Route::get('/', [AskController::class, 'index']);

// Route Hello World Inertia
Route::get('/hello', function () {
    return Inertia::render('Hello', [
        'message' => 'Hello World from Laravel + Inertia!'
    ]);
});

// -----------------------------
// Routes de test SimpleAskService (Blade)
// -----------------------------

Route::get('/test-simpleask', function () {
    return view('prompts.system');
});

Route::post('/test-simpleask', function (Request $request, SimpleAskService $askService) {
    $request->validate([
        'question' => 'required|string',
    ]);

    $model = 'gpt-4o-mini';
    $response = $askService->sendMessage($request->question, $model);

    // extraire uniquement le texte de l'IA
    $text = $response['choices'][0]['message']['content'] ?? 'Pas de réponse.';

    return view('prompts.system', ['responseText' => $text]);
})->name('ask.test');

// -----------------------------
// Routes pour Vue + AskController
// -----------------------------

// Page principale Vue
Route::get('/ask', [AskController::class, 'index'])->name('ask.index');

// Envoi de question (POST)
Route::post('/ask', [AskController::class, 'ask'])->name('ask.send');

// Récupération des modèles disponibles pour Vue
Route::get('/ask/models', [AskController::class, 'getModels']);
