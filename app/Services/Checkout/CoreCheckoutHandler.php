<?php
namespace App\Services\Checkout;

use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Services\PricingService;
use App\Services\BookingService;
use App\Services\AvailabilityService;
use App\Models\Service;
use App\Http\Controllers\Controller;

class CoreCheckoutHandler extends Controller
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

        if (session()->has('temp_cart')) {
            $cart = session()->get('temp_cart');
            foreach ($rooms as $rtId => $qty) {
                if (isset($cart[$rtId])) {
                    $cart[$rtId] += $qty;
                } else {
                    $cart[$rtId] = $qty;
                }
            }
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
            if ($qty < 1) continue;
            $roomType = RoomType::findOrFail($roomTypeId);

            if ($availability->maxAvailableRooms($roomType, $request->check_in, $request->check_out) < $qty) {
                abort(409, 'Room availability changed');
            }

            $nightly = $pricing->nightlyPrices($roomType, $request->check_in, $request->check_out);
            $roomTotal = 0;
            foreach ($nightly as $night) {
                $roomTotal += ($night['price'] * 1);
            }

            $total += ($roomTotal * $qty);

            for ($i = 0; $i < $qty; $i++) {
                $selectedRooms[] = [
                    'cart_id' => $roomTypeId . "_" . $i,
                    'room' => $roomType,
                    'qty' => 1,
                    'total' => $roomTotal,
                    'nightly' => $nightly,
                    'max_extra_persons' => $roomType->max_extra_persons,
                    'extra_person_price' => $roomType->extra_person_price,
                    'base_occupancy' => $roomType->base_occupancy,
                ];
            }
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
                'mode' => site('payment_mode', 'hotel_only'),
                'deposit_type' => site('deposit_type', 'percentage'),
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
        $nights = \Carbon\Carbon::parse($request->check_in)->diffInDays($request->check_out);
        $checkIn = $request->check_in;
        $checkOut = $request->check_out;

        $tempTotal = 0;
        $servicesRequest = json_decode($request->services_json, true) ?? [];

        foreach ($request->rooms as $roomTypeId => $qty) {
            if ($qty < 1) continue;
            $roomType = RoomType::findOrFail($roomTypeId);
            if ($availability->maxAvailableRooms($roomType, $checkIn, $checkOut) < $qty) {
                abort(409, "Room availability changed");
            }

            $roomPrice = $pricing->calculate($roomType, $checkIn, $checkOut, 1);
            $tempTotal += ($roomPrice * $qty);

            for ($i = 0; $i < $qty; $i++) {
                $cartId = $roomTypeId . "_" . $i;
                $extraPersons = ($request->extra_persons[$cartId] ?? 0);
                $tempTotal += ($extraPersons * $roomType->extra_person_price * $nights);
            }
        }

        foreach ($servicesRequest as $item) {
            $price = (float) $item["price"];
            $itemQty = (int) ($item["qty"] ?? 1);
            $itemTotal = $price * $itemQty;
            if (($item["price_unit"] ?? "fixed") === "per_night") {
                $itemTotal *= $nights;
            }
            $tempTotal += $itemTotal;
        }

        $totalPlusUpsells = $tempTotal;

        $paymentEnabled = site("payment_feature_enabled", "0") === "1";
        $paymentMode = site("payment_mode", "hotel_only");
        $grandAdvancePaid = 0;

        if ($paymentEnabled && $request->payment_type === "online") {
            if ($paymentMode === "online_only") {
                $grandAdvancePaid = $totalPlusUpsells;
            } elseif ($paymentMode === "partial_deposit") {
                $depositType = site("deposit_type", "percentage");
                $depositValue = (float) site("deposit_value", 0);
                if ($depositType === "percentage") {
                    $grandAdvancePaid = ($totalPlusUpsells * $depositValue) / 100;
                } else {
                    $grandAdvancePaid = min($depositValue, $totalPlusUpsells);
                }
            }
        }

        $totalDiscount = (float) ($request->input("discount_amount") ?? 0);
        $couponId = $request->input("coupon_id");

        $totalRooms = 0;
        foreach ($request->rooms as $qty) $totalRooms += $qty;

        $firstBooking = null;
        $remainingAdvance = $grandAdvancePaid;
        $remainingDiscount = $totalDiscount;
        $processedRooms = 0;

        foreach ($request->rooms as $roomTypeId => $qty) {
            if ($qty < 1) continue;
            $roomType = RoomType::findOrFail($roomTypeId);

            for ($i = 0; $i < $qty; $i++) {
                $cartId = $roomTypeId . "_" . $i;
                $extraPersons = ($request->extra_persons[$cartId] ?? 0);

                $roomTypeServices = array_filter($servicesRequest, function ($s) use ($cartId) {
                    return (string) ($s["cart_id"] ?? "") === (string) $cartId;
                });

                $processedRooms++;
                $bookingAmount = $pricing->calculate($roomType, $checkIn, $checkOut, 1);
                $bookingAmount += ($extraPersons * $roomType->extra_person_price * $nights);

                $servicesTotal = 0;
                foreach ($roomTypeServices as $s) {
                    $st = $s["price"] * ($s["qty"] ?? 1);
                    if (($s["price_unit"] ?? "fixed") === "per_night") $st *= $nights;
                    $servicesTotal += $st;
                }
                $bookingAmount += $servicesTotal;

                if ($processedRooms === $totalRooms) {
                    $currentBookingDiscount = $remainingDiscount;
                } else {
                    $currentBookingDiscount = round($totalDiscount / $totalRooms, 2);
                    $remainingDiscount -= $currentBookingDiscount;
                }

                $bookingAmount -= $currentBookingDiscount;
                if ($bookingAmount < 0) $bookingAmount = 0;

                $advanceForThis = ($firstBooking === null) ? $remainingAdvance : 0;
                $isOnline = $request->payment_type === "online";

                $booking = \App\Models\Booking::create([
                    "hotel_id" => 1,
                    "group_id" => $groupId,
                    "room_type_id" => $roomType->id,
                    "check_in" => $checkIn,
                    "check_out" => $checkOut,
                    "rooms" => 1,
                    "total_amount" => $bookingAmount,
                    "discount_amount" => $currentBookingDiscount,
                    "coupon_id" => $couponId,
                    "services_json" => array_values($roomTypeServices),
                    "status" => "pending",
                    // Unpaid online bookings auto-expire and release inventory (bookings:expire)
                    "expires_at" => $isOnline ? now()->addMinutes(30) : null,
                    "meta" => [
                        "extra_persons" => $extraPersons,
                        // Advance is DUE until the gateway/webhook confirms it — never pre-credited
                        "advance_paid" => 0,
                        "advance_due" => $isOnline ? $advanceForThis : 0,
                        "source" => "guest_portal"
                    ],
                ]);

                \App\Models\BookingGuest::create([
                    "booking_id" => $booking->id,
                    "name" => $request->name,
                    "phone" => $request->phone,
                ]);

                if ($firstBooking === null) $firstBooking = $booking;
            }
        }

        if ($request->payment_type === "offline") {
            \App\Models\Booking::where("group_id", $groupId)->update(["status" => "confirmed"]);
            if ($firstBooking) {
                event(new \App\Events\BookingConfirmed($firstBooking->fresh()));
            }
            return redirect("/thank-you");
        }

        // Online: hold the booking as pending (30 min) and send the guest to the payment page.
        // Also WhatsApp the payment link so they can complete it from their phone.
        if ($firstBooking && $request->phone) {
            \App\Jobs\SendWhatsAppMessageJob::dispatch($request->phone, 'payment_link', [
                $request->name,
                site('hotel_name', 'our hotel'),
                route('payment.pay', $firstBooking),
            ]);
        }

        return redirect()->route('payment.pay', $firstBooking);
    }

    public function addRoom(Request $request)
    {
        $request->validate([
            "check_in" => "required|date",
            "check_out" => "required|date",
        ]);

        $currentRooms = [];
        if ($request->has("room_type_id")) {
            $currentRooms[$request->room_type_id] = (int) ($request->rooms ?? 1);
        } elseif ($request->has("rooms") && is_array($request->rooms)) {
            $currentRooms = $request->rooms;
        }

        $cart = session("temp_cart", []);
        foreach ($currentRooms as $rtId => $qty) {
            if (isset($cart[$rtId])) {
                $cart[$rtId] += $qty;
            } else {
                $cart[$rtId] = $qty;
            }
        }

        session(["temp_cart" => $cart]);

        return redirect()->route("rooms", [
            "check_in" => $request->check_in,
            "check_out" => $request->check_out,
        ]);
    }
}
