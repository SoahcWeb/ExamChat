<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class SimpleAskService
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $this->baseUrl = config('services.openrouter.base_url', 'https://openrouter.ai/api/v1');

        if (empty($this->apiKey)) {
            throw new Exception('OpenRouter API key is not set. Please check your .env file and config/services.php.');
        }
    }

    /**
     * Lister les modèles disponibles
     *
     * @return array
     */
    public function getModels(): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->get($this->baseUrl . '/models');

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Failed to fetch models', 'status' => $response->status()];

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Envoyer une question simple à l’API OpenRouter
     *
     * @param string $message
     * @param string|null $model
     * @return array
     */
    public function sendMessage(string $message, ?string $model = 'gpt-4o-mini'): array
    {
        if (empty($message)) {
            return ['error' => 'Message cannot be empty'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => $message,
                    ]
                ],
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Failed to send message', 'status' => $response->status()];

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
