<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConversationController extends Controller
{
    // -----------------------------
    // Vues Inertia (HTML)
    // -----------------------------

    /**
     * Page principale du chat (Inertia)
     */
    public function index()
    {
        $conversations = Conversation::orderBy('updated_at', 'desc')->get();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'activeConversation' => null,
        ]);
    }

    /**
     * Affichage d'une conversation spécifique (Inertia)
     */
    public function show(Conversation $conversation)
    {
        $conversation->load('messages');

        $conversations = Conversation::orderBy('updated_at', 'desc')->get();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
        ]);
    }

    // -----------------------------
    // API JSON pour Axios
    // -----------------------------

    /**
     * Liste toutes les conversations (JSON)
     */
    public function listJson()
    {
        $conversations = Conversation::orderBy('updated_at', 'desc')->get();
        return response()->json($conversations);
    }

    /**
     * Affiche une conversation avec ses messages (JSON)
     */
    public function showJson(Conversation $conversation)
    {
        $conversation->load('messages');
        return response()->json($conversation);
    }

    /**
     * Crée une nouvelle conversation (JSON)
     */
    public function storeJson(Request $request)
    {
        $conversation = Conversation::create([
            'title' => $request->input('title', 'Nouvelle conversation'),
            'user_id' => null,
            'model_used' => null,
        ]);

        return response()->json($conversation);
    }

    /**
     * Met à jour le modèle choisi pour une conversation et l'utilisateur
     */
    public function updateModel(Request $request, Conversation $conversation)
    {
        $request->validate([
            'model_used' => 'required|string',
        ]);

        $conversation->update([
            'model_used' => $request->model_used,
        ]);

        if ($request->user()) {
            $request->user()->update([
                'model_used' => $request->model_used,
            ]);
        }

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->title,
            'model_used' => $conversation->model_used,
        ]);
    }

    /**
     * ✅ Génération automatique du titre de conversation
     */
    public function generateTitle(Request $request, Conversation $conversation)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $content = trim($request->content);

        $title = mb_substr($content, 0, 40);
        if (mb_strlen($content) > 40) {
            $title .= '...';
        }

        $conversation->update([
            'title' => $title,
        ]);

        return response()->json([
            'id' => $conversation->id,
            'title' => $title,
        ]);
    }

    /**
     * 🗑 Supprime une conversation (JSON)
     */
    public function destroy(Conversation $conversation)
    {
        $conversation->messages()->delete(); // Supprime tous les messages liés
        $conversation->delete();             // Supprime la conversation

        return response()->json([
            'success' => true,
            'id' => $conversation->id,
        ]);
    }
}
