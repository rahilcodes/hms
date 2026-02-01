<?php

namespace App\Services;

use App\Models\RoomType;
use App\Models\PricingRule;
use Carbon\Carbon;

class PricingService
{
    public function calculate(
        RoomType $roomType,
        string $checkIn,
        string $checkOut,
        int $rooms
    ): float {
        $total = 0;

        foreach ($this->nightlyPrices($roomType, $checkIn, $checkOut) as $night) {
            $total += ($night['price'] * $rooms);
        }

        return round($total, 2);
    }

    /**
     * Returns per-night pricing breakdown
     */
    public function nightlyPrices(
        RoomType $roomType,
        string $checkIn,
        string $checkOut
    ): array {
        $nights = [];
        $startDateStr = Carbon::parse($checkIn)->toDateString();
        $endDateStr = Carbon::parse($checkOut)->toDateString();

        $date = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);

        // Fetch all relevant seasonal rules for this room type in the range
        $seasonalRules = PricingRule::where('room_type_id', $roomType->id)
            ->where('type', 'season')
            ->where(function ($q) use ($startDateStr, $endDateStr) {
                $q->whereBetween('start_date', [$startDateStr, $endDateStr])
                    ->orWhereBetween('end_date', [$startDateStr, $endDateStr])
                    ->orWhere(function ($sq) use ($startDateStr, $endDateStr) {
                        $sq->where('start_date', '<=', $startDateStr)
                            ->where('end_date', '>=', $endDateStr);
                    });
            })
            ->get();

        // Fetch weekend rules (usually global but scoped to room type here)
        $weekendRule = PricingRule::where('room_type_id', $roomType->id)
            ->where('type', 'weekend')
            ->first();

        while ($date->lt($end)) {
            $currentDateStr = $date->toDateString();
            $price = $roomType->base_price;

            // 1. Seasonal check (highest priority)
            $seasonRule = $seasonalRules->first(function ($rule) use ($currentDateStr) {
                return $rule->start_date->toDateString() <= $currentDateStr &&
                    $rule->end_date->toDateString() >= $currentDateStr;
            });

            if ($seasonRule) {
                $price = $seasonRule->price;
            } elseif ($date->isWeekend() && $weekendRule) {
                // 2. Weekend check
                $price = $weekendRule->price;
            }

            $nights[] = [
                'date' => $currentDateStr,
                'label' => $date->format('d M (D)'),
                'price' => (float) $price,
            ];

            $date->addDay();
        }

        return $nights;
    }
}
