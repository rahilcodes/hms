<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Session;

class GuestPortalMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('guest_booking_id') && !Session::has('guest_available_bookings')) {
            return redirect()->route('guest.login')->withErrors(['identity' => 'Please verify your details to access the portal.']);
        }

        return $next($request);
    }
}
