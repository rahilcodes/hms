<?php

namespace Database\Seeders;

use App\Models\BanquetHall;
use App\Models\SiteSetting;
use App\Models\TravelAgent;
use Illuminate\Database\Seeder;

class IndiaOpsSeeder extends Seeder
{
    public function run(): void
    {
        $hotelId = \App\Models\Hotel::first()?->id ?? 1;

        // Banquet halls
        foreach ([
            ['name' => 'Grand Ballroom', 'capacity' => 400, 'base_rent' => 75000],
            ['name' => 'Lotus Hall', 'capacity' => 150, 'base_rent' => 30000],
        ] as $hall) {
            BanquetHall::updateOrCreate(['name' => $hall['name']], $hall + ['hotel_id' => $hotelId, 'is_active' => true]);
        }

        // Travel agents
        foreach ([
            ['name' => 'Ramesh Gupta', 'agency_name' => 'Himalaya Holidays', 'phone' => '9812345670', 'email' => 'ramesh@himalayaholidays.in', 'commission_percent' => 10],
            ['name' => 'Sana Khan', 'agency_name' => 'Valley Tours & Travels', 'phone' => '9812345671', 'email' => 'sana@valleytours.in', 'commission_percent' => 12.5],
        ] as $agent) {
            TravelAgent::updateOrCreate(['phone' => $agent['phone']], $agent + ['hotel_id' => $hotelId, 'is_active' => true]);
        }

        // India settings demo defaults (owner replaces with real values)
        $settings = [
            'gst_enabled' => ['1', 'boolean'],
            'hotel_gstin' => ['01ABCDE1234F1Z5', null],
            'hotel_fssai' => ['10012345678901', null],
            'hotel_upi_vpa' => ['demohotel@okhdfcbank', null],
            'hotel_upi_name' => ['Demo Hotel', null],
            'google_review_link' => ['https://g.page/r/demo-hotel/review', null],
            'owner_whatsapp' => ['', null],
            'day_close_lock' => ['1', 'boolean'],
        ];
        foreach ($settings as $key => [$value, $type]) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value, 'type' => $type]);
        }
    }
}
