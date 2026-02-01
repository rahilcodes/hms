<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingGuest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function create(array $data): Booking
    {
        return DB::transaction(function () use ($data) {

            // 🔒 ATOMIC LOCK: Serialize requests for this RoomType
            $roomType = \App\Models\RoomType::where('id', array_key_first($data['rooms']))
                ->lockForUpdate()
                ->firstOrFail();

            // 🛡️ RE-VERIFY AVAILABILITY INSIDE LOCK (Double-Check)
            // Even if the validator passed, the state might have changed milliseconds ago.
            $availabilityService = app(\App\Services\AvailabilityService::class);
            $isAvailable = $availabilityService->isAvailable(
                $roomType,
                $data['check_in'],
                $data['check_out'],
                array_sum($data['rooms'])
            );

            if (!$isAvailable) {
                throw ValidationException::withMessages([
                    'availability' => 'We apologize. These rooms were just booked by another guest moments ago.',
                ]);
            }

            $booking = Booking::create([
                'hotel_id' => $data['hotel_id'],
                'room_type_id' => $roomType->id,
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'rooms' => array_sum($data['rooms']),
                'total_amount' => $data['total_amount'],
                'services_json' => $data['services_json'] ?? [],
                'status' => 'pending',
                'expires_at' => now()->addMinutes(15),
                'meta' => [
                    'room_breakdown' => $data['rooms'],
                    'advance_paid' => $data['advance_paid'] ?? 0,
                ],
            ]);

            // 🔒 ALWAYS CREATE GUEST RECORDS
            foreach ($data['guests'] ?? [] as $guest) {
                BookingGuest::create([
                    'booking_id' => $booking->id,
                    'name' => $guest['name'],
                    'phone' => $guest['phone'],
                ]);
            }

            return $booking;
        });
    }

    public function confirm(Booking $booking): void
    {
        if ($booking->status !== 'pending') {
            throw ValidationException::withMessages([
                'booking' => 'Only pending bookings can be confirmed',
            ]);
        }

        $booking->update([
            'status' => 'confirmed',
            'expires_at' => null,
        ]);
    }

    public function cancel(Booking $booking): void
    {
        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            throw ValidationException::withMessages([
                'booking' => 'Booking cannot be cancelled',
            ]);
        }

        $booking->update([
            'status' => 'cancelled',
        ]);
    }
}
