<?php

namespace App\Services;

use App\Models\BanquetBooking;
use App\Models\Booking;

/**
 * Computes a GST breakdown for invoices. Prices in the system are treated as
 * GST-INCLUSIVE (the common way Indian hotel tariffs and menus are quoted),
 * so the taxable value is back-calculated: taxable = gross / (1 + rate).
 */
class GstService
{
    public static function enabled(): bool
    {
        return site('gst_enabled', '1') === '1' && trim((string) site('hotel_gstin', '')) !== '';
    }

    /**
     * Per-night declared tariff decides the room GST slab.
     */
    public static function roomRate(Booking $booking): float
    {
        $nights = max(1, (int) $booking->nights);
        $roomRent = $booking->folioItems->where('type', 'room_rent')->sum('amount');
        if ($roomRent <= 0) {
            $roomRent = (float) $booking->total_amount;
        }
        $perNight = $roomRent / $nights;

        return $perNight <= config('gst.room_slab_threshold')
            ? (float) config('gst.room_rate_low')
            : (float) config('gst.room_rate_high');
    }

    protected static function categoryFor($folioItem, float $roomRate): array
    {
        // returns [rate, sac]
        if ($folioItem->type === 'room_rent') {
            return [$roomRate, config('gst.sac.room')];
        }
        $source = data_get($folioItem->metadata, 'source');
        $orderType = data_get($folioItem->metadata, 'order_type', 'dining');
        if ($source === 'room_service_order' && $orderType === 'dining') {
            return [(float) config('gst.food_rate'), config('gst.sac.food')];
        }
        return [(float) config('gst.service_rate'), config('gst.sac.service')];
    }

    protected static function line(string $description, float $gross, float $rate, string $sac): array
    {
        $taxable = $rate > 0 ? $gross / (1 + $rate / 100) : $gross;
        $tax = $gross - $taxable;

        return [
            'description' => $description,
            'sac' => $sac,
            'gross' => round($gross, 2),
            'taxable' => round($taxable, 2),
            'rate' => $rate,
            'cgst' => round($tax / 2, 2),
            'sgst' => round($tax / 2, 2),
            'tax' => round($tax, 2),
        ];
    }

    protected static function summarize(array $lines): array
    {
        $byRate = [];
        foreach ($lines as $l) {
            $key = (string) $l['rate'];
            $byRate[$key] ??= ['rate' => $l['rate'], 'taxable' => 0, 'cgst' => 0, 'sgst' => 0];
            $byRate[$key]['taxable'] += $l['taxable'];
            $byRate[$key]['cgst'] += $l['cgst'];
            $byRate[$key]['sgst'] += $l['sgst'];
        }
        ksort($byRate);

        return [
            'lines' => $lines,
            'by_rate' => array_values($byRate),
            'taxable_total' => round(array_sum(array_column($lines, 'taxable')), 2),
            'cgst_total' => round(array_sum(array_column($lines, 'cgst')), 2),
            'sgst_total' => round(array_sum(array_column($lines, 'sgst')), 2),
            'tax_total' => round(array_sum(array_column($lines, 'tax')), 2),
            'gross_total' => round(array_sum(array_column($lines, 'gross')), 2),
        ];
    }

    /**
     * GST breakdown for a room booking's folio. Groups room rent into one line,
     * keeps every other charge as its own line. Ignores payments/discount/tax rows
     * (discount is applied against the room line).
     */
    public static function forBooking(Booking $booking): array
    {
        $booking->loadMissing('folioItems');
        $roomRate = self::roomRate($booking);
        $lines = [];

        $charges = $booking->folioItems->whereNotIn('type', ['payment', 'discount', 'payment_refund', 'tax']);

        $roomGross = $charges->where('type', 'room_rent')->sum('amount') - (float) $booking->discount_amount;
        if ($roomGross > 0) {
            $label = $booking->isDayUse()
                ? 'Room Charges - Day Use (' . ($booking->day_use_hours ?? 4) . ' hrs)'
                : 'Room Charges (' . $booking->nights . ' night' . ($booking->nights > 1 ? 's' : '') . ')';
            if ($booking->discount_amount > 0) {
                $label .= ' less discount';
            }
            $lines[] = self::line($label, (float) $roomGross, $roomRate, config('gst.sac.room'));
        }

        foreach ($charges->where('type', '!=', 'room_rent')->sortBy('posted_at') as $item) {
            if ((float) $item->amount == 0.0) {
                continue;
            }
            [$rate, $sac] = self::categoryFor($item, $roomRate);
            $lines[] = self::line($item->description, (float) $item->amount, $rate, $sac);
        }

        return self::summarize($lines);
    }

    public static function forBanquet(BanquetBooking $event): array
    {
        $lines = [];
        $hallRate = (float) config('gst.banquet_hall_rate');
        $foodRate = (float) config('gst.banquet_food_rate');
        $sac = config('gst.sac.banquet');

        if ($event->food_total > 0) {
            $lines[] = self::line(
                "Catering: {$event->food_plates} plates @ " . number_format((float) $event->per_plate_rate, 2),
                $event->food_total,
                $foodRate,
                config('gst.sac.food')
            );
        }
        if ((float) $event->hall_rent > 0) {
            $lines[] = self::line('Hall Rent - ' . ($event->hall->name ?? 'Banquet Hall'), (float) $event->hall_rent, $hallRate, $sac);
        }
        if ((float) $event->decoration_charge > 0) {
            $lines[] = self::line('Decoration', (float) $event->decoration_charge, $hallRate, $sac);
        }
        $other = (float) $event->other_charges - (float) $event->discount;
        if ($other != 0.0) {
            $lines[] = self::line('Other Charges' . ($event->discount > 0 ? ' less discount' : ''), $other, $hallRate, $sac);
        }

        return self::summarize($lines);
    }
}
