<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'role' => 'required|in:user,assistant',
            'content' => 'required|string',
        ]);

        // 1️⃣ Créer le message utilisateur
        $userMessage = $conversation->messages()->create([
            'role' => 'user',
            'content' => $request->content,
        ]);

        // 2️⃣ Créer le message assistant vide (sera rempli via SSE)
        $assistantMessage = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => '',
        ]);

        // 3️⃣ Retourner les messages + conversation + ID du message assistant
        return response()->json([
            'userMessage' => $userMessage,
            'botMessage' => $assistantMessage,
            'conversation' => $conversation,
        ]);
    }
}
