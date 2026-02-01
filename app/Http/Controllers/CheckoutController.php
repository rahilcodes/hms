<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Services\PricingService;
use App\Services\BookingService;
use App\Services\AvailabilityService;
use App\Models\Service;

class CheckoutController extends Controller
{
    public function show(
        Request $request,
        PricingService $pricing,
        AvailabilityService $availability
    ) {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);


        /**
         * Normalize rooms input
         */
        if ($request->has('room_type_id')) {
            $rooms = [
                $request->room_type_id => (int) ($request->rooms ?? 1),
            ];
        } else {
            $request->validate([
                'rooms' => 'required|array',
            ]);
            $rooms = $request->rooms;
        }

        // 🔹 MULTI-ROOM: Merge Session Cart if exists
        if (session()->has('temp_cart')) {
            $cart = session()->get('temp_cart'); // ['room_type_id' => qty]

            // Merge current request rooms into cart
            foreach ($rooms as $rtId => $qty) {
                if (isset($cart[$rtId])) {
                    $cart[$rtId] += $qty;
                } else {
                    $cart[$rtId] = $qty;
                }
            }

            // If we are just viewing, and have session data, we might want to consolidate
            // But usually, if we just came back from "Add Room", the URL params might only have the NEW room
            // We need to redirect to a clean URL containing ALL rooms to reset state

            // However, to keep it simple: if we detect session cart, use THAT as the source of truth + current request
            // And to "clean" the URL and session, we construct a full query string and redirect once

            if (!$request->has('merged')) {
                // Forget session to avoid loop, provided we redirect with all data
                session()->forget('temp_cart');

                $queryParams = [
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'rooms' => $cart,
                    'merged' => 1
                ];

                return redirect()->route('checkout.show', $queryParams);
            }

            // If already merged, use the cart (which should be in $rooms now via URL if we directed correctly)
            // Actually, if we redirect with 'rooms' array, the code above lines 28-37 handles it.
            // So we just need to ensure we don't double count if we didn't redirect.

            // Simplified approach: atomic merge and redirect
            session()->forget('temp_cart');
            $queryParams = [
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'rooms' => $cart,
            ];
            return redirect()->route('checkout.show', $queryParams);
        }

        $selectedRooms = [];
        $total = 0;

        foreach ($rooms as $roomTypeId => $qty) {
            if ($qty < 1)
                continue;

            $roomType = RoomType::findOrFail($roomTypeId);

            if (
                $availability->maxAvailableRooms(
                    $roomType,
                    $request->check_in,
                    $request->check_out
                ) < $qty
            ) {
                abort(409, 'Room availability changed');
            }

            // 🔹 PER-NIGHT BREAKDOWN
            $nightly = $pricing->nightlyPrices(
                $roomType,
                $request->check_in,
                $request->check_out
            );

            $roomTotal = 0;
            foreach ($nightly as $night) {
                $roomTotal += ($night['price'] * $qty);
            }

            $total += $roomTotal;

            $selectedRooms[] = [
                'room' => $roomType,
                'qty' => $qty,
                'total' => $roomTotal,
                'nightly' => $nightly,
                'max_extra_persons' => $roomType->max_extra_persons,
                'extra_person_price' => $roomType->extra_person_price,
                'base_occupancy' => $roomType->base_occupancy,
            ];
        }

        abort_if(empty($selectedRooms), 400);

