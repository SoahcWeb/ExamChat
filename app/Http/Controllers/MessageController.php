<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'role' => 'required|in:user,assistant',
            'content' => 'required|string',
            'model_used' => 'nullable|string', // nouveau
        ]);

        // 1️⃣ Créer le message utilisateur
        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->content,
        ]);

        // 2️⃣ Mettre à jour le modèle choisi pour cette conversation
        if ($request->model_used) {
            $conversation->update(['model_used' => $request->model_used]);
        }

        // 3️⃣ Générer la réponse IA via OpenRouter
        $assistantContent = 'Désolé, je n’ai pas pu répondre.';
        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                'Content-Type' => 'application/json',
            ])->post(env('OPENROUTER_BASE_URL') . '/chat/completions', [
                'model' => $request->model_used ?? ($conversation->model_used ?? 'gpt-3.5-turbo'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Tu es un assistant utile et concis.'],
                    ['role' => 'user', 'content' => $request->content],
                ],
            ]);

            $data = $res->json();
            if (isset($data['choices'][0]['message']['content'])) {
                $assistantContent = $data['choices'][0]['message']['content'];
            }
        } catch (\Exception $e) {
            \Log::error('Erreur OpenRouter : ' . $e->getMessage());
        }

        // 4️⃣ Créer le message assistant
        $botMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $assistantContent,
        ]);

        // 5️⃣ Générer automatiquement un titre si c’est le premier message
        if (!$conversation->title || $conversation->title === 'Nouvelle conversation') {
            try {
                $titleRes = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
                    'Content-Type' => 'application/json',
                ])->post(env('OPENROUTER_BASE_URL') . '/chat/completions', [
                    'model' => $request->model_used ?? ($conversation->model_used ?? 'gpt-3.5-turbo'),
                    'messages' => [
                        ['role' => 'system', 'content' => 'Tu es un assistant qui génère un titre concis pour une conversation à partir du premier message.'],
                        ['role' => 'user', 'content' => $request->content],
                    ],
                ]);
                $titleData = $titleRes->json();
                if (isset($titleData['choices'][0]['message']['content'])) {
                    $conversation->update(['title' => substr($titleData['choices'][0]['message']['content'], 0, 100)]);
                }
            } catch (\Exception $e) {
                \Log::error('Erreur génération titre : ' . $e->getMessage());
            }
        }

        // 6️⃣ Retourner les messages + conversation mise à jour
        return response()->json([
            'userMessage' => $userMessage,
            'botMessage' => $botMessage,
            'conversation' => $conversation,
        ]);
    }
}
