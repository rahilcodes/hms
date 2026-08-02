<?php

namespace App\Http\Controllers;

use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\RoomServiceOrder;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GuestDiningController extends Controller
{
    public function index()
    {
        $bookingId = Session::get('guest_booking_id');
        if (!$bookingId)
            return redirect()->route('guest.login');

        $categories = MenuCategory::with([
            'items' => function ($q) {
                $q->where('is_available', true);
            }
        ])->where('is_active', true)->ordered()->get();

        $booking = Booking::with('roomType')->findOrFail($bookingId);

        // Fetch recent orders
        $recentOrders = RoomServiceOrder::where('booking_id', $bookingId)
            ->latest()
            ->take(5)
            ->get();

        return view('guest.dining', compact('categories', 'booking', 'recentOrders'));
    }

    public function store(Request $request)
    {
        $bookingId = Session::get('guest_booking_id');
        if (!$bookingId)
            return response()->json(['error' => 'Unauthenticated'], 401);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $orderItems = [];
        $totalAmount = 0;

        foreach ($request->items as $item) {
            $menuItem = MenuItem::findOrFail($item['id']);
            $subtotal = $menuItem->price * $item['qty'];
            $totalAmount += $subtotal;

            $orderItems[] = [
                'id' => $menuItem->id,
                'name' => $menuItem->name,
                'price' => $menuItem->price,
                'qty' => $item['qty'],
                'subtotal' => $subtotal
            ];
        }

        $order = RoomServiceOrder::create([
            'booking_id' => $bookingId,
            'items' => $orderItems,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'notes' => $request->notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your order has been placed!',
            'order_id' => $order->id
        ]);
    }
}
