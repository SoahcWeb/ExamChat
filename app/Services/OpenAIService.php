<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Conversation;

class OpenAIService
{
    /**
     * Streaming réel OpenRouter avec contexte complet de la conversation
     */
    public function streamResponse(int $conversationId, string $userMessage): void
    {
        $apiKey = env('OPENROUTER_API_KEY');
        $model  = env('OPENROUTER_MODEL', 'openai/gpt-4o-mini');

        // 1️⃣ Récupérer l'historique de la conversation
        $conversation = Conversation::findOrFail($conversationId);
        $messages = [];

        // System prompt personnalisé si existant
        $instructions = session('custom_instructions');
        $messages[] = [
            'role' => 'system',
            'content' => $instructions ?? 'Tu es un assistant intelligent, clair et utile.'
        ];

        // Ajouter tous les messages existants de la conversation
        $conversationMessages = Message::where('conversation_id', $conversationId)
            ->orderBy('created_at')
            ->get();

        foreach ($conversationMessages as $msg) {
            $messages[] = [
                'role' => $msg->role,
                'content' => $msg->content
            ];
        }

        // Ajouter le nouveau message utilisateur
        $messages[] = [
            'role' => 'user',
            'content' => $userMessage
        ];

        // 2️⃣ Enregistrer le message utilisateur
        $userMessageModel = Message::create([
            'conversation_id' => $conversationId,
            'role' => 'user',
            'content' => $userMessage
        ]);

        // 3️⃣ Préparer le payload pour OpenRouter
        $payload = [
            'model' => $model,
            'stream' => true,
            'messages' => $messages,
        ];

        $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');

        $fullAssistantContent = ''; // Stocke le contenu complet du bot

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer {$apiKey}",
                "Content-Type: application/json",
                "HTTP-Referer: http://localhost",
                "X-Title: ExamChat",
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_WRITEFUNCTION => function ($ch, $data) use (&$fullAssistantContent) {
                static $buffer = '';
                $buffer .= $data;

                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    if ($line === '' || $line === 'data: [DONE]') continue;

                    if (str_starts_with($line, 'data: ')) {
                        $json = json_decode(substr($line, 6), true);
                        $token = $json['choices'][0]['delta']['content'] ?? null;

                        if ($token) {
                            // 4️⃣ Stocker localement pour la DB plus tard
                            $fullAssistantContent .= $token;

                            // 5️⃣ Envoyer le token au frontend
                            echo "data: " . json_encode(['token' => $token]) . "\n\n";
                            ob_flush();
                            flush();
                        }
                    }
                }

                return strlen($data);
            },
        ]);

        curl_exec($ch);

        if (curl_errno($ch)) {
            echo "data: " . json_encode(['token' => 'cURL Error: ' . curl_error($ch)]) . "\n\n";
            ob_flush();
            flush();
        }

        curl_close($ch);

        // 6️⃣ Une seule mise à jour DB à la fin du flux
        Message::create([
            'conversation_id' => $conversationId,
            'role' => 'assistant',
            'content' => $fullAssistantContent
        ]);

        // 7️⃣ Signaler la fin du flux SSE
        echo "event: end\n";
        echo "data: {}\n\n";
        ob_flush();
        flush();
    }
}
