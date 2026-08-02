<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Service;
use App\Models\Coupon;
use App\Models\LinenType;
use App\Models\Hotel;

class HotelDataSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Create Hotel
        $hotel = Hotel::create([
            'name' => 'Demo Hotel',
            'slug' => 'demo-hotel',
            'phone' => '9999999999',
            'email' => 'contact@demohotel.com',
            'address' => '123, Paradise Street, Goa',
        ]);

        // 1. Room Types
        $roomTypes = [
            [
                'name' => 'Standard Single',
                'base_price' => 2500,
                'base_occupancy' => 1,
                'max_extra_persons' => 0,
                'total_rooms' => 5, // Static count
                'description' => 'Cozy room for solo travelers with all basic amenities.',
                'amenities' => ['Wi-Fi', 'TV', 'AC', 'Breakfast'],
                'gallery_json' => [],
            ],
            [
                'name' => 'Deluxe Double',
                'base_price' => 4500,
                'base_occupancy' => 2,
                'max_extra_persons' => 1,
                'extra_person_price' => 1000,
                'total_rooms' => 5,
                'description' => 'Spacious room with a king-size bed and city view.',
                'amenities' => ['Wi-Fi', 'TV', 'AC', 'Mini Bar', 'Breakfast'],
                'gallery_json' => [],
            ],
            [
                'name' => 'Executive Suite',
                'base_price' => 7500,
                'base_occupancy' => 2,
                'max_extra_persons' => 2,
                'extra_person_price' => 1500,
                'total_rooms' => 5,
                'description' => 'Luxury suite with separate living area and premium service.',
                'amenities' => ['Wi-Fi', 'TV', 'AC', 'Mini Bar', 'Bathtub', 'Breakfast', 'Lounge Access'],
                'gallery_json' => [],
            ],
            [
                'name' => 'Family Room',
                'base_price' => 6000,
                'base_occupancy' => 4,
                'max_extra_persons' => 2,
                'extra_person_price' => 1200,
                'total_rooms' => 5,
                'description' => 'Large room with two queen beds, perfect for families.',
                'amenities' => ['Wi-Fi', 'TV', 'AC', 'Breakfast', 'Extra Bed'],
                'gallery_json' => [],
            ],
        ];

        foreach ($roomTypes as $rt) {
            $rt['hotel_id'] = $hotel->id;
            // Handle amenities array casting if model doesn't cast automatically in create, but model has cast 'amenities' => 'array'
            RoomType::updateOrCreate(['name' => $rt['name']], $rt);
        }

        // 2. Rooms
        $types = RoomType::all();
        $floors = [1, 2, 3, 4];

        foreach ($types as $type) {
            // Create rooms based on total_rooms/5 logic
            for ($i = 1; $i <= 5; $i++) {
                $floor = $floors[array_rand($floors)];
                $roomNumber = $floor . sprintf('%02d', $i + ($type->id * 10)); // pseudo-unique number

                Room::updateOrCreate(['room_number' => $roomNumber], [
                    'room_type_id' => $type->id,
                    'floor' => $floor,
                    'status' => 'available',
                    'housekeeping_status' => 'clean',
                    'notes' => 'Regular inspection done.',
                ]);
            }
        }

        // 3. Upsell Services
        $services = [
            ['name' => 'Airport Pickup', 'price' => 1500],
            ['name' => 'Airport Drop', 'price' => 1500],
            ['name' => 'Extra Bed', 'price' => 1000],
            ['name' => 'Spa Package', 'price' => 3000],
            ['name' => 'Candlelight Dinner', 'price' => 2500],
            ['name' => 'Late Checkout (2 Hours)', 'price' => 500],
        ];

        foreach ($services as $svc) {
            // Removed 'type' key as it does not exist in schema
            Service::updateOrCreate(['name' => $svc['name']], $svc);
        }

        // 4. Coupons
        $coupons = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'min_booking_value' => 0,
                'valid_from' => now()->subDays(10),
                'valid_until' => now()->addDays(365),
                'description' => '10% off for new guests',
                'status' => 'active'
            ],
            [
                'code' => 'FLAT500',
                'type' => 'fixed',
                'value' => 500,
                'min_booking_value' => 3000,
                'valid_from' => now()->subDays(10),
                'valid_until' => now()->addDays(365),
                'description' => 'Flat 500 off on bookings above 3000',
                'status' => 'active'
            ],
            [
                'code' => 'SUMMER25',
                'type' => 'percentage',
                'value' => 25,
                'min_booking_value' => 0,
                'valid_from' => now()->subMonths(1),
                'valid_until' => now()->subDays(1), // Expired
                'description' => 'Summer sale ended',
                'status' => 'expired'
            ],
        ];

        foreach ($coupons as $coupon) {
            $data = [
                'code' => $coupon['code'],
                'type' => $coupon['type'],
                'value' => $coupon['value'],
                'min_spend' => $coupon['min_booking_value'],
                'starts_at' => $coupon['valid_from'],
                'expires_at' => $coupon['valid_until'],
                'is_active' => $coupon['status'] === 'active',
            ];
            // Description not in table
            Coupon::updateOrCreate(['code' => $coupon['code']], $data);
        }

        // 5. Linen Types
        $linenTypes = [
            ['name' => 'Bed Sheet (King)', 'total_stock' => 100, 'category' => 'bedding'],
            ['name' => 'Bed Sheet (Single)', 'total_stock' => 100, 'category' => 'bedding'],
            ['name' => 'Pillow Case', 'total_stock' => 200, 'category' => 'bedding'],
            ['name' => 'Bath Towel', 'total_stock' => 150, 'category' => 'bath'],
            ['name' => 'Hand Towel', 'total_stock' => 150, 'category' => 'bath'],
            ['name' => 'Bath Mat', 'total_stock' => 80, 'category' => 'bath'],
            ['name' => 'Duvet Cover', 'total_stock' => 80, 'category' => 'bedding'],
        ];

        foreach ($linenTypes as $lt) {
            LinenType::updateOrCreate(['name' => $lt['name']], $lt);
        }

        // 6. Subscription (Dummy Pro Plan)
        // Ensure hotel exists from step 0 variable, or fetch first
        $hotel = Hotel::first();
        if ($hotel) {
            \App\Models\Subscription::updateOrCreate(
                ['hotel_id' => $hotel->id],
                [
                    'plan_name' => 'Pro Plan',
                    'price' => 4999.00,
                    'billing_cycle' => 'monthly',
                    'starts_at' => now()->subDays(15), // Started 15 days ago
                    'next_billing_date' => now()->addDays(15), // Due in 15 days
                    'status' => 'active',
                    'features_snapshot' => ['all_features_enabled' => true],
                ]
            );
        }
    }
}
