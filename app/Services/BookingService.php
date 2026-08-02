<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingGuest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function create(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $roomTypeIds = array_keys($data['items']);
            sort($roomTypeIds); // 🔒 Consistent ordering to prevent deadlocks

            // 1. ATOMIC LOCK: Serialize requests for ALL involved RoomTypes
            $roomTypes = \App\Models\RoomType::whereIn('id', $roomTypeIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $availabilityService = app(\App\Services\AvailabilityService::class);
            $checkIn = $data['check_in'];
            $checkOut = $data['check_out'];
            $groupId = count($data['items']) > 1 ? (string)\Illuminate\Support\Str::uuid() : null;
            $createdBookings = [];

            // 2. 🛡️ VERIFY ALL ITEMS BEFORE SAVING ANYTHING
            foreach ($data['items'] as $item) {
                $rt = $roomTypes->get($item['room_type_id']);
                if (!$availabilityService->isAvailable($rt, $checkIn, $checkOut, $item['rooms'])) {
                    throw ValidationException::withMessages([
                        'availability' => "Room type '{$rt->name}' is no longer available for the requested dates.",
                    ]);
                }
            }

            // 3. EXECUTE CREATION
            foreach ($data['items'] as $item) {
                $rt = $roomTypes->get($item['room_type_id']);

                $booking = Booking::create([
                    'group_id' => $groupId,
                    'company_id' => $data['company_id'] ?? null,
                    'room_type_id' => $rt->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'rooms' => (int)$item['rooms'],
                    'total_amount' => $item['total_amount'],
                    'services_json' => $item['services'] ?? [],
                    'status' => $data['status'] ?? 'pending',
                    'meta' => array_merge($data['meta'] ?? [], [
                        'extra_persons' => (int)($item['extra_persons'] ?? 0),
                    ]),
                ]);

                $booking->guests()->create([
                    'name' => $data['guest_name'],
                    'phone' => $data['guest_phone'] ?? null,
                ]);

                $createdBookings[] = $booking;
            }

            return $createdBookings;
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

        // Confirm sibling group bookings paid in the same transaction
        if ($booking->group_id) {
            Booking::where('group_id', $booking->group_id)
                ->where('status', 'pending')
                ->update(['status' => 'confirmed', 'expires_at' => null]);
        }

        event(new \App\Events\BookingConfirmed($booking));
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
