<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getCurrentByCity(string $city): array
    {
        $key = config('services.openweather.key');
        if (!$key || $key === 'TU_API_KEY_AQUI') {
            return ['ok' => false, 'error' => 'Falta OPENWEATHER_API_KEY'];
        }

        $res = Http::timeout(10)
            ->withoutVerifying() // Para desarrollo local en Windows
            ->get('https://api.openweathermap.org/data/2.5/weather', [
            'q' => $city,
            'appid' => $key,
            'units' => config('services.openweather.units', 'metric'),
            'lang' => config('services.openweather.lang', 'es'),
        ]);

        if (!$res->ok()) {
            return [
                'ok' => false, 
                'error' => 'OpenWeather error: ' . $res->body(), 
                'status' => $res->status(), 
                'body' => $res->body()
            ];
        }

        $json = $res->json();

        return [
            'ok' => true,
            'temp' => data_get($json, 'main.temp'),
            'condition' => data_get($json, 'weather.0.description'),
            'raw' => $json,
        ];
    }

    /**
     * Obtener clima (método legacy para compatibilidad)
     */
    public function getWeather(string $city): array
    {
        return $this->getCurrentByCity($city);
    }
}
