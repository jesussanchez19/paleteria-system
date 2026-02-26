<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherService
{
    public function getCurrentWeather($lat, $lon)
    {
        $response = Http::get('https://api.open-meteo.com/v1/forecast', [
            'latitude' => $lat,
            'longitude' => $lon,
            'current_weather' => true
        ]);

        return $response->json();
    }
}