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
        ): bool
    {
        return $this->maxAvailableRooms($roomType, $checkIn, $checkOut) >= $rooms;
    }

    public function maxAvailableRooms(
        RoomType $roomType,
        string $checkIn,
        string $checkOut
        ): int
    {
        $dates = $this->dateRange($checkIn, $checkOut);
        if (empty($dates))
            return 0;

        // 🚀 BATCH FETCH ALL RELEVANT DATA ONCE
        $bookings = Booking::where('room_type_id', $roomType->id)
            ->whereIn('status', ['pending', 'confirmed', 'checked_in'])
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->get();

        $blocked = BlockedDate::where('room_type_id', $roomType->id)
            ->whereIn('date', $dates)
            ->get();

        $maintenance = \App\Models\RoomMaintenance::where('room_type_id', $roomType->id)
            ->where('status', 'ongoing')
            ->where('start_date', '<=', end($dates))
            ->where('end_date', '>=', reset($dates))
            ->get();

        $holds = \App\Models\RoomHold::where('room_type_id', $roomType->id)
            ->where('expires_at', '>', now())
            ->where('session_id', '!=', session()->getId() ?? '')
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->get();

        $minAvailable = $roomType->total_rooms;

        // O(N) Initialization
        $dailyUsage = array_fill_keys($dates, 0);

        // O(M) Data Mapping
        foreach ($bookings as $b) {
            $rc = $b->roomCount($roomType->id);
            if ($rc <= 0)
                continue;

            $start = Carbon::parse(max($checkIn, \Carbon\Carbon::parse($b->check_in)->toDateString()));
            $end = Carbon::parse(min($checkOut, \Carbon\Carbon::parse($b->check_out)->toDateString()));

            while ($start->lt($end)) {
                $ds = $start->toDateString();
                if (isset($dailyUsage[$ds]))
                    $dailyUsage[$ds] += $rc;
                $start->addDay();
            }
        }

        foreach ($blocked as $b) {
            if (isset($dailyUsage[$b->date]))
                $dailyUsage[$b->date] += $b->blocked_rooms;
        }

        foreach ($maintenance as $m) {
            $start = Carbon::parse(max($checkIn, \Carbon\Carbon::parse($m->start_date)->toDateString()));
            $end = Carbon::parse(min($checkOut, \Carbon\Carbon::parse($m->end_date)->toDateString()));
            while ($start->lte($end)) {
                $ds = $start->toDateString();
                if (isset($dailyUsage[$ds]))
                    $dailyUsage[$ds] += $m->rooms_count;
                $start->addDay();
            }
        }

        foreach ($holds as $h) {
            $start = Carbon::parse(max($checkIn, \Carbon\Carbon::parse($h->check_in)->toDateString()));
            $end = Carbon::parse(min($checkOut, \Carbon\Carbon::parse($h->check_out)->toDateString()));
            while ($start->lt($end)) {
                $ds = $start->toDateString();
                if (isset($dailyUsage[$ds]))
                    $dailyUsage[$ds] += $h->rooms;
                $start->addDay();
            }
        }

        // Final O(N) Extraction
        foreach ($dates as $date) {
            $available = $roomType->total_rooms - $dailyUsage[$date];
            $minAvailable = min($minAvailable, max(0, $available));
            if ($minAvailable === 0)
                break; // Early termination optimization
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
