<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$booking = \Illuminate\Support\Facades\DB::table('bookings')->where('id', 12)->first();
echo "Raw Meta: " . $booking->meta . "\n";
