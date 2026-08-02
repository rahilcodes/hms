<?php

use App\Models\SiteSetting;

if (!function_exists('site')) {
    function site(string $key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            // 🚀 TITANIUM OPTIMIZATION: Load ALL settings in 1 query
            // Instead of N+1 queries, we fetch everything once and cache it in memory.
            try {
                $settings = \App\Models\SiteSetting::all()->pluck('value', 'key')->toArray();
            } catch (\Exception $e) {
                $settings = [];
            }
        }

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('amount_in_words')) {
    /**
     * Convert an amount to words using the Indian numbering system
     * (crore / lakh / thousand). Required on GST invoices.
     */
    function amount_in_words(float $amount): string
    {
        $rupees = (int) floor($amount);
        $paise = (int) round(($amount - $rupees) * 100);

        $toWords = function (int $n) use (&$toWords): string {
            $ones = [
                '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
                'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen',
                'Eighteen', 'Nineteen',
            ];
            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            if ($n < 20) {
                return $ones[$n];
            }
            if ($n < 100) {
                return trim($tens[intdiv($n, 10)] . ' ' . $ones[$n % 10]);
            }
            if ($n < 1000) {
                return trim($ones[intdiv($n, 100)] . ' Hundred ' . $toWords($n % 100));
            }
            if ($n < 100000) {
                return trim($toWords(intdiv($n, 1000)) . ' Thousand ' . $toWords($n % 1000));
            }
            if ($n < 10000000) {
                return trim($toWords(intdiv($n, 100000)) . ' Lakh ' . $toWords($n % 100000));
            }
            return trim($toWords(intdiv($n, 10000000)) . ' Crore ' . $toWords($n % 10000000));
        };

        $words = $rupees > 0 ? 'Rupees ' . $toWords($rupees) : 'Rupees Zero';
        if ($paise > 0) {
            $words .= ' and ' . $toWords($paise) . ' Paise';
        }

        return $words . ' Only';
    }
}

if (!function_exists('upi_uri')) {
    /**
     * Build a upi:// deep link (encodes into a scannable QR) for the hotel's VPA.
     * Returns null when no UPI ID is configured in Site Settings.
     */
    function upi_uri(float $amount, string $note = ''): ?string
    {
        $vpa = trim((string) site('hotel_upi_vpa', ''));
        if ($vpa === '') {
            return null;
        }
        $params = [
            'pa' => $vpa,
            'pn' => site('hotel_upi_name', site('hotel_name', 'Hotel')),
            'cu' => 'INR',
        ];
        if ($amount > 0) {
            $params['am'] = number_format($amount, 2, '.', '');
        }
        if ($note !== '') {
            $params['tn'] = mb_substr($note, 0, 50);
        }

        return 'upi://pay?' . http_build_query($params);
    }
}

if (!function_exists('business_date')) {
    function business_date(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse(site('business_date', now()->toDateString()));
    }
}

if (!function_exists('day_is_closed')) {
    /**
     * A calendar day is "closed" once the night audit has advanced the business
     * date past it. Back-dated financial edits on closed days are blocked for
     * everyone except super admins.
     */
    function day_is_closed($date): bool
    {
        if (site('day_close_lock', '1') !== '1') {
            return false;
        }
        return \Carbon\Carbon::parse($date)->startOfDay()->lt(business_date()->startOfDay());
    }
}
