<?php

use App\Models\RoomType;
use App\Models\Booking;
use App\Services\AvailabilityService;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roomTypeId = 1;
$checkIn = '2026-01-28';
$checkOut = '2026-01-30';

$roomType = RoomType::find($roomTypeId);
if (!$roomType) {
    echo "RoomType $roomTypeId not found!\n";
    exit;
}

echo "Room: {$roomType->name}\n";
echo "Total Rooms: {$roomType->total_rooms}\n";

$service = new AvailabilityService();
$max = $service->maxAvailableRooms($roomType, $checkIn, $checkOut);

echo "Max Available from Service: $max\n";

// Manual Breakdown
// Manual Breakdown
$dates = [];
$start = \Carbon\Carbon::parse($checkIn);
$end = \Carbon\Carbon::parse($checkOut);
while ($start->lt($end)) {
    $dates[] = $start->toDateString();
    $start->addDay();
}
// Replicate loop
foreach ($dates as $date) {
    echo "\nDate: $date\n";

    $booked = Booking::where('room_type_id', $roomType->id)
        ->whereIn('status', ['pending', 'confirmed'])
        ->whereDate('check_in', '<=', $date)
        ->whereDate('check_out', '>', $date)
        ->sum('rooms');
    echo "  Booked: $booked\n";

    $blocked = \App\Models\BlockedDate::where('room_type_id', $roomType->id)
        ->whereDate('date', $date)
        ->sum('blocked_rooms');
    echo "  Blocked: $blocked\n";

    // Check if maintenance class exists before querying
    if (class_exists(\App\Models\RoomMaintenance::class)) {
        $maintenance = \App\Models\RoomMaintenance::where('room_type_id', $roomType->id)
            ->where('status', 'ongoing')
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->sum('rooms_count');
        echo "  Maintenance: $maintenance\n";
    } else {
        echo "  Maintenance: Class not found\n";
    }
}
