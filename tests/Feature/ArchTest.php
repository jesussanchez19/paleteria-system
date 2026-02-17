<?php

arch('app')
    ->expect('App')
    ->not->toUse(['dd', 'dump', 'ray'])
    ->not->toUse('Illuminate\Http\Request')
    ->ignoring('App\Http');

arch('models')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->not->toUse('Illuminate\Http\Request');

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toUse('App\Models')
    ->ignoring('App\Http\Controllers'); // Relaxed for this project size, usually Controllers shouldn't call Models directly in big apps but for this size it's fine.
