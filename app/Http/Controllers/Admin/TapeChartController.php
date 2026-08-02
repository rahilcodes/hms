<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\MaintenanceLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TapeChartController extends Controller
{
    public function index(Request $request)
    {
        // 1. Date Range
        $startDate = $request->query('start_date')
            ? Carbon::parse($request->query('start_date'))
            : now();

        $days = (int) $request->query('days', 15);
        $endDate = $startDate->copy()->addDays($days - 1);

        // 2. Fetch Room Types with Rooms
        // We only want active rooms
        $roomTypes = RoomType::with([
            'rooms' => function ($q) {
                $q->orderBy('floor')->orderBy('room_number');
            }
        ])->get();

        // 3. Fetch Bookings in Range
        // We need bookings that overlap with the Date Range
        // Overlap Logic: (StartA <= EndB) and (EndA >= StartB)
        // Booking Start <= Range End AND Booking End >= Range Start
        $bookings = Booking::query()
            ->where('check_in', '<=', $endDate->format('Y-m-d'))
            ->where('check_out', '>=', $startDate->format('Y-m-d'))
            ->whereIn('status', ['confirmed', 'checked_in', 'checked_out']) // Exclude cancelled
            ->with(['guests', 'roomType', 'assignedRooms']) // Eager load
            ->get();

        // 4. Fetch Maintenance Logs in Range
        // Maintenance often has created_at, but we need the "scheduled" time or duration.
        // Assuming 'created_at' is start, and correct logic would rely on 'completed_at' or a 'due_date'.
        // For now, looking for Active maintenance or recently completed in range.
        // If MaintenanceLog doesn't have a start/end date specifically for the "blocking", we might rely on 'status' = 'in_progress'.
        // Let's grab in-progress ones.
        $maintenance = MaintenanceLog::query()
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('room_id')
            ->get();

        // 5. Structure Data for View
        // We need to map Bookings to specific Rooms.
        // Note: Some bookings might NOT have a room assigned yet. They go to "Unassigned" row.

        // Let's pass the raw collections and handle mapping in the View or a Transformer?
        // View is easier for Blade if we keep it simple.

        // Dates Header
        $dates = [];
        $curr = $startDate->copy();
        for ($i = 0; $i < $days; $i++) {
            $dates[] = [
                'date' => $curr->format('Y-m-d'),
                'day' => $curr->format('D'),
                'day_num' => $curr->format('d'),
                'is_weekend' => $curr->isWeekend(),
                'is_today' => $curr->isToday(),
            ];
            $curr->addDay();
        }

        return view('admin.tape-chart.index', compact(
            'roomTypes',
            'bookings',
            'maintenance',
            'dates',
            'startDate',
            'endDate',
            'days'
        ));
    }
}
