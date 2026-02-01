<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    public function index()
    {
        // Return key-value pair for easy access in view: $settings['hotel_name']
        $dbSettings = SiteSetting::all()->pluck('value', 'key');

        $defaults = collect([
            'meta_title_format' => '{{page}} - {{site}}',
            'checkin_time' => '12:00 PM',
            'checkout_time' => '11:00 AM',
            'primary_color' => '#2563eb',
            'button_primary_color' => '#2563eb',
            'footer_text' => '© 2026 Hotel Name. All rights reserved.',
            'payment_mode' => 'hotel_only', // hotel_only, online_only, partial_deposit
            'deposit_type' => 'percentage', // percentage, fixed
            'deposit_value' => '100',
        ]);

        $settings = $dbSettings->union($defaults);

        return view('admin.site-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $inputSettings = $request->input('settings', []);

        // 1. Bulk handle regular settings
        foreach ($inputSettings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // 2. Handle file uploads
        $fileSettings = ['logo', 'site_logo', 'hero_image'];
        foreach ($fileSettings as $key) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('site', 'public');
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $path, 'type' => 'image']
                );
            }
        }

        // 3. Special handling for booleans (if unchecked, they aren't in $inputSettings)
        // We look for keys that we KNOW are booleans and check their presence.
        // 3. Special handling for booleans (if unchecked, they aren't in $inputSettings)
        // We look for keys that we KNOW are booleans and check their presence.
        $booleanKeys = [
            'maintenance_mode',
            'show_whatsapp_cta',
            'payment_feature_enabled',
            'home_hero_enabled',
            'home_search_enabled',
            'home_story_enabled',
            'home_rooms_enabled',
            'home_offers_enabled',
            'home_lifestyle_enabled',
            'home_amenities_enabled',
            'home_stats_enabled',
            'home_reviews_enabled',
            'home_gallery_enabled',
            'home_faq_enabled',
            'home_map_enabled'
        ];
        foreach ($booleanKeys as $key) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => isset($inputSettings[$key]) ? '1' : '0', 'type' => 'boolean']
            );
        }

        // Clear view cache so changes reflect immediately if cached
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return back()->with('success', 'Website settings updated successfully');
    }
}
