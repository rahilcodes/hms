<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Jobs\SendWhatsAppMessageJob;

class SendBookingConfirmationWhatsApp
{
    public function handle(BookingConfirmed $event): void
    {
        $guest = $event->booking->guests()->first();

        if (!$guest) {
            return;
        }

        SendWhatsAppMessageJob::dispatch(
            $guest->phone,
            'booking_confirmation',
            [
                $event->booking->id,
                $event->booking->check_in->format('d M Y'),
                $event->booking->check_out->format('d M Y'),
            ]
        );
    }
}
