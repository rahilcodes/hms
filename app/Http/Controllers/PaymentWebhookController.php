<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Services\PaymentService;
use App\Services\BookingService;

class PaymentWebhookController extends Controller
{
    public function handle(
        Request $request,
        PaymentService $paymentService,
        BookingService $bookingService
    ) {
        $payload = $request->all();

        if (!isset($payload['payload']['payment']['entity'])) {
            return response()->json(['ignored'], 200);
        }

        $payment = $payload['payload']['payment']['entity'];

        $bookingId = str_replace('booking_', '', $payment['order_id']);

        $booking = Booking::findOrFail($bookingId);

        $paymentService->recordPayment([
            'booking_id' => $booking->id,
            'payment_id' => $payment['id'],
            'amount'     => $payment['amount'] / 100,
        ]);

        $bookingService->confirm($booking);

        return response()->json(['success'], 200);
    }
}
