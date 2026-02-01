<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\Booking;
use App\Models\BlockedDate;
use Carbon\Carbon;

class AvailabilityService
{
    public function isAvailable(
        RoomType $roomType,
        string $checkIn,
        string $checkOut,
        int $rooms
    ): bool {
        return $this->maxAvailableRooms($roomType, $checkIn, $checkOut) >= $rooms;
    }

    public function maxAvailableRooms(
        RoomType $roomType,
        string $checkIn,
        string $checkOut
    ): int {
        $dates = $this->dateRange($checkIn, $checkOut);
        $minAvailable = $roomType->total_rooms;

        foreach ($dates as $date) {

            $booked = Booking::where('room_type_id', $roomType->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->whereDate('check_in', '<=', $date)
                ->whereDate('check_out', '>', $date)
                ->sum('rooms');

            $blocked = BlockedDate::where('room_type_id', $roomType->id)
                ->whereDate('date', $date)
                ->sum('blocked_rooms');

            $maintenance = \App\Models\RoomMaintenance::where('room_type_id', $roomType->id)
                ->where('status', 'ongoing')
                ->whereDate('start_date', '<=', $date)
                ->whereDate('end_date', '>=', $date)
                ->sum('rooms_count');

            $available = $roomType->total_rooms - $booked - $blocked - $maintenance;

            $minAvailable = min($minAvailable, max(0, $available));
        }

        return max(0, $minAvailable);
    }

    private function dateRange(string $checkIn, string $checkOut): array
    {
        $dates = [];
        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);

        while ($start->lt($end)) {
            $dates[] = $start->toDateString();
            $start->addDay();
        }

        return $dates;
    }
}
