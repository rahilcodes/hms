<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\RoomType;
use Carbon\Carbon;

class SmartAlertService
{
    public function getOccupancyAlerts()
    {
        $alerts = [];
        $roomTypes = RoomType::all();
        $startDate = Carbon::today();
        $endDate = Carbon::today()->addDays(14);

        // 🚀 BATCH FETCH ALL RELEVANT BOOKINGS ONCE
        $bookings = Booking::whereIn('status', ['confirmed', 'checked_in'])
            ->where('check_in', '<', $endDate->toDateString())
            ->where('check_out', '>', $startDate->toDateString())
            ->get();

        foreach ($roomTypes as $roomType) {
            $typeBookings = $bookings->where('room_type_id', $roomType->id);
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->toDateString();
                $dateObj = $currentDate->copy();

                // Calculate booked rooms for this specific date in memory
                $booked = $typeBookings->filter(fn($b) =>
                Carbon::parse($b->check_in)->lte($dateObj) &&
                Carbon::parse($b->check_out)->gt($dateObj)
                )->sum(fn($b) => $b->roomCount($roomType->id));

                $occupancy = ($roomType->total_rooms > 0) ? ($booked / $roomType->total_rooms) * 100 : 0;

                if ($occupancy >= 80) {
                    $alerts[] = [
                        'type' => 'high_occupancy',
                        'level' => 'warning',
                        'message' => "High occupancy (" . round($occupancy) . "%) for {$roomType->name} on " . $currentDate->format('M d') . ".",
                        'suggestion' => "Consider increasing price by 15-20% for this date.",
                        'date' => $dateStr
                    ];
                }
                elseif ($occupancy <= 20 && $currentDate->diffInDays(Carbon::today()) < 3) {
                    $alerts[] = [
                        'type' => 'low_occupancy',
                        'level' => 'info',
                        'message' => "Low occupancy (" . round($occupancy) . "%) for {$roomType->name} in the next 48 hours.",
                        'suggestion' => "Apply a 'Last Minute' discount to fill remaining slots.",
                        'date' => $dateStr
                    ];
                }

                $currentDate->addDay();
            }
        }

        return collect($alerts)->unique('message')->take(3);
    }
}
