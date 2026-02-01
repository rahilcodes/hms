<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;
use App\Models\Hotel;

class FeatureSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            ['name' => 'Front Desk', 'slug' => 'front-desk', 'description' => 'Check-in, Check-out and Dashboard'],
            ['name' => 'Housekeeping', 'slug' => 'housekeeping', 'description' => 'Room status and maintenance logs'],
            ['name' => 'Financials', 'slug' => 'financials', 'description' => 'Ledger, Invoices and Night Audit'],
            ['name' => 'CRM', 'slug' => 'crm', 'description' => 'Guest profiles and history'],
            ['name' => 'Inventory', 'slug' => 'inventory', 'description' => 'Assets, Linen and Laundry'],
        ];

        foreach ($features as $f) {
            Feature::updateOrCreate(['slug' => $f['slug']], $f);
        }

        // Attach all to Demo Hotel
        $hotel = Hotel::first();
        if ($hotel) {
            $allFeatures = Feature::all();
            $hotel->features()->sync($allFeatures); // Enable all by default
        }
    }
}
