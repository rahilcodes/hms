<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Helper to update or insert
function updateSetting($key, $value)
{
    if (DB::table('site_settings')->where('key', $key)->exists()) {
        DB::table('site_settings')->where('key', $key)->update(['value' => $value, 'updated_at' => now()]);
    } else {
        DB::table('site_settings')->insert([
            'key' => $key,
            'value' => $value,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
    echo "Updated $key: $value\n";
}

// RESET TO TITANIUM DEFAULTS
updateSetting('hotel_name', 'LuxeStay');
updateSetting('hero_heading', "Beyond\nExpectations.");
updateSetting('hero_subheading', 'A sanctuary where timeless elegance meets modern mastery. Your extraordinary journey begins here.');
updateSetting('footer_text', 'Experience the pinnacle of luxury.');

echo "Site Settings Reset Complete.\n";
