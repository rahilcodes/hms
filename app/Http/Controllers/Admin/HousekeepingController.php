<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use App\Models\GuestRequest;
use Illuminate\Http\Request;

class HousekeepingController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::with([
            'rooms.bookings' => function ($q) {
                // Get active bookings to find pending requests
                $q->where('status', 'checked_in')
                    ->with([
                        'guestRequests' => function ($r) {
                        $r->where('status', 'pending');
                    }
                    ]);
            }
        ])->with([
                    'rooms' => function ($q) {
                        $q->orderBy('room_number');
                    }
                ])->get();

        // Calculate pending arrivals for today per room type
        // Group bookings by room_type_id and count them
        $arrivals = Booking::where('status', 'confirmed')
            ->whereDate('check_in', now())
            ->whereNull('checked_in_at')
            ->get()
            ->groupBy('room_type_id')
            ->map(function ($bookings) {
                return $bookings->count();
            });

        $stats = [
            'total' => Room::count(),
            'dirty' => Room::where('housekeeping_status', 'dirty')->count(),
            'clean' => Room::where('housekeeping_status', 'clean')->count(),
            'cleaning' => Room::where('housekeeping_status', 'cleaning')->count(),
            'inspection' => Room::where('housekeeping_status', 'inspection_ready')->count(),
            'requests' => GuestRequest::where('status', 'pending')->count()
        ];

        return view('admin.housekeeping.index', compact('roomTypes', 'stats', 'arrivals'));
    }

    public function updateStatus(Request $request, Room $room)
    {
        $validated = $request->validate([
            'status' => 'required|in:clean,dirty,cleaning,inspection_ready',
        ]);

        $room->update(['housekeeping_status' => $validated['status']]);

        return response()->json(['success' => true, 'message' => 'Room status updated successfully.']);
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
            'notes' => 'nullable|string',
        ]);

        $room->update($validated);

        return response()->json(['success' => true, 'message' => 'Room details updated successfully.']);
    }
}
