<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeminiService
{
    protected $apiKey;
    protected $endpoint;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        $this->endpoint = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $this->apiKey;
    }

    public function recommend($weatherData)
    {
        $temp = $weatherData['current_weather']['temperature'];
        $wind = $weatherData['current_weather']['windspeed'];

        $prompt = "
    Temperatura actual: {$temp}°C
    Viento: {$wind} km/h
    Negocio: Paletería.
    Recomienda productos para maximizar ganancias.
    Responde breve.
    ";

        $response = Http::timeout(15)
            ->withHeaders([
                'Content-Type' => 'application/json'
            ])
            ->post($this->endpoint, [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $prompt]
                        ]
                    ]
                ]
            ]);

        if (!$response->successful()) {
            return "Error HTTP: " . $response->status();
        }

        $data = $response->json();

        if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return "Sin recomendación generada.";
        }

        return $data['candidates'][0]['content']['parts'][0]['text'];
    }
}
