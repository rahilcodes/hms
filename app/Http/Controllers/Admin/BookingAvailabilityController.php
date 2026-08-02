<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Services\AvailabilityService;

class BookingAvailabilityController extends Controller
{
    public function check(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $availabilities = [];
        $roomTypes = RoomType::all();
        $availabilityService = app(AvailabilityService::class);

        foreach ($roomTypes as $rt) {
            $availabilities[$rt->id] = $availabilityService->maxAvailableRooms(
                $rt,
                $request->check_in,
                $request->check_out
            );
        }

        return response()->json($availabilities);
    }
}
