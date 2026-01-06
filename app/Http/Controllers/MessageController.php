<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Stocke un message utilisateur et prépare un message assistant vide pour SSE
     */
    public function store(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string',
            'role' => 'required|in:user,assistant',
        ]);

        if ($request->role === 'user') {
            // 1️⃣ Crée le message utilisateur
            $userMessage = $conversation->messages()->create([
                'role' => 'user',
                'content' => $request->content,
            ]);

            // 2️⃣ Crée immédiatement un message assistant vide pour recevoir le stream
            $assistantMessage = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => '', // vide mais pas null
            ]);

            return response()->json([
                'userMessage' => $userMessage,
                'assistantMessage' => $assistantMessage,
            ]);
        }

        // Si c’est déjà un assistant, créer le message normalement
        $message = $conversation->messages()->create([
            'role' => $request->role,
            'content' => $request->content,
        ]);

        return response()->json($message);
    }

    /**
     * 🔹 Sauvegarde un message assistant existant
     */
    public function save(Request $request, Conversation $conversation, Message $message)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        // Met à jour le message existant
        $message->update([
            'content' => $request->content,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
