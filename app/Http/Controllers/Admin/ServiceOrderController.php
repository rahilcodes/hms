<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomServiceOrder;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    public function index()
    {
        $orders = RoomServiceOrder::with(['booking.roomType'])
            ->where('type', 'service')
            ->whereHas('booking', function ($q) {
                $q->where('status', 'checked_in');
            })
            ->latest()
            ->paginate(15);

        return view('admin.services.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, RoomServiceOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Service order status updated to ' . $request->status);
    }
}
