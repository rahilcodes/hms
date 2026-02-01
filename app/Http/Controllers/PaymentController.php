<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function pay(Booking $booking, PaymentService $paymentService)
    {
        // Only pending bookings can be paid online
        if ($booking->status !== 'pending') {
            abort(404);
        }

        $order = $paymentService->createOrder($booking);

        return view('payment.pay', [
            'booking' => $booking,
            'order' => $order,
        ]);
    }
}
