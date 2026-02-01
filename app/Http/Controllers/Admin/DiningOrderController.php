<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomServiceOrder;
use Illuminate\Http\Request;

class DiningOrderController extends Controller
{
    public function index()
    {
        $orders = RoomServiceOrder::with(['booking.roomType'])
            ->latest()
            ->paginate(15);

        return view('admin.dining.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, RoomServiceOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,delivered,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated to ' . $request->status);
    }
}
