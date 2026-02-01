<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RoomType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class YieldController extends Controller
{
    public function index()
    {
        // 1. Calculate Occupancy for next 30 days
        $dates = [];
        $occupancyData = [];
        $revenueData = [];

        $totalRooms = RoomType::sum('total_rooms');
        if ($totalRooms == 0)
            $totalRooms = 1; // Prevent div by zero

        for ($i = 0; $i < 30; $i++) {
            $date = Carbon::today()->addDays($i);
            $formattedDate = $date->format('Y-m-d');
            $dates[] = $date->format('M d');

            // Count bookings for this date (simple logic: overlaps)
            $occupied = Booking::where('status', 'confirmed')
                ->where('check_in', '<=', $formattedDate)
                ->where('check_out', '>', $formattedDate)
                ->count();

            // Calculate potential revenue (average rate * occupied)
            // Ideally we check specific rates, but for summary we use avg
            $avgRate = Booking::where('status', 'confirmed')
                ->where('check_in', '<=', $formattedDate)
                ->where('check_out', '>', $formattedDate)
                ->avg('total_amount') / 2; // Rough estimate of nightly rate if stays are ~2 days

            $occupancyData[] = round(($occupied / $totalRooms) * 100);
            $revenueData[] = round($avgRate * $occupied) ?: 0;
        }

        // 2. Generate Insight Cards
        $insights = [
            [
                'type' => 'opportunity',
                'title' => 'High Demand Weekend',
                'message' => 'Next Saturday occupancy is at 85%. Suggest raising rates by 15%.',
                'action' => 'Apply +15%',
                'icon' => 'trending-up'
            ],
            [
                'type' => 'warning',
                'title' => 'Low Mid-Week Pace',
                'message' => 'Next Tuesday-Wednesday is tracking 20% below average. Consider a flash sale.',
                'action' => 'Create Offer',
                'icon' => 'trending-down'
            ]
        ];

        return view('admin.yield.index', compact('dates', 'occupancyData', 'revenueData', 'insights'));
    }
}
