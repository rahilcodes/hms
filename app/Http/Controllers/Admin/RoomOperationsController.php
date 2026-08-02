<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use Carbon\Carbon;

class RoomOperationsController extends Controller
{
    /**
     * Display the Room Master View (Today's Operations)
     */
    public function index(Request $request)
    {
        $today = Carbon::today();

        // Fetch all rooms with their type and TODAY'S bookings
        // We filter the relationship to only include relevant bookings to avoid fetching history
        $rooms = Room::with(['roomType', 'bookings' => function ($q) use ($today) {
            $q->whereIn('status', ['confirmed', 'checked_in', 'checked_out']) // Include active and departed bookings
                ->where(function ($sub) use ($today) {
                // Check-in is today OR Check-out is today OR Stayover (encompasses today)
                $sub->whereDate('check_in', $today)
                    ->orWhereDate('check_out', $today)
                    ->orWhere(function ($stay) use ($today) {
                    $stay->whereDate('check_in', '<', $today)
                        ->whereDate('check_out', '>', $today);
                }
                );
            }
            )
                ->with(['guests', 'roomServiceOrders']); // Load guests and addons
        }])
            ->orderBy('room_number') // Natural sort if possible, but standard sort works
            ->get();

        $processedRooms = $rooms->map(function ($room) use ($today) {
            $data = [
                'id' => $room->id,
                'number' => $room->room_number,
                'type' => $room->roomType->name ?? 'Standard',
                'hk_status' => $room->housekeeping_status ?? 'clean', // clean, dirty, maintenance
                'status' => 'vacant',

                'booking_arrival' => null,
                'booking_departure' => null,
                'booking_stayover' => null,
            ];

            // Analyze bookings for this room
            // A room can have max 2 bookings relevant today: 1 Departure AND 1 Arrival (Turnover)
            // Or just 1 (Stayover / Arrival / Departure)

            foreach ($room->bookings as $booking) {
                // Ignore cancelled (already filtered by query, but good safety)
                if ($booking->status === 'cancelled')
                    continue;

                $isArrival = $booking->check_in->isSameDay($today);
                $isDeparture = $booking->check_out->isSameDay($today);
                $isStayover = $booking->check_in->lt($today) && $booking->check_out->gt($today);

                if ($isArrival)
                    $data['booking_arrival'] = $booking;
                if ($isDeparture)
                    $data['booking_departure'] = $booking;
                if ($isStayover)
                    $data['booking_stayover'] = $booking;
            }

            // Determine Master Status
            if ($data['booking_arrival'] && $data['booking_departure']) {
                $data['status'] = 'turnover';
            }
            elseif ($data['booking_arrival']) {
                $data['status'] = $data['booking_arrival']->checked_in_at ? 'occupied' : 'arrival';
            }
            elseif ($data['booking_departure']) {
                $data['status'] = $data['booking_departure']->checked_out_at ? 'vacant_dirty' : 'departure';
                // If checked out, it's virtually vacant but physically dirty until HK cleans it
                if ($data['booking_departure']->checked_out_at && $room->housekeeping_status === 'clean') {
                    $data['status'] = 'vacant'; // Ready for occupancy
                }
            }
            elseif ($data['booking_stayover']) {
                $data['status'] = 'occupied';
            }

            return (object)$data;
        });

        // Fetch unassigned arrivals for today (Pending Room)
        $unassignedBookings = \App\Models\Booking::whereDate('check_in', $today)
            ->where('status', 'confirmed')
            ->doesntHave('assignedRooms')
            ->with(['guests', 'roomType'])
            ->get();

        // Group by floor (optional, but good for UI) or just pass list
        return view('admin.operations.room-view', [
            'rooms' => $processedRooms,
            'today' => $today,
            'unassignedBookings' => $unassignedBookings
        ]);
    }
}
