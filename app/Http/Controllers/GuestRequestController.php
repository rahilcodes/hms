<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GuestRequest;
use Illuminate\Support\Facades\Auth;

class GuestRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'request' => 'required|string',
        ]);

        $bookingId = \Illuminate\Support\Facades\Session::get('guest_booking_id');
        if (!$bookingId)
            return back()->with('error', 'Session expired');

        GuestRequest::create([
            'booking_id' => $bookingId,
            'type' => $request->type,
            'request' => $request->input('request'),
            'status' => 'pending'
        ]);

        return back()->with('success', 'Your request has been sent to the concierge.');
    }
}
