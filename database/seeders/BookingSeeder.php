<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\BookingGuest;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\Company;
use App\Models\Service;
use App\Models\Hotel;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $hotel = Hotel::first();
        if (!$hotel)
            return; // Should exist

        $roomTypes = RoomType::all();
        $companies = Company::all();
        $allRooms = Room::all()->groupBy('room_type_id');
        $services = Service::all();

        // Helper to create guest
        // Fixed $isPrimary lint
        $createGuest = function ($booking, $isPrimary = true) use ($faker) {
            BookingGuest::create([
                'booking_id' => $booking->id,
                'name' => $faker->name,
                'phone' => $faker->phoneNumber,
                'email' => $faker->safeEmail,
                'nationality' => 'Indian',
                'address' => $faker->address,
                'purpose_of_visit' => $faker->randomElement(['Business', 'Leisure', 'Family']),
                'tags' => $isPrimary && rand(0, 1) ? ['VIP', 'Returning'] : [],
            ]);
        };

        // 1. PAST CHECKED OUT BOOKINGS
        for ($i = 0; $i < 10; $i++) {
            $checkIn = Carbon::today()->subDays(rand(5, 30));
            $duration = rand(1, 5);
            $checkOut = (clone $checkIn)->addDays($duration);
            $roomType = $roomTypes->random();
            $rooms = $allRooms[$roomType->id]->take(1);
            $totalAmount = $roomType->price * $duration;

            $booking = Booking::create([
                'hotel_id' => $hotel->id,
                'uuid' => Str::uuid(),
                'room_type_id' => $roomType->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'rooms' => 1,
                'total_amount' => $totalAmount,
                'status' => 'checked_out',
                'checked_in_at' => $checkIn->addHours(14),
                'checked_out_at' => $checkOut->addHours(11),
                'meta' => ['source' => 'Walk-in', 'advance_paid' => $totalAmount],
                'company_id' => rand(0, 100) < 30 && $companies->count() > 0 ? $companies->random()->id : null,
            ]);

            $createGuest($booking, true);
            if ($rooms->isNotEmpty()) {
                $booking->assignedRooms()->sync([$rooms->first()->id]);
            }
        }

        // 2. CURRENTLY IN-HOUSE
        for ($i = 0; $i < 10; $i++) {
            $checkIn = Carbon::today()->subDays(rand(1, 3));
            $duration = rand(3, 7);
            $checkOut = (clone $checkIn)->addDays($duration);
            $roomType = $roomTypes->random();
            $rooms = $allRooms[$roomType->id]->take(1);
            $totalAmount = $roomType->price * $duration;

            $booking = Booking::create([
                'hotel_id' => $hotel->id,
                'uuid' => Str::uuid(),
                'room_type_id' => $roomType->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'rooms' => 1,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'checked_in_at' => $checkIn->addHours(13),
                'checked_out_at' => null,
                'meta' => ['source' => 'Online', 'advance_paid' => $totalAmount / 2],
            ]);

            $createGuest($booking, true);
            if ($rooms->isNotEmpty()) {
                $booking->assignedRooms()->sync([$rooms->first()->id]);
                $rooms->first()->update([
                    'status' => 'occupied',
                    'housekeeping_status' => 'dirty'
                ]);
            }
        }

        // 3. UPCOMING
        for ($i = 0; $i < 10; $i++) {
            $checkIn = Carbon::today()->addDays(rand(1, 30));
            $duration = rand(2, 5);
            $checkOut = (clone $checkIn)->addDays($duration);
            $roomType = $roomTypes->random();
            $totalAmount = $roomType->price * $duration;

            $booking = Booking::create([
                'hotel_id' => $hotel->id,
                'uuid' => Str::uuid(),
                'room_type_id' => $roomType->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'rooms' => 1,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'checked_in_at' => null,
                'checked_out_at' => null,
                'meta' => ['source' => 'OTA', 'advance_paid' => 0],
            ]);

            $createGuest($booking, true);
        }

        // 4. CANCELLED
        for ($i = 0; $i < 5; $i++) {
            $checkIn = Carbon::today()->subDays(rand(5, 15));
            $duration = 2;
            $checkOut = (clone $checkIn)->addDays($duration);
            $roomType = $roomTypes->random();
            $totalAmount = $roomType->price * $duration;

            $booking = Booking::create([
                'hotel_id' => $hotel->id,
                'uuid' => Str::uuid(),
                'room_type_id' => $roomType->id,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
                'rooms' => 1,
                'total_amount' => $totalAmount,
                'status' => 'cancelled',
                'checked_in_at' => null,
                'checked_out_at' => null,
                'meta' => ['source' => 'Direct', 'cancellation_reason' => 'Guest Request'],
            ]);

            $createGuest($booking, true);
        }
    }
}
