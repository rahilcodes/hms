<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\BookingEmailService;

class SendGuestStayReminders extends Command
{
    protected $signature = 'guest:send-reminders';
    protected $description = 'Send stay reminders to guests arriving tomorrow';

    public function handle(BookingEmailService $emailService)
    {
        $tomorrow = now()->addDay()->format('Y-m-d');

        $bookings = Booking::whereDate('check_in', $tomorrow)
            ->where('status', 'confirmed')
            ->whereNull('checked_in_at')
            ->get();

        $this->info("Found {$bookings->count()} bookings for tomorrow.");

        foreach ($bookings as $booking) {
            // Avoid double sending if already sent in meta
            if ($booking->meta['reminder_sent'] ?? false) {
                continue;
            }

            $this->comment("Sending reminder to #{$booking->id}...");
            $emailService->sendStayReminder($booking);

            // Mark as sent
            $meta = $booking->meta;
            $meta['reminder_sent'] = true;
            $booking->update(['meta' => $meta]);
        }

        $this->info('Reminders sent successfully.');
    }
}
