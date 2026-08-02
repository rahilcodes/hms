<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;
use App\Models\MaintenanceLog;
use App\Models\LostFoundItem;
use App\Models\Asset;
use App\Models\LaundryBatch;
use App\Models\LaundryVendor;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class OperationsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $rooms = Room::all();

        // Ensure a user exists for LostFound found_by_user_id
        $user = User::firstOrCreate(
            ['email' => 'staff@hotel.com'],
            [
                'name' => 'General Staff',
                'password' => Hash::make('password'),
                'phone' => '1234567890'
            ]
        );

        // 1. Maintenance Logs (Room Issues)
        if (class_exists(\App\Models\MaintenanceLog::class) && $rooms->count() > 0) {
            for ($i = 0; $i < 10; $i++) {
                MaintenanceLog::create([
                    'room_id' => $rooms->random()->id,
                    'description' => $faker->randomElement(['AC not cooling', 'Tap leaking', 'TV remote missing', 'Light bulb fused']),
                    'technician_name' => 'Housekeeping Staff',
                    'status' => $faker->randomElement(['pending', 'in-progress', 'completed']), // Check table definition, usually hyphenated string or enum
                ]);
            }
        }

        // 2. Lost & Found
        if (class_exists(\App\Models\LostFoundItem::class) && $rooms->count() > 0) {
            for ($i = 0; $i < 10; $i++) {
                LostFoundItem::create([
                    'item_name' => $faker->randomElement(['iPhone Charger', 'Sunglasses', 'Wallet', 'Kid\'s Toy']),
                    'description' => 'Found under the bed',
                    'found_location' => 'Room ' . $rooms->random()->room_number,
                    'found_date' => now()->subDays(rand(1, 20)),
                    'status' => $faker->randomElement(['found', 'claimed']), // Check enum -> found, claimed, disposed, donated
                    'found_by_user_id' => $user->id,
                    'hotel_id' => 1, // Default assuming single hotel
                ]);
            }
        }

        // 3. Assets
        if (class_exists(\App\Models\Asset::class)) {
            $assets = [
                ['name' => 'Dell PC', 'type' => 'Electronics', 'qty' => 5, 'value' => 50000], // type column from migration
                ['name' => 'Vacuum Cleaner', 'type' => 'Cleaning', 'qty' => 3, 'value' => 15000],
                ['name' => 'Printer', 'type' => 'Electronics', 'qty' => 2, 'value' => 20000],
                ['name' => 'Lobby Sofa', 'type' => 'Furniture', 'qty' => 2, 'value' => 45000],
            ];

            foreach ($assets as $asset) {
                // Migration 2026_01_30_061021 defines: name, type, brand, model, purchase_date, qr_code
                // Need to match columns.
                // Assuming basic match or updateOrCreate
                // QR Code unique
                Asset::create([
                    'name' => $asset['name'],
                    'type' => $asset['type'],
                    'brand' => 'Generic',
                    'purchase_date' => now()->subMonths(rand(6, 24)),
                    'status' => 'active',
                    'qr_code' => \Illuminate\Support\Str::random(10),
                ]);
            }
        }

        // 4. Laundry Batches
        if (class_exists(\App\Models\LaundryBatch::class)) {
            $vendors = LaundryVendor::all();
            if ($vendors->count() > 0) {
                // Status: out, processing, returned, completed
                for ($i = 0; $i < 5; $i++) {
                    LaundryBatch::create([
                        'vendor_id' => $vendors->random()->id,
                        'batch_number' => 'LND-' . rand(1000, 9999), // Unique required
                        'sent_date' => now()->subDays(rand(2, 5)),
                        'status' => $faker->randomElement(['out', 'processing', 'returned']),
                        // 'total_items' => rand(10, 50), // Not in table? create_linen_laundry_tables uses laundry_items table for details?
                        // Table has: total_cost, notes. ITEMS likely not a column but a relation?
                        // Wait, create_housekeeping_tables had 'items' JSON. create_linen_laundry_tables DOES NOT.
                        // It uses laundry_items table.
                        // So I should seed LaundryItems too if I want items.
                    ]);
                }
            }
        }
    }
}
