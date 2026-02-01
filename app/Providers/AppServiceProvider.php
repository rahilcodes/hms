<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\SiteSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (Schema::hasTable('site_settings')) {
            $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

            // Set defaults if missing (Titanium Fallbacks)
            $defaults = [
                'hotel_name' => 'LuxeStay',
                'bg_color' => '#0f172a',
                'accent_color' => '#2563eb',
                'currency' => '₹',
            ];

            $globalSettings = array_merge($defaults, $settings);

            // Inject into ALL views
            View::share('site_settings', $globalSettings);

            // Also share a helper function/variable for direct access
            // We can bindings or singleton if we want a helper class, 
            // but for now, the array is sufficient for the `site()` helper 
            // (assuming site() helper pulls from this shared data or cache)
        }
    }
}
