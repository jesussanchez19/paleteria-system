<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use App\Services\GeminiService;

class WeatherController extends Controller
{
    protected $weatherService;
    protected $GeminiService;

    public function __construct(
        WeatherService $weatherService,
        GeminiService $GeminiService
    ) {
        $this->weatherService = $weatherService;
        $this->GeminiService = $GeminiService;
    }

    public function analyze($lat, $lon)
    {
        $weather = $this->weatherService->getCurrentWeather($lat, $lon);
        $recommendation = $this->GeminiService->recommend($weather);

        return response()->json([
            'weather' => $weather['current_weather'],
            'recommendation' => $recommendation
        ]);
    }
}