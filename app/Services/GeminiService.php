<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    public function ask(string $prompt): string
    {
        $key = config('services.groq.key');
        $model = config('services.groq.model');

        if (!$key) {
            return "Error: No se ha configurado la API Key de Groq";
        }

        $url = "https://api.groq.com/openai/v1/chat/completions";

        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$key}",
                    'Content-Type' => 'application/json',
                ])
                ->post($url, [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 1024,
                    'temperature' => 0.7,
                ]);

            if ($response->status() === 429) {
                return "La API ha alcanzado su límite. Espera unos segundos e intenta de nuevo.";
            }

            if ($response->status() === 401) {
                return "Error de autenticación. Verifica la API Key en .env";
            }

            if ($response->status() === 502 || $response->status() === 503) {
                return "El servicio de IA está temporalmente no disponible. Intenta en unos segundos.";
            }

            if (!$response->ok()) {
                $error = $response->json()['error']['message'] ?? 'Error desconocido';
                return "Error: {$error}";
            }

            return $response->json()['choices'][0]['message']['content'] ?? "Sin respuesta";
        } catch (\Exception $e) {
            return "Error de conexión: " . $e->getMessage();
        }
    }
}