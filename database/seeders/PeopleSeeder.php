<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Company;
use App\Models\LaundryVendor;
use App\Models\Hotel;
use Illuminate\Support\Facades\Hash;

class PeopleSeeder extends Seeder
{
    public function run(): void
    {
        $hotel = Hotel::first(); // Should be created by HotelDataSeeder
        if (!$hotel) {
            // Fallback if running individually
            $hotel = Hotel::create([
                'name' => 'Demo Hotel',
                'slug' => 'demo-hotel-fallback',
                'phone' => '9999999999',
                'email' => 'contact@demohotel.com',
                'address' => '123, Paradise Street, Goa',
            ]);
        }

        // 1. Admins / Staff
        $admins = [
            [
                'name' => 'Manager',
                'email' => 'admin@hotel.com',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ],
            [
                'name' => 'Receptionist 1',
                'email' => 'reception@hotel.com',
                'password' => Hash::make('password'),
                'role' => 'receptionist',
            ],
            [
                'name' => 'Housekeeping Lead',
                'email' => 'hk@hotel.com',
                'password' => Hash::make('password'),
                'role' => 'housekeeping',
            ],
            [
                'name' => 'Revenue Manager',
                'email' => 'revenue@hotel.com',
                'password' => Hash::make('password'),
                'role' => 'revenue',
            ],
        ];

        foreach ($admins as $admin) {
            $admin['hotel_id'] = $hotel->id;
            Admin::updateOrCreate(['email' => $admin['email']], $admin);
        }

        // 2. Companies (Corporate)
        $companies = [
            [
                'name' => 'Acme Corp',
                'email' => 'contact@acmecorp.com',
                'phone' => '9876543210',
                'gst_number' => '27ABCDE1234F1Z5',
                'address' => '123 Business Park, Mumbai',
                'is_active' => true
            ],
            [
                'name' => 'TechSolutions Inc',
                'email' => 'admin@techsolutions.com',
                'phone' => '8765432109',
                'gst_number' => '27FGHIJ5678K1Z5',
                'address' => '456 Tech Park, Bangalore',
                'is_active' => true
            ],
            [
                'name' => 'Global Logistics',
                'email' => 'ops@globallogistics.com',
                'phone' => '7654321098',
                'gst_number' => '27KLMNO9012P1Z5',
                'address' => '789 Logistics Hub, Delhi',
                'is_active' => false
            ],
        ];

        foreach ($companies as $comp) {
            Company::updateOrCreate(['email' => $comp['email']], $comp);
        }

        // 3. Laundry Vendors
        $vendors = [
            [
                'name' => 'Sparkle Cleaners',
                'contact_person' => 'Rajesh Kumar',
                'phone' => '9988776655',
                'email' => 'info@sparkle.com',
                'address' => 'Near Market, City Center',
                // 'service_type' => 'washing_ironing' // Removed if not in schema
            ],
            [
                'name' => 'Quick Wash Dry Cleaners',
                'contact_person' => 'Suresh Dry',
                'phone' => '8877665544',
                'email' => 'quick@wash.com',
                'address' => 'Industrial Area, Phase 1',
            ],
        ];

        foreach ($vendors as $ven) {
            LaundryVendor::updateOrCreate(['email' => $ven['email']], $ven);
        }
    }
}
