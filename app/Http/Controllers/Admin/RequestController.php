<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GuestRequest;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index()
    {
        // Fetch requests for this hotel
        // Assuming we are in a single-tenant or using scopes correctly.
        // Since we pivoted to 'Titanium', the Admin guard behaves like tenant admin?
        // Wait, Admin guard is 'admin'.
        // We need to filter by hotel_id if multi-tenant.
        // Assuming Booking belongs to Hotel.

        $user = Auth::guard('admin')->user();
        $hotelId = $user->hotel_id;

        $requests = GuestRequest::whereHas('booking', function ($query) use ($hotelId) {
            $query->where('hotel_id', $hotelId);
        })
            ->with(['booking.roomType', 'booking.guests']) // Eager load
            ->orderByRaw("FIELD(status, 'pending', 'in_progress', 'completed')")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.requests.index', compact('requests'));
    }

    public function update(Request $request, GuestRequest $guestRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        $guestRequest->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Request status updated.');
    }
}
