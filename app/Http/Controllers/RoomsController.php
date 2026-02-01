<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use Illuminate\Http\Request;
use App\Services\AvailabilityService;
use App\Services\PricingService;
use Carbon\Carbon;

class RoomsController extends Controller
{
    public function index(
        Request $request,
        AvailabilityService $availability,
        PricingService $pricing
    ) {
        /**
         * Validate ONLY when dates are provided
         */
        if ($request->filled(['check_in', 'check_out'])) {
            $request->validate([
                'check_in' => 'nullable|date|after_or_equal:today',
                'check_out' => 'nullable|date|after:check_in',
                'rooms' => 'nullable|integer|min:1',
            ]);
        }

        $checkIn = $request->check_in ?? now()->toDateString();
        $checkOut = $request->check_out ?? now()->addDay()->toDateString();
        $rooms = (int) ($request->rooms ?? 1);

        $roomTypes = RoomType::orderBy('id')->get();
        $results = [];

        foreach ($roomTypes as $roomType) {

            $data = [
                'roomType' => $roomType,
                'max_available' => null,
                'total_price' => null,
                'breakdown' => null,
            ];

            if ($checkIn && $checkOut) {

                $maxAvailable = $availability->maxAvailableRooms(
                    $roomType,
                    $checkIn,
                    $checkOut
                );

                if ($maxAvailable > 0) {
                    $data['max_available'] = $maxAvailable;

                    $data['total_price'] = $pricing->calculate(
                        $roomType,
                        $checkIn,
                        $checkOut,
                        $rooms
                    );

                    $data['breakdown'] = $this->priceBreakdown(
                        $checkIn,
                        $checkOut
                    );
                }
            } else {
                // FALLBACK: Show Base Price if no dates selected
                $data['total_price'] = $roomType->price;
                $data['max_available'] = $roomType->total_rooms; // Assume all available for browsing
            }

            $results[] = $data;
        }

        return view('rooms.index', [
            'results' => $results,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'rooms' => $rooms,
        ]);
    }

    public function show(
        Request $request,
        RoomType $roomType,
        AvailabilityService $availability,
        PricingService $pricing
    ) {
        $checkIn = $request->check_in ?? now()->toDateString();
        $checkOut = $request->check_out ?? now()->addDay()->toDateString();
        $rooms = (int) ($request->rooms ?? 1);

        $maxAvailable = $availability->maxAvailableRooms($roomType, $checkIn, $checkOut);
        $totalPrice = $pricing->calculate($roomType, $checkIn, $checkOut, $rooms);

        $relatedRooms = RoomType::where('id', '!=', $roomType->id)
            ->inRandomOrder()
            ->take(3)
            ->get();

        // Get blocked dates for this room type
        $blockedDates = \App\Models\BlockedDate::where('room_type_id', $roomType->id)
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addMonths(6)->toDateString())
            ->pluck('date')
            ->toArray();

        // Get dates with full bookings (no availability)
        $bookedDates = [];
        $currentDate = now();
        $endDate = now()->addMonths(6);

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->toDateString();
            $nextDay = $currentDate->copy()->addDay()->toDateString();

            if ($availability->maxAvailableRooms($roomType, $dateStr, $nextDay) === 0) {
                $bookedDates[] = $dateStr;
            }

            $currentDate->addDay();
        }

        // Merge blocked and booked dates
        $unavailableDates = array_unique(array_merge($blockedDates, $bookedDates));

        return view('rooms.show', [
            'roomType' => $roomType,
            'relatedRooms' => $relatedRooms,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'rooms' => $rooms,
            'maxAvailable' => $maxAvailable,
            'totalPrice' => $totalPrice,
            'unavailableDates' => $unavailableDates,
        ]);
    }

    private function priceBreakdown(string $checkIn, string $checkOut): array
    {
        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);

        $weekday = 0;
        $weekend = 0;

        while ($start->lt($end)) {
            $start->isWeekend() ? $weekend++ : $weekday++;
            $start->addDay();
        }

        return [
            'weekday_nights' => $weekday,
            'weekend_nights' => $weekend,
        ];
    }
}
