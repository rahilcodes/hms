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

        // 2. Real insight cards from the 30-day pace
        $insights = [];
        $peak = max($occupancyData);
        $peakIdx = array_search($peak, $occupancyData);
        if ($peak >= 70) {
            $insights[] = [
                'type' => 'opportunity',
                'title' => 'High Demand: ' . $dates[$peakIdx],
                'message' => "Occupancy is pacing at {$peak}% on {$dates[$peakIdx]}. Raise rates for that window.",
                'action' => 'Review Rates',
                'icon' => 'trending-up',
            ];
        }
        $low = min($occupancyData);
        $lowIdx = array_search($low, $occupancyData);
        if ($low <= 30) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Low Pace: ' . $dates[$lowIdx],
                'message' => "Only {$low}% booked for {$dates[$lowIdx]}. Consider a last-minute offer or agent push.",
                'action' => 'Create Offer',
                'icon' => 'trending-down',
            ];
        }

        // 3. Festival calendar (next 120 days) with one-click surge pricing
        $festivals = \App\Models\Festival::upcoming(120)->get()->map(function ($f) use ($totalRooms) {
            $start = $f->date;
            $end = $f->end_date ?? $f->date;
            $occupied = Booking::whereIn('status', ['confirmed', 'checked_in'])
                ->where('check_in', '<=', $end->toDateString())
                ->where('check_out', '>', $start->toDateString())
                ->count();
            $f->occupancy = min(100, round(($occupied / max(1, $totalRooms)) * 100));
            $f->rule_exists = \App\Models\PricingRule::where('type', 'season')
                ->whereDate('start_date', '<=', $start)
                ->whereDate('end_date', '>=', $end)
                ->exists();
            return $f;
        });

        return view('admin.yield.index', compact('dates', 'occupancyData', 'revenueData', 'insights', 'festivals'));
    }

    /**
     * One-click festival surge: creates a season pricing rule for EVERY room
     * type at base price + uplift %, covering the festival window.
     */
    public function applyFestival(Request $request, \App\Models\Festival $festival)
    {
        $uplift = (int) $request->input('uplift', $festival->suggested_uplift_percent);
        $uplift = max(0, min(200, $uplift));

        $start = $festival->date->toDateString();
        $end = ($festival->end_date ?? $festival->date)->toDateString();

        $count = 0;
        foreach (RoomType::all() as $roomType) {
            \App\Models\PricingRule::updateOrCreate(
                [
                    'room_type_id' => $roomType->id,
                    'type' => 'season',
                    'start_date' => $start,
                    'end_date' => $end,
                ],
                ['price' => round($roomType->base_price * (1 + $uplift / 100), 2)]
            );
            $count++;
        }

        \App\Models\ActivityLog::log(
            'Festival Pricing Applied',
            "{$festival->name}: +{$uplift}% season rates created for {$count} room types ({$start} → {$end})"
        );

        return back()->with('success', "{$festival->name}: +{$uplift}% surge pricing applied to {$count} room types.");
    }
}
