<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WeatherSnapshot;
use App\Services\WeatherService;

class WeatherSnapshotDaily extends Command
{
    protected $signature = 'weather:snapshot';
    protected $description = 'Guarda el clima del día (snapshot) según la ciudad configurada';

    public function handle(WeatherService $weather)
    {
        $city = app_setting('business_city', 'Mexico City');
        $today = now()->toDateString();

        $data = $weather->getCurrentByCity($city);

        if (!$data['ok']) {
            $this->error('No se pudo obtener clima: ' . ($data['error'] ?? ''));
            return Command::FAILURE;
        }

        WeatherSnapshot::updateOrCreate(
            ['date' => $today, 'city' => $city],
            [
                'temp' => (float) $data['temp'],
                'condition' => (string) $data['condition'],
                'raw' => $data['raw'],
            ]
        );

        audit_log('weather.snapshot', 'weather', null, [
            'city' => $city,
            'temp' => $data['temp'],
            'condition' => $data['condition'],
        ]);

        $this->info("Snapshot guardado: {$city} {$data['temp']}°C {$data['condition']}");
        return Command::SUCCESS;
    }
}
