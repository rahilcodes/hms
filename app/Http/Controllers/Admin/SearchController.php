<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingGuest;
use App\Models\RoomType;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function global(Request $request)
    {
        $query = $request->query('query');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Static Pages / Actions
        $pages = [
            ['title' => 'Dashboard', 'url' => route('admin.dashboard'), 'keywords' => 'home dashboard'],
            ['title' => 'Bookings Calendar', 'url' => route('admin.bookings.calendar'), 'keywords' => 'calendar bookings timeline'],
            ['title' => 'Create New Booking', 'url' => route('admin.bookings.create'), 'keywords' => 'new create booking add'],
            ['title' => 'Guest Database', 'url' => route('admin.guests.index'), 'keywords' => 'guests crm customers'],
            ['title' => 'Inventory Matrix', 'url' => route('admin.inventory.index'), 'keywords' => 'inventory rooms availability'],
            ['title' => 'Financial Reports', 'url' => route('admin.financials.index'), 'keywords' => 'money finance report revenue'],
            ['title' => 'Site Settings', 'url' => route('admin.site-settings.index'), 'keywords' => 'settings config site'],
            ['title' => 'Manage Coupons', 'url' => route('admin.coupons.index'), 'keywords' => 'coupon discount promo'],
        ];

        foreach ($pages as $page) {
            if (str_contains(strtolower($page['title']), strtolower($query)) || str_contains($page['keywords'], strtolower($query))) {
                $results[] = [
                    'type' => 'Go To',
                    'title' => $page['title'],
                    'subtitle' => 'Navigation',
                    'url' => $page['url'],
                    'icon' => 'arrow-right-circle'
                ];
            }
        }

        // Search Bookings (by ID or Guest Name)
        $bookings = Booking::where('id', 'like', "%{$query}%")
            ->orWhereHas('guests', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->latest()
            ->take(5)
            ->get();

        foreach ($bookings as $booking) {
            $results[] = [
                'type' => 'Booking',
                'title' => "Booking #{$booking->id} - {$booking->guest_name}",
                'subtitle' => $booking->check_in->format('d M') . ' - ' . $booking->check_out->format('d M Y'),
                'url' => route('admin.bookings.show', $booking),
                'icon' => 'calendar'
            ];
        }

        // Search Guests
        $guests = BookingGuest::where('name', 'like', "%{$query}%")
            ->orWhere('phone', 'like', "%{$query}%")
            ->select('name', 'phone')
            ->distinct()
            ->take(5)
            ->get();

        foreach ($guests as $guest) {
            $results[] = [
                'type' => 'Guest',
                'title' => $guest->name,
                'subtitle' => $guest->phone ?? 'No Phone',
                'url' => route('admin.guests.show', ['phone' => $guest->phone ?: 'none']),
                'icon' => 'user'
            ];
        }

        // Search Room Types
        $roomTypes = RoomType::where('name', 'like', "%{$query}%")
            ->take(3)
            ->get();

        foreach ($roomTypes as $rt) {
            $results[] = [
                'type' => 'Room Type',
                'title' => $rt->name,
                'subtitle' => "Base Price: ₹" . number_format($rt->base_price),
                'url' => route('admin.room-types.edit', $rt),
                'icon' => 'home'
            ];
        }

        return response()->json($results);
    }
}
