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

        // Strip "Res #" or "#" from identity if guest types it
        $cleanId = str_replace(['Res #', 'Res', '#'], '', $identity);
        $isNumericId = is_numeric($cleanId);

        // Sanitize identity for phone matching (strip spaces, dashes, etc)
        $cleanIdentity = preg_replace('/[^0-9]/', '', $identity);

        // Search for all matching active bookings
        $bookings = Booking::where('check_in', $checkIn)
            ->where(function ($query) use ($identity, $cleanIdentity, $isNumericId, $cleanId) {
                // Initialize with a dummy false condition so we can orWhere everything else safely
                $query->whereRaw('0=1');

                // 1. Numeric ID Match
                if ($isNumericId) {
                    $query->orWhere('id', $cleanId)
                        ->orWhere('id', $cleanId - 1000);
                }

                // 2. Metadata / Guest Info Match (Email/Phone)
                $query->orWhere('meta->guest_email', $identity)
                    ->orWhere('meta->guest_phone', $identity)
                    ->orWhereHas('guests', function ($q) use ($identity, $cleanIdentity) {
                    $q->where('phone', $identity)
                        ->orWhere('email', $identity)
                        ->orWhere('phone', 'LIKE', "%{$identity}%");

                    if (!empty($cleanIdentity)) {
                        $q->orWhereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', '') LIKE ?", ["%{$cleanIdentity}%"]);
                    }
                });
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

    public function switch(Booking $booking)
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

        return view('guest.dashboard', compact('booking'));
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

        $bookingId = Session::get('guest_booking_id');
        $booking = Booking::findOrFail($bookingId);
        $service = \App\Models\Service::findOrFail($request->service_id);

        $servicesJson = $booking->services_json ?? [];

        // Calculate price
        $nights = \Carbon\Carbon::parse($booking->check_in)->diffInDays(\Carbon\Carbon::parse($booking->check_out)) ?: 1;
        $price = $service->price * $request->qty;
        if ($service->price_unit === 'per_night') {
            $price *= $nights;
        }

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

        return back()->with('success', $service->name . ' has been added to your booking.');
    }

    public function logout()
    {
        Session::forget(['guest_booking_id', 'guest_available_bookings']);
        return redirect()->route('guest.login');
    }
}
