<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get a setting value from the database with caching
     */
    function setting(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 600, function() use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }
}

if (!function_exists('setting_set')) {
    /**
     * Set a setting value and clear its cache
     */
    function setting_set(string $key, $value, string $type = 'text')
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type]
        );
        Cache::forget("setting_{$key}");
        return true;
    }
}
