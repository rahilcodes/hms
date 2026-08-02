<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$booking = App\Models\Booking::with('guests')->latest()->first();

if ($booking) {
    echo "CheckIn: " . $booking->check_in->format('Y-m-d') . "\n";
    echo "Identity: " . ($booking->meta['guest_email'] ?? $booking->guests->first()->email ?? 'No Email') . "\n";
} else {
    echo "No bookings found.\n";
}
