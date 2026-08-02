<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingGuest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class GuestPortalController extends Controller
{
    public function showLogin()
    {
        if (Session::has('guest_booking_id')) {
            return redirect()->route('guest.dashboard');
        }
        return view('guest.login');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'identity' => 'required|string', // Email or Phone
            'check_in' => 'required|date',
        ]);

        $identity = trim($request->identity);
        $checkIn = $request->check_in;

        // Search for all matching active bookings
        $bookings = Booking::whereDate('check_in', $checkIn)
            ->where(function ($query) use ($identity) {
            // 1. Check Guest Table (Primary)
            $query->whereHas('guests', function ($q) use ($identity) {
                    $q->where('email', $identity)
                        ->orWhere('phone', $identity);
                }
                );

                // 2. Check Meta (Secondary)
                $query->orWhere('meta->guest_email', $identity)
                    ->orWhere('meta->guest_phone', $identity);

                // 3. Numeric ID Match
                if (is_numeric($identity)) {
                    $query->orWhere('id', $identity)
                        ->orWhere('id', (int)$identity - 1000);
                }
            })
            ->get();

        if ($bookings->isEmpty()) {
            return back()->withErrors(['identity' => 'No matching active booking found for these details.']);
        }

        if ($bookings->count() > 1) {
            // Store matching IDs in session and redirect to choice page
            Session::put('guest_available_bookings', $bookings->pluck('id')->toArray());
            return redirect()->route('guest.select');
        }

        // Only one booking found
        Session::put('guest_booking_id', $bookings->first()->id);
        Session::put('guest_available_bookings', [$bookings->first()->id]);

        return redirect()->route('guest.dashboard');
    }

    public function select()
    {
        $bookingIds = Session::get('guest_available_bookings');
        if (!$bookingIds)
            return redirect()->route('guest.login');

        $bookings = Booking::with('roomType', 'assignedRooms')->whereIn('id', $bookingIds)->get();
        return view('guest.select', compact('bookings'));
    }

    public function switch (Booking $booking)
    {
        $bookingIds = Session::get('guest_available_bookings', []);
        if (!in_array($booking->id, $bookingIds)) {
            return redirect()->route('guest.login');
        }

        Session::put('guest_booking_id', $booking->id);
        return redirect()->route('guest.dashboard');
    }

    public function dashboard()
    {
        $bookingId = Session::get('guest_booking_id');
        if (!$bookingId) {
            return redirect()->route('guest.login');
        }

        $booking = Booking::with(['roomType', 'guests', 'roomServiceOrders', 'assignedRooms', 'guestRequests'])->findOrFail($bookingId);

        // Fetch other rooms in the same group to show a "Multi-room Trip" summary
        $groupBookings = [];
        if ($booking->group_id) {
            $groupBookings = Booking::where('group_id', $booking->group_id)
                ->where('id', '!=', $booking->id)
                ->with(['roomType', 'assignedRooms'])
                ->get();
        }

        return view('guest.dashboard', compact('booking', 'groupBookings'));
    }

    public function addons()
    {
        $bookingId = Session::get('guest_booking_id');
        if (!$bookingId)
            return redirect()->route('guest.login');

        $booking = Booking::findOrFail($bookingId);
        $services = \App\Models\Service::where('is_active', true)->get();

        return view('guest.addons', compact('booking', 'services'));
    }

    public function bookAddon(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'qty' => 'required|integer|min:1'
        ]);

        return \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
            $bookingId = Session::get('guest_booking_id');
            // 🔒 PESSIMISTIC LOCK: Prevent concurrent total_amount / JSON corruption
            $booking = Booking::where('id', $bookingId)->lockForUpdate()->firstOrFail();
            $service = \App\Models\Service::findOrFail($request->service_id);

            // Calculate price
            $nights = \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) ?: 1;
            $price = $service->price * $request->qty;
            if ($service->price_unit === 'per_night') {
                $price *= $nights;
            }

            if ($booking->status === 'checked_in') {
                $booking->roomServiceOrders()->create([
                    'items' => [[
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->price,
                            'qty' => $request->qty,
                            'unit' => $service->price_unit
                        ]],
                    'total_amount' => $price,
                    'status' => 'pending',
                    'type' => 'service',
                    'notes' => 'Added via Guest Portal'
                ]);
            }
            else {
                $servicesJson = $booking->services_json ?? [];
                $servicesJson[] = [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => $service->price,
                    'qty' => $request->qty,
                    'price_unit' => $service->price_unit
                ];

                $booking->update([
                    'services_json' => $servicesJson,
                    'total_amount' => $booking->total_amount + $price
                ]);

                // Create FolioItem to keep ledger in sync
                \App\Models\FolioItem::create([
                    'hotel_id' => $booking->hotel_id,
                    'booking_id' => $booking->id,
                    'type' => 'service',
                    'description' => "Guest Portal: " . $service->name,
                    'amount' => $price,
                    'posted_at' => now(),
                    'metadata' => ['service_id' => $service->id, 'source' => 'guest_portal_addon']
                ]);
            }

            return back()->with('success', $service->name . ' has been requested.');
        });
    }

    public function logout()
    {
        Session::forget(['guest_booking_id', 'guest_available_bookings']);
        return redirect()->route('guest.login');
    }
}
