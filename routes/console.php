<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cierre automático de caja a las 5pm
Schedule::command('cash:close')->dailyAt('17:00');

// Snapshots de clima (múltiples capturas para tener datos actualizados)
Schedule::command('weather:snapshot')->dailyAt('08:00'); // Apertura
Schedule::command('weather:snapshot')->dailyAt('12:00'); // Mediodía
Schedule::command('weather:snapshot')->dailyAt('16:00'); // Tarde

// Backup automático de base de datos diario (23:00)
Schedule::command('backup:db')->dailyAt('23:00');

// Limpieza de backups antiguos (>14 días) a las 23:10
Schedule::call(function () {
    $dir = storage_path('backups');
    if (!is_dir($dir)) return;

    $files = glob($dir . DIRECTORY_SEPARATOR . '*.sql');
    $keepDays = 14;

    foreach ($files as $file) {
        if (filemtime($file) < now()->subDays($keepDays)->timestamp) {
            @unlink($file);
        }
    }
})->dailyAt('23:10');
