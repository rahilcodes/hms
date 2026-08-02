<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'hotel_name' => 'Demo Hotel',
            'hotel_phone' => '9999999999',
            'hero_heading' => 'Book Your Stay Directly',
            'hero_subheading' => 'Best Price • Pay at Hotel • Instant Confirmation',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
