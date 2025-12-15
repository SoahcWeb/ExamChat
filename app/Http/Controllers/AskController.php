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
        // On peut aussi envoyer la liste des modèles à l'affichage initial si besoin
        $models = $this->askService->getModels();
        return Inertia::render('Ask', [
            'response' => null,
            'models' => $models,
        ]);
    }

    /**
     * Récupère la liste des modèles pour Vue
     */
    public function getModels()
    {
        $models = $this->askService->getModels();

        // Assurer un formatage simple {id: "..."} pour Vue
        $formatted = [];
        if (isset($models['data'])) {
            foreach ($models['data'] as $model) {
                $formatted[] = ['id' => $model['id']];
            }
        }

        return response()->json(['models' => $formatted]);
    }

    /**
     * Traite l'envoi d'une question depuis Vue
     */
    public function ask(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'model' => 'required|string',
        ]);

        $response = $this->askService->sendMessage(
            $request->input('question'),
            $request->input('model')
        );

        // extraire uniquement le texte de l'IA
        $text = $response['choices'][0]['message']['content'] ?? 'Pas de réponse.';

        return response()->json(['response' => $text]);
    }
}
