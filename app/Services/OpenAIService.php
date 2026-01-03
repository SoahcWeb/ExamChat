<?php

namespace App\Services;

class OpenAIService
{
    /**
     * Streaming réel OpenRouter (token par token)
     */
    public function streamResponse(int $conversationId, string $userMessage): \Generator
    {
        $apiKey = env('OPENROUTER_API_KEY');
        $model  = env('OPENROUTER_MODEL', 'openai/gpt-4o-mini');

        $payload = [
            'model' => $model,
            'stream' => true,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Tu es un assistant intelligent, clair et utile.'
                ],
                [
                    'role' => 'user',
                    'content' => $userMessage
                ]
            ],
        ];

        $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');

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
            CURLOPT_WRITEFUNCTION => function ($ch, $data) {
                static $buffer = '';

                $buffer .= $data;

                while (($pos = strpos($buffer, "\n")) !== false) {
                    $line = trim(substr($buffer, 0, $pos));
                    $buffer = substr($buffer, $pos + 1);

                    if ($line === '' || $line === 'data: [DONE]') {
                        continue;
                    }

                    if (str_starts_with($line, 'data: ')) {
                        $json = json_decode(substr($line, 6), true);

                        $token = $json['choices'][0]['delta']['content'] ?? null;

                        if ($token) {
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
        curl_close($ch);

        yield from [];
    }
}
