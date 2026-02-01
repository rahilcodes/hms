<?php

use App\Models\SiteSetting;

if (!function_exists('site')) {
    function site(string $key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            // 🚀 TITANIUM OPTIMIZATION: Load ALL settings in 1 query
            // Instead of N+1 queries, we fetch everything once and cache it in memory.
            try {
                $settings = \App\Models\SiteSetting::all()->pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                $settings = [];
            }
        }

        return $settings[$key] ?? $default;
    }
}
