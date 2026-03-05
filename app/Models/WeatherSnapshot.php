<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeatherSnapshot extends Model
{
    protected $fillable = ['date', 'city', 'temp', 'condition', 'raw'];

    protected $casts = [
        'date' => 'date',
        'raw' => 'array',
    ];
}
