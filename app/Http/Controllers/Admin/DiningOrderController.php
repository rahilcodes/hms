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
            ->where('type', 'dining')
            ->whereHas('booking', function ($q) {
                $q->where('status', 'checked_in');
            })
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

    /**
     * 80mm thermal-style Kitchen Order Ticket.
     */
    public function kot(RoomServiceOrder $order)
    {
        $order->load(['booking.assignedRooms', 'booking.guests']);

        if (!$order->kot_number) {
            $order->update([
                'kot_number' => (int) (RoomServiceOrder::whereDate('created_at', $order->created_at->toDateString())
                    ->where('id', '!=', $order->id)
                    ->max('kot_number') ?? 0) + 1,
            ]);
        }

        return view('admin.dining.orders.kot', compact('order'));
    }
}
