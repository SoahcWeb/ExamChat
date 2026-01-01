<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Services\SimpleAskService;
use Illuminate\Http\Request;

class AskController extends Controller
{
    private SimpleAskService $askService;

    public function __construct(SimpleAskService $askService)
    {
        $this->askService = $askService;
    }

    /**
     * Affiche la page principale Inertia
     */
    public function index()
    {
        $models = $this->askService->getModels();

        // Formatage simple pour Vue
        $formattedModels = [];
        if (isset($models['data'])) {
            foreach ($models['data'] as $model) {
                $formattedModels[] = ['id' => $model['id']];
            }
        }

        return Inertia::render('Ask/Index', [
            'models' => $formattedModels,
            'selectedModel' => $formattedModels[0]['id'] ?? null,
            'message' => null,
            'response' => null,
            'error' => null,
        ]);
    }

    /**
     * Traite l'envoi d'une question (Inertia)
     */
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'model' => 'required|string',
        ]);

        $response = $this->askService->sendMessage(
            $request->input('message'),
            $request->input('model')
        );

        $text = $response['choices'][0]['message']['content'] ?? null;

        // Renvoie les props directement à Inertia pour mise à jour du composant
        $models = $this->askService->getModels();
        $formattedModels = [];
        if (isset($models['data'])) {
            foreach ($models['data'] as $model) {
                $formattedModels[] = ['id' => $model['id']];
            }
        }

        return Inertia::render('Ask/Index', [
            'models' => $formattedModels,
            'selectedModel' => $request->input('model'),
            'message' => $request->input('message'),
            'response' => $text,
            'error' => null,
        ]);
    }
}
