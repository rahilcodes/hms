<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected Api $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    public function createOrder(Booking $booking): array
    {
        return $this->razorpay->order->create([
            'receipt'  => 'booking_' . $booking->id,
            'amount'   => $booking->total_amount * 100,
            'currency' => 'INR',
        ])->toArray();
    }

    public function recordPayment(array $payload): Payment
    {
        return DB::transaction(function () use ($payload) {

            return Payment::updateOrCreate(
                [
                    'provider' => 'razorpay',
                    'provider_payment_id' => $payload['payment_id'],
                ],
                [
                    'booking_id' => $payload['booking_id'],
                    'amount'     => $payload['amount'],
                    'status'     => 'paid',
                ]
            );
        });
    }
}