        return view('checkout.index', [
            'selectedRooms' => $selectedRooms,
            'total' => $total,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'rooms' => $rooms,
            'upsells' => Service::where('is_active', true)->get(),
            'paymentSettings' => [
                'enabled' => site('payment_feature_enabled', '0') === '1',
                'mode' => site('payment_mode', 'hotel_only'), // hotel_only, online_only, partial_deposit
                'deposit_type' => site('deposit_type', 'percentage'), // percentage, fixed
                'deposit_value' => site('deposit_value', 0),
            ],
        ]);
    }

    public function store(
        Request $request,
        BookingService $bookingService,
        PricingService $pricing,
        AvailabilityService $availability
    ) {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'payment_type' => 'required|in:offline,online',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
            'rooms' => 'required|array',
        ]);

        $groupId = \Illuminate\Support\Str::uuid()->toString();
        $totalPlusUpsells = 0;
        $nights = \Carbon\Carbon::parse($request->check_in)->diffInDays($request->check_out);
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;

        // 1. First, calculate the grand total to determine deposit
        $tempTotal = 0;
        $servicesRequest = json_decode($request->services_json, true) ?? [];

        foreach ($request->rooms as $roomTypeId => $qty) {
            if ($qty < 1)
                continue;
            $roomType = RoomType::findOrFail($roomTypeId);

            // Availability check
            if ($availability->maxAvailableRooms($roomType, $checkIn, $checkOut) < $qty) {
                abort(409, 'Room availability changed');
            }

            // Room Base Price
            $roomPrice = $pricing->calculate($roomType, $checkIn, $checkOut, 1);
            $tempTotal += ($roomPrice * $qty);

            // Extra Persons
            $extraPersons = ($request->extra_persons[$roomTypeId] ?? 0);
            $tempTotal += ($extraPersons * $roomType->extra_person_price * $nights * $qty);
        }

        // Add services from request (they are per-room-type in UI)
        foreach ($servicesRequest as $item) {
            $price = (float) $item['price'];
            $itemQty = (int) ($item['qty'] ?? 1);
            $itemTotal = $price * $itemQty;
            if (($item['price_unit'] ?? 'fixed') === 'per_night') {
                $itemTotal *= $nights;
            }
            // In our UI, services are currently per room type, but we'll apply them to all rooms of that type
            $roomsOfType = (int) ($request->rooms[$item['room_type_id']] ?? 0);
            $tempTotal += ($itemTotal * $roomsOfType);
        }

        $totalPlusUpsells = $tempTotal;

        // 2. Determine advance paid
        $paymentEnabled = site('payment_feature_enabled', '0') === '1';
        $paymentMode = site('payment_mode', 'hotel_only');
        $grandAdvancePaid = 0;

        if ($paymentEnabled && $request->payment_type === 'online') {
            if ($paymentMode === 'online_only') {
                $grandAdvancePaid = $totalPlusUpsells;
            } elseif ($paymentMode === 'partial_deposit') {
                $depositType = site('deposit_type', 'percentage');
                $depositValue = (float) site('deposit_value', 0);
                if ($depositType === 'percentage') {
                    $grandAdvancePaid = ($totalPlusUpsells * $depositValue) / 100;
                } else {
                    $grandAdvancePaid = min($depositValue, $totalPlusUpsells);
                }
            }
        }

        $totalDiscount = (float) ($request->input('discount_amount') ?? 0);
        $couponId = $request->input('coupon_id');

        $totalRooms = 0;
        foreach ($request->rooms as $qty)
            $totalRooms += $qty;

        // 3. Create individual bookings
        $firstBooking = null;
        $remainingAdvance = $grandAdvancePaid;
        $remainingDiscount = $totalDiscount;
        $processedRooms = 0;

        foreach ($request->rooms as $roomTypeId => $qty) {
            if ($qty < 1)
                continue;
            $roomType = RoomType::findOrFail($roomTypeId);
            $extraPersons = ($request->extra_persons[$roomTypeId] ?? 0);

            // Services for this room type
            $roomTypeServices = array_filter($servicesRequest, function ($s) use ($roomTypeId) {
                return (string) $s['room_type_id'] === (string) $roomTypeId;
            });

            for ($i = 0; $i < $qty; $i++) {
                $processedRooms++;
                $bookingAmount = $pricing->calculate($roomType, $checkIn, $checkOut, 1);
                $bookingAmount += ($extraPersons * $roomType->extra_person_price * $nights);

                $servicesTotal = 0;
                foreach ($roomTypeServices as $s) {
                    $st = $s['price'] * ($s['qty'] ?? 1);
                    if (($s['price_unit'] ?? 'fixed') === 'per_night')
                        $st *= $nights;
                    $servicesTotal += $st;
                }
                $bookingAmount += $servicesTotal;

                // Simple flat distribution for now: last room gets the remainder
                if ($processedRooms === $totalRooms) {
                    $currentBookingDiscount = $remainingDiscount;
                } else {
                    $currentBookingDiscount = round($totalDiscount / $totalRooms, 2);
                    $remainingDiscount -= $currentBookingDiscount;
                }

                // Apply discount to this individual booking's amount
                $bookingAmount -= $currentBookingDiscount;
                if ($bookingAmount < 0)
                    $bookingAmount = 0; // Ensure booking amount doesn't go negative

                // Distribute advance paid? Or put all in first? 
                // Let's put all in first for simplicity of accounting reconciliation
                $advanceForThis = ($firstBooking === null) ? $remainingAdvance : 0;

                $booking = \App\Models\Booking::create([
                    'hotel_id' => 1,
                    'group_id' => $groupId,
                    'room_type_id' => $roomType->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                    'rooms' => 1,
                    'total_amount' => $bookingAmount,
                    'discount_amount' => $currentBookingDiscount, // Add discount amount
                    'coupon_id' => $couponId, // Add coupon ID
                    'services_json' => array_values($roomTypeServices),
                    'status' => 'pending',
                    'meta' => [
                        'extra_persons' => $extraPersons,
                        'advance_paid' => $advanceForThis,
                        'source' => 'guest_portal'
                    ],
                ]);

                \App\Models\BookingGuest::create([
                    'booking_id' => $booking->id,
                    'name' => $request->name,
                    'phone' => $request->phone,
                ]);

                if ($firstBooking === null)
                    $firstBooking = $booking;
            }
        }

        if ($request->payment_type === 'offline') {
            // Confirm all bookings in the group
            \App\Models\Booking::where('group_id', $groupId)->update(['status' => 'confirmed']);
            return redirect('/thank-you');
        }

        abort(503, 'Online payment disabled');
    }

    public function addRoom(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        // Get current rooms from request
        $currentRooms = [];
        if ($request->has('room_type_id')) {
            $currentRooms[$request->room_type_id] = (int) ($request->rooms ?? 1);
        } elseif ($request->has('rooms') && is_array($request->rooms)) {
            $currentRooms = $request->rooms;
        }

        // Get existing cart
        $cart = session('temp_cart', []);

        // Merge
        foreach ($currentRooms as $rtId => $qty) {
            if (isset($cart[$rtId])) {
                $cart[$rtId] += $qty;
            } else {
                $cart[$rtId] = $qty;
            }
        }

        // Save back to session
        session(['temp_cart' => $cart]);

        // Redirect to rooms page to pick another
        return redirect()->route('rooms', [
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
        ]);
    }
}
