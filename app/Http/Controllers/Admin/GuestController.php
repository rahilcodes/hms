<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingGuest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuestController extends Controller
{
    public function index(Request $request)
    {
        $query = BookingGuest::select(
            'phone',
            'name',
            'email',
            DB::raw('count(*) as stay_count'),
            DB::raw('max(created_at) as last_stay'),
            DB::raw('SUM((SELECT total_amount FROM bookings WHERE bookings.id = booking_guests.booking_id)) as total_ltv')
        );

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                    ->orWhere('phone', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%");
            });
        }

        $guests = $query->groupBy('phone', 'name', 'email')
            ->orderByDesc('last_stay')
            ->paginate(20)
            ->withQueryString();

        return view('admin.guests.index', compact('guests'));
    }

    public function show($phone)
    {
        $guestBookings = BookingGuest::where('phone', $phone)
            ->with(['booking.roomType', 'booking.assignedRooms'])
            ->get();

        if ($guestBookings->isEmpty()) {
            abort(404);
        }

        $guestInfo = $guestBookings->first();

        $totalSpend = $guestBookings->sum(function ($gb) {
            return $gb->booking->total_amount ?? 0;
        });

        $avgSpend = $guestBookings->count() > 0 ? $totalSpend / $guestBookings->count() : 0;

        return view('admin.guests.show', compact('guestBookings', 'guestInfo', 'totalSpend', 'avgSpend'));
    }

    public function update(Request $request, $phone)
    {
        $validated = $request->validate([
            'preferences' => 'nullable|string|max:1000',
            'internal_notes' => 'nullable|string|max:1000',
            'tags' => 'nullable|array'
        ]);

        // Update all records for this guest (identified by phone)
        BookingGuest::where('phone', $phone)->update([
            'preferences' => $validated['preferences'],
            'internal_notes' => $validated['internal_notes'],
            'tags' => isset($validated['tags']) ? json_encode($validated['tags']) : null
        ]);

        return redirect()->back()->with('success', 'Guest profile updated successfully.');
    }
}
