<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roomTypes = RoomType::all();

        if ($roomTypes->isEmpty()) {
            return;
        }

        foreach ($roomTypes as $index => $type) {
            $floor = $index + 1;

            // Create 5 rooms per type
            for ($i = 1; $i <= 5; $i++) {
                $roomNumber = ($floor * 100) + $i;

                Room::firstOrCreate(
                    ['room_number' => (string) $roomNumber],
                    [
                        'room_type_id' => $type->id,
                        'floor' => $floor,
                        'status' => 'available',
                        'housekeeping_status' => 'clean',
                        'notes' => 'Auto-generated room',
                    ]
                );
            }
        }
    }
}
