<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FolioItem;
use App\Models\TaxRule;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;

class NightAuditService
{
    /**
     * Perform the night audit for all active bookings.
     */
    public function performAudit($date = null)
    {
        $auditDate = $date ?Carbon::parse($date) : Carbon::today()->subDay(); // Audit for yesterday
        $results = [
            'bookings_processed' => 0,
            'charges_posted' => 0,
            'taxes_posted' => 0,
            'errors' => []
        ];

        // 1. Fetch all bookings that were in-house during the audit date
        $bookings = Booking::whereIn('status', ['confirmed', 'checked_in'])
            ->whereDate('check_in', '<=', $auditDate)
            ->whereDate('check_out', '>', $auditDate) // They stayed this night
            ->get();

        foreach ($bookings as $booking) {
            try {
                DB::transaction(function () use ($booking, $auditDate, &$results) {
                    // 2. Check if rent already posted for this date
                    $exists = FolioItem::where('booking_id', $booking->id)
                        ->where('type', 'room_rent')
                        ->whereDate('reference_date', $auditDate)
                        ->exists();

                    if (!$exists) {
                        $roomType = $booking->roomType;
                        $roomCount = is_array($booking->rooms) ? ($booking->rooms[$booking->room_type_id] ?? 1) : $booking->rooms;
                        $dailyPrice = $roomType->base_price * $roomCount; // Basic daily price

                        // Post Room Rent
                        $rentItem = FolioItem::create([
                            'hotel_id' => $booking->hotel_id,
                            'booking_id' => $booking->id,
                            'type' => 'room_rent',
                            'description' => "Room Rent - {$roomType->name} (Night of " . $auditDate->format('M d, Y') . ")",
                            'amount' => $dailyPrice,
                            'reference_date' => $auditDate,
                            'posted_at' => now(),
                        ]);

                        $results['charges_posted']++;

                        // 3. Calculate and Apply Taxes
                        $taxes = $this->applyTaxes($booking, $rentItem, 'accommodation');
                        $results['taxes_posted'] += count($taxes);

                        $results['bookings_processed']++;
                    }
                });
            }
            catch (\Exception $e) {
                $results['errors'][] = "Booking #{$booking->id}: " . $e->getMessage();
            }
        }

        ActivityLog::log('Night Audit Performed', "Processed {$results['bookings_processed']} bookings for {$auditDate->toDateString()}. Posted {$results['charges_posted']} charges.");

        return $results;
    }

    /**
     * Apply relevant taxes to a folio item.
     */
    protected function applyTaxes(Booking $booking, FolioItem $parentItem, $category)
    {
        $postedTaxes = [];
        $taxRules = TaxRule::where('hotel_id', $booking->hotel_id)
            ->where('is_active', true)
            ->where(function ($q) use ($category) {
            $q->where('category', $category)->orWhere('category', 'any');
        })
            ->get();

        foreach ($taxRules as $rule) {
            // Check thresholds
            if ($parentItem->amount < $rule->min_threshold)
                continue;
            if ($rule->max_threshold && $parentItem->amount > $rule->max_threshold)
                continue;

            $taxAmount = 0;
            if ($rule->type === 'percentage') {
                $taxAmount = $parentItem->amount * ($rule->value / 100);
            }
            else {
                $taxAmount = $rule->value;
            }

            if ($taxAmount > 0) {
                $postedTaxes[] = FolioItem::create([
                    'hotel_id' => $booking->hotel_id,
                    'booking_id' => $booking->id,
                    'type' => 'tax',
                    'description' => "{$rule->name} on {$parentItem->description}",
                    'amount' => $taxAmount,
                    'reference_date' => $parentItem->reference_date,
                    'posted_at' => now(),
                    'metadata' => [
                        'tax_rule_id' => $rule->id,
                        'parent_folio_item_id' => $parentItem->id
                    ]
                ]);
            }
        }

        return $postedTaxes;
    }
}
