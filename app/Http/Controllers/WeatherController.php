<?php

namespace App\Http\Controllers;

use App\Models\WeatherSnapshot;
use App\Services\WeatherService;

class WeatherController extends Controller
{
    public function index(WeatherService $weatherService)
    {
        $city = app_setting('business_city', 'Mexico City');
        $today = now()->toDateString();

        $snap = WeatherSnapshot::where('date', $today)
            ->where('city', $city)
            ->first();

        // Si no hay snapshot de hoy, obtenerlo en vivo
        if (!$snap) {
            $data = $weatherService->getCurrentByCity($city);
            
            if ($data['ok']) {
                $snap = WeatherSnapshot::create([
                    'date' => $today,
                    'city' => $city,
                    'temp' => (float) $data['temp'],
                    'condition' => (string) $data['condition'],
                    'raw' => $data['raw'],
                ]);

                audit_log('weather.snapshot.ondemand', 'weather', null, [
                    'city' => $city,
                    'temp' => $data['temp'],
                    'condition' => $data['condition'],
                ]);
            }
        }

        return view('panel.clima', compact('snap', 'city', 'today'));
    }
}