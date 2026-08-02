<?php

namespace App\Services;

use App\Models\BookingGuest;

/**
 * Lightweight repeat-guest recognition keyed by phone number.
 * Tiers reward direct repeat business — the hotel's anti-OTA weapon.
 */
class LoyaltyService
{
    public const TIERS = [
        // min completed stays => [tier, suggested direct-booking discount %]
        10 => ['name' => 'Platinum', 'discount' => 15, 'color' => 'violet'],
        6 => ['name' => 'Gold', 'discount' => 10, 'color' => 'amber'],
        3 => ['name' => 'Silver', 'discount' => 5, 'color' => 'slate'],
    ];

    /**
     * @return array{stays:int, tier:?string, discount:int, color:?string, next_tier_at:?int}
     */
    public static function profile(?string $phone): array
    {
        $stays = 0;
        if ($phone !== null && trim($phone) !== '') {
            $stays = BookingGuest::where('phone', $phone)
                ->whereHas('booking', fn ($q) => $q->where('status', 'checked_out'))
                ->count();
        }

        $tier = null;
        foreach (self::TIERS as $min => $t) {
            if ($stays >= $min) {
                $tier = $t + ['min' => $min];
                break;
            }
        }

        $nextAt = null;
        foreach (array_reverse(self::TIERS, true) as $min => $t) {
            if ($stays < $min) {
                $nextAt = $min;
                break;
            }
        }

        return [
            'stays' => $stays,
            'tier' => $tier['name'] ?? null,
            'discount' => $tier['discount'] ?? 0,
            'color' => $tier['color'] ?? null,
            'next_tier_at' => $nextAt,
        ];
    }
}
