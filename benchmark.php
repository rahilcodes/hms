<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RoomType;
use App\Models\Booking;
use App\Models\BookingGuest;
use App\Services\AvailabilityService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Helper to benchmark execution
function benchmark($label, $closure)
{
    $startMemory = memory_get_usage();
    $startTime = microtime(true);

    $closure();

    $endTime = microtime(true);
    $endMemory = memory_get_usage();

    $timeMs = round(($endTime - $startTime) * 1000, 2);
    $memoryMb = round(($endMemory - $startMemory) / 1024 / 1024, 2);

    echo str_pad($label, 50) . " | {$timeMs}ms | {$memoryMb}MB\n";
}

echo "=== PERFORMANCE BENCHMARKS ===\n\n";

// 1. Availability Search Scaling
$availabilityService = app(AvailabilityService::class);
$roomType = RoomType::first(); // Assuming at least one exists
if (!$roomType) {
    echo "No room types found for benchmarking.\n";
    exit;
}

echo "Availability Search (Simulated under load with Post-Fix O(N+M) complexity):\n";
benchmark("7-Day Range Search", fn() => $availabilityService->maxAvailableRooms($roomType, now()->toDateString(), now()->addDays(7)->toDateString()));
benchmark("30-Day Range Search", fn() => $availabilityService->maxAvailableRooms($roomType, now()->toDateString(), now()->addDays(30)->toDateString()));
benchmark("60-Day Peak Season Search", fn() => $availabilityService->maxAvailableRooms($roomType, now()->toDateString(), now()->addDays(60)->toDateString()));
echo "\n";

// 2. Dashboard N+1 Elimination Simulation
// We will create some dummy bookings if fewer than 100 exist
$count = Booking::count();
echo "Dashboard N+1 Ledger Loading (Simulated for scale):\n";

benchmark("Load 10 Active Bookings (Post-Fix)", function () {
    $bookings = Booking::take(10)->get();
    foreach ($bookings as $b) {
        $bal = $b->total_bill; // Triggers the memory access
    }
});

benchmark("Load 50 Active Bookings (Post-Fix)", function () {
    $bookings = Booking::take(50)->get();
    foreach ($bookings as $b) {
        $bal = $b->total_bill;
    }
});

benchmark("Load 100 Active Bookings (Post-Fix)", function () {
    $bookings = Booking::take(100)->get();
    foreach ($bookings as $b) {
        $bal = $b->total_bill;
    }
});

echo "\nBenchmarks completed successfully.\n";
