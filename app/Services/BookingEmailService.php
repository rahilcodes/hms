<?php

namespace App\Services;

use App\Models\Booking;
use App\Mail\GuestBookingConfirmed;
use App\Mail\GuestStayReminder;
use App\Mail\GuestWelcome;
use App\Mail\GuestPostStaySurvey;
use Illuminate\Support\Facades\Mail;

class BookingEmailService
{
    public function sendConfirmation(Booking $booking)
    {
        $email = $this->getGuestEmail($booking);
        if ($email) {
            Mail::to($email)->send(new GuestBookingConfirmed($booking));
        }
    }

    public function sendStayReminder(Booking $booking)
    {
        $email = $this->getGuestEmail($booking);
        if ($email) {
            Mail::to($email)->queue(new GuestStayReminder($booking));
        }
    }

    public function sendWelcome(Booking $booking)
    {
        $email = $this->getGuestEmail($booking);
        if ($email) {
            Mail::to($email)->send(new GuestWelcome($booking));
        }
    }

    public function sendSurvey(Booking $booking)
    {
        $email = $this->getGuestEmail($booking);
        if ($email) {
            Mail::to($email)->queue(new GuestPostStaySurvey($booking));
        }
    }

    protected function getGuestEmail(Booking $booking)
    {
        return $booking->meta['guest_email'] ?? $booking->guests->first()->email ?? null;
    }
}
