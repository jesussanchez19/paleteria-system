<?php

namespace App\Http\Controllers;

use App\Models\WeatherSnapshot;

class WeatherInsightController extends Controller
{
    public function index()
    {
        $city = app_setting('business_city', 'Mexico City');

        $weather = WeatherSnapshot::where('date', now()->toDateString())
            ->where('city', $city)
            ->first();

        $recommendation = null;

        if ($weather) {

            if ($weather->temp >= 30) {
                $recommendation = "🔥 Día muy caluroso. Se recomienda aumentar producción de paletas y aguas.";
            }

            elseif ($weather->temp >= 25) {
                $recommendation = "☀️ Día cálido. Se recomienda aumentar bebidas frías.";
            }

            elseif ($weather->temp <= 20) {
                $recommendation = "🌥️ Día fresco. Posible baja en ventas de paletas.";
            }

            else {
                $recommendation = "🌤️ Clima templado. Ventas normales esperadas.";
            }

        }

        return view('panel.weather-insight', [
            'weather' => $weather,
            'recommendation' => $recommendation
        ]);
    }
}
