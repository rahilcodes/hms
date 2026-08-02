<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$booking = App\Models\Booking::with('guests')->find(12);
echo "Guest Count: " . $booking->guests->count() . "\n";
foreach ($booking->guests as $guest) {
    echo "Guest ID: " . $guest->id . "\n";
    echo "Email: " . $guest->email . "\n";
    echo "Phone: " . $guest->phone . "\n";
}
