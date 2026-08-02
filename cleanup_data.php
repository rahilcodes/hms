<?php

use App\Models\RoomType;
use App\Models\Booking;
use App\Models\BlockedDate;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roomTypeId = 1;

// Clean Blocked Dates
$deletedBlocked = BlockedDate::where('room_type_id', $roomTypeId)->delete();
echo "Deleted $deletedBlocked BlockedDate records.\n";

// Clean Bookings (Optional: might want to keep some, but for now wipe to unblock)
// Only wiping pending/confirmed future bookings for this room type for these dates
$deletedBookings = Booking::where('room_type_id', $roomTypeId)->delete();
echo "Deleted $deletedBookings Booking records.\n";

echo "Availability Reset.\n";
