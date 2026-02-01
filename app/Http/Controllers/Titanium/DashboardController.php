<?php

namespace App\Http\Controllers\Titanium;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // For Single-Tenant Owner Mode, we fetch the admin's hotel.
        // If not linked yet, fall back to the first hotel (or handle this edge case).
        $user = Auth::guard('titanium')->user();

        $hotel = $user->hotel;

        // Edge Case: If no hotel is linked (legacy/seed data), fetch the first one and link it?
        // For now, let's just grab the first hotel if null, assuming single-tenant deployment.
        if (!$hotel) {
            $hotel = Hotel::with('subscription')->first();
            // If still no hotel (empty DB), we just use empty collections
            if ($hotel) {
                $notifications = \App\Models\AdminNotification::where('hotel_id', $hotel->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
            } else {
                $notifications = collect();
            }
        } else {
            $hotel->load('subscription');
            $notifications = \App\Models\AdminNotification::where('hotel_id', $hotel->id)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        $templates = \App\Models\NotificationTemplate::all();

        return view('titanium.dashboard', compact('hotel', 'notifications', 'templates'));
    }
}
