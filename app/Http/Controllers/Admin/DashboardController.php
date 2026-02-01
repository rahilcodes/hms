<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\RoomType;
use App\Models\ActivityLog;
use App\Models\Payment;
use App\Models\Room;
use App\Models\LaundryBatch;
use App\Models\MaintenanceLog;
use Carbon\Carbon;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Expneses Calculation
        $laundryCost = LaundryBatch::whereDate('created_at', $today)->sum('total_cost');
        $maintenanceCost = MaintenanceLog::whereDate('created_at', $today)->sum('cost');

        // 1. HEADLINE STATS
        $stats = [
            'check_ins' => Booking::whereDate('check_in', $today)
                ->where('status', 'confirmed')
                ->count(),

            'check_outs' => Booking::whereDate('check_out', $today)
                ->where('status', 'confirmed')
                ->count(),

            'new_bookings' => Booking::whereDate('created_at', $today)->count(),

            'revenue_today' => Payment::whereDate('created_at', $today)->sum('amount'),

            'expenses_today' => $laundryCost + $maintenanceCost,

            'rooms_dirty' => Room::where('status', 'dirty')->count(),

            'pending_bookings' => Booking::where('status', 'pending')->count(),
        ];

        // 2. RECENT ACTIVITY
        $recentBookings = Booking::with(['roomType', 'guests'])
            ->latest()
            ->take(5)
            ->get();

        $alertService = new \App\Services\SmartAlertService();
        $alerts = $alertService->getOccupancyAlerts();

        return view('admin.dashboard', compact(
            'stats',
            'recentBookings',
            'alerts'
        ));
    }

    public function analytics()
    {
        $today = Carbon::today();

        // 1. CHART DATA (Monthly Trends - Last 6 Months)
        $chartData = [];
        $chartLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->subMonths($i);
            $revenue = Booking::where('status', 'confirmed')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');

            $chartLabels[] = $month->format('M');
            $chartData[] = (int) $revenue;
        }

        // 2. DAILY CHART DATA (Last 7 Days)
        $dailyChartData = [];
        $dailyChartLabels = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $revenue = Booking::where('status', 'confirmed')
                ->whereDate('created_at', $day)
                ->sum('total_amount');

            $dailyChartLabels[] = $day->format('d M');
            $dailyChartData[] = (int) $revenue;
        }

        // 3. REVENUE BY ROOM TYPE
        $roomTypeRevenue = [];
        $roomTypeLabels = [];
        foreach (RoomType::all() as $rt) {
            $rev = Booking::where('room_type_id', $rt->id)
                ->where('status', 'confirmed')
                ->whereMonth('created_at', $today->month)
                ->sum('total_amount');

            if ($rev > 0) {
                $roomTypeLabels[] = $rt->name;
                $roomTypeRevenue[] = (int) $rev;
            }
        }

        return view('admin.analytics', compact(
            'chartData',
            'chartLabels',
            'dailyChartData',
            'dailyChartLabels',
            'roomTypeRevenue',
            'roomTypeLabels'
        ));
    }

    public function frontDesk()
    {
        $today = Carbon::today();

        $todayArrivals = Booking::with(['roomType', 'guests', 'assignedRooms'])
            ->whereDate('check_in', $today)
            ->where('status', 'confirmed')
            ->whereNull('checked_in_at')
            ->get();

        $todayDepartures = Booking::with(['roomType', 'guests', 'assignedRooms'])
            ->whereDate('check_out', $today)
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->get();

        return view('admin.front-desk.index', compact(
            'todayArrivals',
            'todayDepartures'
        ));
    }

    public function pulse()
    {
        $events = [];

        $hotel = auth('admin')->user()->hotel;

        // 1. Activity Logs (Admin Actions)
        $logs = ActivityLog::with('admin')
            ->latest()
            ->take(10)
            ->get();

        foreach ($logs as $log) {
            $events[] = [
                'type' => 'log',
                'title' => $log->action,
                'description' => $log->description . ' (' . ($log->admin->name ?? 'System') . ')',
                'timestamp' => $log->created_at->diffForHumans(),
                'raw_date' => $log->created_at,
                'icon' => 'clipboard-list',
                'color' => 'slate'
            ];
        }

        // 2. New Bookings (Guest Actions) - Feature: front-desk
        if ($hotel->hasFeature('front-desk')) {
            $bookings = Booking::latest()
                ->take(5)
                ->get();

            foreach ($bookings as $booking) {
                $events[] = [
                    'type' => 'booking',
                    'title' => 'New Booking #' . $booking->id,
                    'description' => $booking->guest_name . ' booked ' . ($booking->roomType->name ?? 'Room'),
                    'timestamp' => $booking->created_at->diffForHumans(),
                    'raw_date' => $booking->created_at,
                    'icon' => 'sparkles',
                    'color' => 'blue'
                ];
            }
        }

        // 3. Payments - Feature: financials
        if ($hotel->hasFeature('financials')) {
            $payments = Payment::latest()
                ->take(5)
                ->get();

            foreach ($payments as $payment) {
                $events[] = [
                    'type' => 'payment',
                    'title' => 'Payment Received',
                    'description' => '₹' . number_format($payment->amount) . ' via ' . ucfirst($payment->provider),
                    'timestamp' => $payment->created_at->diffForHumans(),
                    'raw_date' => $payment->created_at,
                    'icon' => 'currency-rupee',
                    'color' => 'emerald'
                ];
            }
        }

        // 4. Sort by Date DESC
        usort($events, function ($a, $b) {
            return $b['raw_date'] <=> $a['raw_date'];
        });

        // 5. Slice top 15
        return response()->json(array_slice($events, 0, 15));
    }
}
