<?php

namespace App\Http\Controllers\Titanium;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,urgent',
        ]);

        $user = Auth::guard('titanium')->user();
        $hotel = $user->hotel;

        if (!$hotel) {
            $hotel = \App\Models\Hotel::first();
        }

        if (!$hotel) {
            return back()->with('error', 'No hotel found in the system.');
        }

        AdminNotification::create([
            'hotel_id' => $hotel->id,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'is_read' => false,
        ]);

        return back()->with('success', 'Notification sent to Hotel Staff.');
    }
}
