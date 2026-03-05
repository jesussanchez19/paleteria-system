<?php

namespace App\Http\Controllers;

use App\Models\WeatherSnapshot;

class WeatherController extends Controller
{
    public function index()
    {
        $city = app_setting('business_city', 'Mexico City');
        $today = now()->toDateString();

        $snap = WeatherSnapshot::where('date', $today)
            ->where('city', $city)
            ->first();

        return view('panel.clima', compact('snap', 'city', 'today'));
    }
}