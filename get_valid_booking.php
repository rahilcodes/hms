<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find a booking that actually has an email in meta or guests
$booking = App\Models\Booking::whereNotNull('meta->guest_email')
    ->orWhereHas('guests', function($q) {
        $q->whereNotNull('email');
    })
    ->latest()
    ->first();

if ($booking) {
    $email = $booking->meta['guest_email'] ?? $booking->guests->first()->email;
    echo "ID: " . $booking->id . "\n";
    echo "CheckIn: " . $booking->check_in->format('Y-m-d') . "\n";
    echo "Identity: " . $email . "\n";
} else {
    echo "No valid bookings found with email.\n";
}
