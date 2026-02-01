<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\BlockedDate;
use App\Models\RoomMaintenance;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::today();
        $days = (int) ($request->days ?? 15);
        $endDate = $startDate->copy()->addDays($days - 1);

        $roomTypes = RoomType::orderBy('id')->get();
        $period = CarbonPeriod::create($startDate, $endDate);

        $matrix = [];
        $dates = [];

        // Pre-fetch data for the entire range to avoid N+1
        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('check_in', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhereBetween('check_out', [$startDate->toDateString(), $endDate->toDateString()])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('check_in', '<=', $startDate->toDateString())
                            ->where('check_out', '>=', $endDate->toDateString());
                    });
            })
            ->get();

        $blocks = BlockedDate::whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        $maintenance = RoomMaintenance::where('status', 'ongoing')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereDate('start_date', '<=', $endDate)
                    ->whereDate('end_date', '>=', $startDate);
            })
            ->get();

        foreach ($period as $date) {
            $dateStr = $date->toDateString();
            $dates[] = $date;

            foreach ($roomTypes as $roomType) {
                // Sold through bookings
                $soldBookings = $bookings->where('room_type_id', $roomType->id)
                    ->filter(function ($b) use ($dateStr) {
                        return $b->check_in <= $dateStr && $b->check_out > $dateStr;
                    })->sum(fn($b) => $b->rooms[$roomType->id] ?? 0);

                // Blocked rooms
                $soldBlocked = $blocks->where('room_type_id', $roomType->id)
                    ->where('date', $dateStr)
                    ->sum('blocked_rooms');

                // Maintenance rooms
                $soldMaintenance = $maintenance->where('room_type_id', $roomType->id)
                    ->filter(function ($m) use ($dateStr) {
                        return $m->start_date <= $dateStr && $m->end_date >= $dateStr;
                    })->sum('rooms_count');

                $matrix[$dateStr][$roomType->id] = [
                    'sold' => $soldBookings + $soldBlocked + $soldMaintenance,
                    'total' => $roomType->total_rooms,
                    'room_name' => $roomType->name
                ];
            }
        }

        return view('admin.inventory.index', [
            'matrix' => $matrix,
            'roomTypes' => $roomTypes,
            'dates' => $dates,
            'startDate' => $startDate,
            'days' => $days
        ]);
    }
}
