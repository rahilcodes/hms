<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncFolioData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'folio:sync';

    protected $description = 'Synchronize existing booking data into the FolioItem ledger';

    public function handle()
    {
        $bookings = \App\Models\Booking::all();
        $this->info("Syncing Folio data for " . $bookings->count() . " bookings...");

        foreach ($bookings as $booking) {
            // 1. Extract known extras from total_amount for better rent calculation
            $servicesJson = is_array($booking->services_json) ? $booking->services_json : json_decode($booking->services_json ?? '[]', true);
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
                // We need the room type to get the extra person price if not stored
                $epPrice = $booking->roomType->extra_person_price ?? 0;
                $extrasTotal += ($extraPersonsCount * $epPrice * $booking->nights);
            }

            // 1. Backfill Room Rent (Base only)
            if ($booking->nights > 0) {
                $rentExists = \App\Models\FolioItem::where('booking_id', $booking->id)->where('type', 'room_rent')->exists();

                if (!$rentExists) {
                    $baseTotal = $booking->total_amount - $extrasTotal + ($booking->discount_amount ?? 0);
                    $dailyRent = $baseTotal / $booking->nights;
                    $currentDate = \Carbon\Carbon::parse($booking->check_in);

                    for ($i = 0; $i < $booking->nights; $i++) {
                        \App\Models\FolioItem::create([
                            'hotel_id' => $booking->hotel_id,
                            'booking_id' => $booking->id,
                            'type' => 'room_rent',
                            'description' => "Base Room Rent",
                            'amount' => $dailyRent,
                            'reference_date' => $currentDate->toDateString(),
                            'posted_at' => $booking->created_at,
                        ]);
                        $currentDate->addDay();
                    }
                }
            }

            // 2. Backfill Pre-booked Add-ons (from services_json)
            if (is_array($servicesJson)) {
                foreach ($servicesJson as $s) {
                    $desc = "Pre-booked: " . ($s['name'] ?? 'Service');
                    $exists = \App\Models\FolioItem::where('booking_id', $booking->id)
                        ->where('type', 'service')
                        ->where('description', $desc)
                        ->exists();

                    if (!$exists) {
                        $itemTotal = ($s['price'] ?? 0) * ($s['qty'] ?? 1);
                        if (($s['price_unit'] ?? 'fixed') === 'per_night') {
                            $itemTotal *= $booking->nights;
                        }

                        \App\Models\FolioItem::create([
                            'hotel_id' => $booking->hotel_id,
                            'booking_id' => $booking->id,
                            'type' => 'service',
                            'description' => $desc,
                            'amount' => $itemTotal,
                            'posted_at' => $booking->created_at,
                            'metadata' => ['service_id' => $s['id'] ?? null, 'source' => 'sync']
                        ]);
                    }
                }
            }

            // 3. Backfill Extra Persons
            if ($extraPersonsCount > 0) {
                $epDesc = "Extra Persons ({$extraPersonsCount})";
                $epExists = \App\Models\FolioItem::where('booking_id', $booking->id)
                    ->where('type', 'service')
                    ->where('description', $epDesc)
                    ->exists();

                if (!$epExists) {
                    $epPrice = $booking->roomType->extra_person_price ?? 0;
                    \App\Models\FolioItem::create([
                        'hotel_id' => $booking->hotel_id,
                        'booking_id' => $booking->id,
                        'type' => 'service',
                        'description' => $epDesc,
                        'amount' => ($extraPersonsCount * $epPrice * $booking->nights),
                        'posted_at' => $booking->created_at,
                        'metadata' => ['source' => 'sync_extra_persons']
                    ]);
                }
            }

            // 4. Backfill Service Orders
            foreach ($booking->roomServiceOrders as $order) {
                $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                if (!is_array($items))
                    continue;

                foreach ($items as $item) {
                    $desc = "Order #" . (5000 + $order->id) . ": " . ($item['name'] ?? 'Service');

                    $orderExists = \App\Models\FolioItem::where('booking_id', $booking->id)
                        ->where('type', 'service')
                        ->where('metadata->order_id', $order->id)
                        ->where('description', $desc)
                        ->exists();

                    if (!$orderExists) {
                        \App\Models\FolioItem::create([
                            'hotel_id' => $booking->hotel_id,
                            'booking_id' => $booking->id,
                            'type' => 'service',
                            'description' => $desc,
                            'amount' => ($item['price'] ?? 0) * ($item['qty'] ?? 1),
                            'posted_at' => $order->created_at,
                            'metadata' => [
                                'source' => 'sync_room_service',
                                'order_id' => $order->id,
                                'service_id' => $item['id'] ?? null
                            ]
                        ]);
                    }
                }
            }

            // 6. Backfill Payments
            $payments = \App\Models\Payment::where('booking_id', $booking->id)->get();
            foreach ($payments as $payment) {
                \App\Models\FolioItem::firstOrCreate([
                    'booking_id' => $booking->id,
                    'type' => 'payment',
                    'amount' => $payment->amount,
                ], [
                    'hotel_id' => $booking->hotel_id,
                    'description' => "Payment via " . $payment->provider,
                    'posted_at' => $payment->created_at,
                ]);
            }

            // 7. Backfill Discounts
            if ($booking->discount_amount > 0) {
                \App\Models\FolioItem::firstOrCreate([
                    'booking_id' => $booking->id,
                    'type' => 'discount',
                    'amount' => $booking->discount_amount,
                ], [
                    'hotel_id' => $booking->hotel_id,
                    'description' => "Promo Discount",
                    'posted_at' => $booking->created_at,
                ]);
            }
        }

        $this->info("Folio sync completed!");
        return 0;
    }
}
