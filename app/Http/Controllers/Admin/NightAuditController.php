<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\NightAudit;
use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NightAuditController extends Controller
{
    public function index()
    {
        $businessDate = site('business_date', now()->format('Y-m-d'));

        $pendingCheckins = Booking::where('check_in', '<=', $businessDate)
            ->where('status', 'confirmed')
            ->whereNull('checked_in_at')
            ->count();

        $pendingCheckouts = Booking::where('check_out', '<=', $businessDate)
            ->where('status', 'checked_in')
            ->whereNull('checked_out_at')
            ->count();

        $history = NightAudit::with('admin')->orderByDesc('audit_date')->paginate(10);

        return view('admin.night-audit.index', compact('businessDate', 'pendingCheckins', 'pendingCheckouts', 'history'));
    }

    public function perform(Request $request)
    {
        $businessDate = site('business_date', now()->format('Y-m-d'));
        $hotel = Hotel::first(); // Assuming single hotel for now
        $admin = Auth::guard('admin')->user();

        // 1. Mark No-Shows
        $noShows = Booking::where('check_in', '<=', $businessDate)
            ->where('status', 'confirmed')
            ->whereNull('checked_in_at')
            ->get();

        foreach ($noShows as $booking) {
            $booking->update(['status' => 'cancelled', 'meta' => array_merge($booking->meta ?? [], ['cancel_reason' => 'System No-Show'])]);
        }

        // 2. Calculate Revenue (Simplified: Sum of payments today)
        $revenue = DB::table('payments')
            ->whereDate('created_at', $businessDate)
            ->sum('amount');

        // 3. Occupancy Rate
        $totalRooms = Room::count();
        $occupiedRooms = Booking::where('check_in', '<=', $businessDate)
            ->where('check_out', '>', $businessDate)
            ->where('status', 'checked_in')
            ->count();
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        // 4. Create Audit Record
        NightAudit::create([
            'hotel_id' => $hotel->id,
            'audit_date' => $businessDate,
            'performed_by_admin_id' => $admin->id,
            'revenue_total' => $revenue,
            'occupancy_rate' => $occupancyRate,
            'no_shows_count' => $noShows->count(),
            'checked_out_count' => 0, // Simplified for now
            'status' => 'completed',
        ]);

        // 5. Advance Business Date
        $nextDate = \Carbon\Carbon::parse($businessDate)->addDay()->format('Y-m-d');
        DB::table('site_settings')->updateOrInsert(
            ['key' => 'business_date'],
            ['value' => $nextDate, 'updated_at' => now()]
        );

        return redirect()->route('admin.night-audit.index')->with('success', "Night Audit for {$businessDate} completed. Business date is now {$nextDate}.");
    }
}
