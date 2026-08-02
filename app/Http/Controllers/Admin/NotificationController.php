<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        $notifications = AdminNotification::where('hotel_id', $hotelId)
            ->latest()
            ->take(20)
            ->get();

        return response()->json($notifications);
    }

    public function check()
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;

        $notifications = AdminNotification::where('hotel_id', $hotelId)
            ->where('is_read', false)
            ->get();

        return response()->json($notifications);
    }

    public function markAllRead()
    {
        $hotelId = Auth::guard('admin')->user()->hotel_id;
        AdminNotification::where('hotel_id', $hotelId)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
