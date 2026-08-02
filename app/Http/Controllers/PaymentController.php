<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function pay(Booking $booking)
    {
        // Only pending bookings can be paid online
        if ($booking->status !== 'pending') {
            abort(404);
        }

        // Amount due now: the configured advance if set, else the full amount
        $amountDue = (float) ($booking->meta['advance_due'] ?? 0);
        if ($amountDue <= 0) {
            $amountDue = (float) $booking->total_amount;
        }

        // Razorpay when configured…
        if (PaymentService::configured()) {
            $order = app(PaymentService::class)->createOrder($booking, $amountDue);

            return view('payment.pay', [
                'booking' => $booking,
                'order' => $order,
                'amountDue' => $amountDue,
            ]);
        }

        // …otherwise fall back to a UPI QR: guest pays the hotel's VPA directly,
        // the front desk verifies and confirms with "Record Payment".
        $upi = upi_uri($amountDue, 'Booking #' . ($booking->id + 1000));

        return view('payment.upi', [
            'booking' => $booking,
            'amountDue' => $amountDue,
            'upi' => $upi,
        ]);
    }
}
