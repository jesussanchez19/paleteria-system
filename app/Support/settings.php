<?php

use App\Models\Setting;

if (! function_exists('app_setting')) {
    function app_setting(string $key, $default = null)
    {
        $row = Setting::where('key', $key)->first();
        return $row?->value ?? $default;
    }
}
