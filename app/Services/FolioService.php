<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FolioItem;
use App\Models\Payment;
use Carbon\Carbon;

/**
 * Keeps the folio (folio_items) as the single source of truth for a booking.
 * All methods are idempotent so they can run on every folio/invoice view.
 */
class FolioService
{
    public static function sync(Booking $booking): void
    {
        $servicesJson = is_array($booking->services_json)
            ? $booking->services_json
            : json_decode($booking->services_json ?? '[]', true);

        $extrasTotal = 0;
        if (is_array($servicesJson)) {
            foreach ($servicesJson as $s) {
                $itemTotal = ($s['price'] ?? 0) * ($s['qty'] ?? 1);
                if (($s['price_unit'] ?? 'fixed') === 'per_night') {
                    $itemTotal *= $booking->nights;
                }
                $extrasTotal += $itemTotal;
            }
        }

        $extraPersonsCount = $booking->meta['extra_persons'] ?? 0;
        if ($extraPersonsCount > 0) {
            $epPrice = $booking->roomType->extra_person_price ?? 0;
            $extrasTotal += ($extraPersonsCount * $epPrice * $booking->nights);
        }

        $childrenCount = $booking->meta['children'] ?? 0;
        if ($childrenCount > 0) {
            $childPrice = $booking->roomType->child_price ?? 0;
            $extrasTotal += ($childrenCount * $childPrice * $booking->nights);
        }

        // 1. Room rent, one line per night (or a single day-use line)
        $rentExists = FolioItem::where('booking_id', $booking->id)->where('type', 'room_rent')->exists();
        if (!$rentExists) {
            $baseTotal = $booking->total_amount - $extrasTotal + ($booking->discount_amount ?? 0);

            if ($booking->isDayUse()) {
                FolioItem::create([
                    'hotel_id' => $booking->hotel_id,
                    'booking_id' => $booking->id,
                    'type' => 'room_rent',
                    'description' => 'Day Use (' . ($booking->day_use_hours ?? 4) . ' hrs)',
                    'amount' => $baseTotal,
                    'reference_date' => $booking->check_in->toDateString(),
                    'posted_at' => $booking->created_at,
                ]);
            } elseif ($booking->nights > 0) {
                $dailyRent = $baseTotal / $booking->nights;
                $currentDate = Carbon::parse($booking->check_in);
                for ($i = 0; $i < $booking->nights; $i++) {
                    FolioItem::create([
                        'hotel_id' => $booking->hotel_id,
                        'booking_id' => $booking->id,
                        'type' => 'room_rent',
                        'description' => 'Base Room Rent',
                        'amount' => $dailyRent,
                        'reference_date' => $currentDate->toDateString(),
                        'posted_at' => $booking->created_at,
                    ]);
                    $currentDate->addDay();
                }
            }
        }

        // 2. Pre-booked add-ons
        if (is_array($servicesJson)) {
            foreach ($servicesJson as $s) {
                $desc = 'Pre-booked: ' . ($s['name'] ?? 'Service');
                $exists = FolioItem::where('booking_id', $booking->id)
                    ->where('type', 'service')->where('description', $desc)->exists();
                if (!$exists) {
                    $itemTotal = ($s['price'] ?? 0) * ($s['qty'] ?? 1);
                    if (($s['price_unit'] ?? 'fixed') === 'per_night') {
                        $itemTotal *= $booking->nights;
                    }
                    FolioItem::create([
                        'hotel_id' => $booking->hotel_id,
                        'booking_id' => $booking->id,
                        'type' => 'service',
                        'description' => $desc,
                        'amount' => $itemTotal,
                        'posted_at' => $booking->created_at,
                        'metadata' => ['service_id' => $s['id'] ?? null, 'source' => 'sync'],
                    ]);
                }
            }
        }

        // 3. Extra persons
        if ($extraPersonsCount > 0) {
            $epDesc = "Extra Persons ({$extraPersonsCount})";
            $epExists = FolioItem::where('booking_id', $booking->id)
                ->where('type', 'service')->where('description', $epDesc)->exists();
            if (!$epExists) {
                $epPrice = $booking->roomType->extra_person_price ?? 0;
                FolioItem::create([
                    'hotel_id' => $booking->hotel_id,
                    'booking_id' => $booking->id,
                    'type' => 'service',
                    'description' => $epDesc,
                    'amount' => ($extraPersonsCount * $epPrice * $booking->nights),
                    'posted_at' => $booking->created_at,
                    'metadata' => ['source' => 'sync_extra_persons'],
                ]);
            }
        }

        // 3b. Children (5-12 yrs)
        if ($childrenCount > 0) {
            $chDesc = "Children 5-12 yrs ({$childrenCount})";
            $chExists = FolioItem::where('booking_id', $booking->id)
                ->where('type', 'service')->where('description', $chDesc)->exists();
            if (!$chExists) {
                $childPrice = $booking->roomType->child_price ?? 0;
                FolioItem::create([
                    'hotel_id' => $booking->hotel_id,
                    'booking_id' => $booking->id,
                    'type' => 'service',
                    'description' => $chDesc,
                    'amount' => ($childrenCount * $childPrice * $booking->nights),
                    'posted_at' => $booking->created_at,
                    'metadata' => ['source' => 'sync_children'],
                ]);
            }
        }

        // 4. Room service / dining orders (normally posted by the model hook; backfill any gaps)
        foreach ($booking->roomServiceOrders as $order) {
            if ($order->status === 'cancelled') {
                continue;
            }
            $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
            if (!is_array($items)) {
                continue;
            }
            foreach ($items as $item) {
                $desc = 'Order #' . (5000 + $order->id) . ': ' . ($item['name'] ?? 'Service');
                $orderExists = FolioItem::where('booking_id', $booking->id)
                    ->where('type', 'service')
                    ->where('metadata->order_id', $order->id)
                    ->where('description', $desc)
                    ->exists();
                if (!$orderExists) {
                    FolioItem::create([
                        'hotel_id' => $booking->hotel_id,
                        'booking_id' => $booking->id,
                        'type' => 'service',
                        'description' => $desc,
                        'amount' => ($item['price'] ?? 0) * ($item['qty'] ?? 1),
                        'posted_at' => $order->created_at,
                        'metadata' => [
                            'source' => 'room_service_order',
                            'order_type' => $order->type,
                            'order_id' => $order->id,
                            'service_id' => $item['id'] ?? null,
                        ],
                    ]);
                }
            }
        }

        // 5. Gateway payments
        $payments = Payment::where('booking_id', $booking->id)->get();
        foreach ($payments as $payment) {
            FolioItem::firstOrCreate([
                'booking_id' => $booking->id,
                'type' => 'payment',
                'amount' => $payment->amount,
            ], [
                'hotel_id' => $booking->hotel_id,
                'description' => 'Payment via ' . $payment->provider,
                'posted_at' => $payment->created_at,
                'metadata' => ['payment_id' => $payment->id],
            ]);
        }

        // 6. Discounts
        if ($booking->discount_amount > 0) {
            FolioItem::firstOrCreate([
                'booking_id' => $booking->id,
                'type' => 'discount',
                'amount' => $booking->discount_amount,
            ], [
                'hotel_id' => $booking->hotel_id,
                'description' => 'Promo Discount',
                'posted_at' => $booking->created_at,
            ]);
        }
    }
}
