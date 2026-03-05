<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cierre automático de caja a las 5pm
Schedule::command('cash:close')->dailyAt('17:00');

// Snapshot de clima diario (08:35 después de apertura)
Schedule::command('weather:snapshot')->dailyAt('08:35');
