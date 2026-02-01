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

        foreach ($roomTypes as $roomType) {
            $currentDate = $startDate->copy();

            while ($currentDate->lte($endDate)) {
                $dateStr = $currentDate->toDateString();

                $booked = Booking::where('room_type_id', $roomType->id)
                    ->whereIn('status', ['confirmed'])
                    ->whereDate('check_in', '<=', $dateStr)
                    ->whereDate('check_out', '>', $dateStr)
                    ->sum('rooms');

                $occupancy = ($roomType->total_rooms > 0) ? ($booked / $roomType->total_rooms) * 100 : 0;

                if ($occupancy >= 80) {
                    $alerts[] = [
                        'type' => 'high_occupancy',
                        'level' => 'warning',
                        'message' => "High occupancy ({$occupancy}%) for {$roomType->name} on {$currentDate->format('M d')}.",
                        'suggestion' => "Consider increasing price by 15-20% for this date.",
                        'date' => $dateStr
                    ];
                } elseif ($occupancy <= 20 && $currentDate->diffInDays(Carbon::today()) < 3) {
                    $alerts[] = [
                        'type' => 'low_occupancy',
                        'level' => 'info',
                        'message' => "Low occupancy ({$occupancy}%) for {$roomType->name} in the next 48 hours.",
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
