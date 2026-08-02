<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Expense;
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

        // Expected cash for the drawer reconciliation: desk payments recorded
        // today in booking meta with a cash method.
        $expectedCash = $this->cashCollectedOn($businessDate);

        $history = NightAudit::with('admin')->orderByDesc('audit_date')->paginate(10);

        return view('admin.night-audit.index', compact('businessDate', 'pendingCheckins', 'pendingCheckouts', 'history', 'expectedCash'));
    }

    protected function cashCollectedOn(string $businessDate): float
    {
        $total = 0.0;
        Booking::whereNotNull('meta')
            ->whereDate('updated_at', '>=', \Carbon\Carbon::parse($businessDate)->subDays(7))
            ->get()
            ->each(function ($booking) use (&$total, $businessDate) {
                foreach ($booking->meta['payments'] ?? [] as $p) {
                    $isCash = str_contains(strtolower($p['method'] ?? ''), 'cash');
                    $sameDay = isset($p['timestamp'])
                        && \Carbon\Carbon::parse($p['timestamp'])->toDateString() === $businessDate;
                    if ($isCash && $sameDay) {
                        $total += (float) ($p['amount'] ?? 0);
                    }
                }
            });

        return round($total, 2);
    }

    public function perform(Request $request)
    {
        $request->validate([
            'cash_counted' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

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

        // 2. Revenue: gateway payments + desk payments recorded today
        $gatewayRevenue = (float) DB::table('payments')
            ->whereDate('created_at', $businessDate)
            ->sum('amount');

        $deskRevenue = 0.0;
        Booking::whereNotNull('meta')
            ->whereDate('updated_at', '>=', \Carbon\Carbon::parse($businessDate)->subDays(7))
            ->get()
            ->each(function ($booking) use (&$deskRevenue, $businessDate) {
                foreach ($booking->meta['payments'] ?? [] as $p) {
                    if (isset($p['timestamp']) && \Carbon\Carbon::parse($p['timestamp'])->toDateString() === $businessDate) {
                        $deskRevenue += (float) ($p['amount'] ?? 0);
                    }
                }
            });

        $revenue = $gatewayRevenue + $deskRevenue;

        // 3. Occupancy Rate + ADR
        $totalRooms = Room::count();
        $occupiedRooms = Booking::where('check_in', '<=', $businessDate)
            ->where('check_out', '>', $businessDate)
            ->where('status', 'checked_in')
            ->count();
        $occupancyRate = $totalRooms > 0 ? ($occupiedRooms / $totalRooms) * 100 : 0;

        $roomRentToday = (float) DB::table('folio_items')
            ->where('type', 'room_rent')
            ->whereDate('reference_date', $businessDate)
            ->sum('amount');
        $adr = $occupiedRooms > 0 && $roomRentToday > 0 ? $roomRentToday / $occupiedRooms : 0;

        $checkedOutCount = Booking::whereDate('checked_out_at', $businessDate)->count();

        // 4. Cash drawer reconciliation
        $expectedCash = $this->cashCollectedOn($businessDate);
        $countedCash = $request->filled('cash_counted') ? (float) $request->cash_counted : null;
        $variance = $countedCash !== null ? round($countedCash - $expectedCash, 2) : null;

        $expensesToday = (float) Expense::whereDate('expense_date', $businessDate)->sum('amount');

        $flash = sprintf(
            "%s Daily Flash — %s\nOccupancy: %d%% (%d/%d rooms)\nADR: ₹%s\nCollections: ₹%s (cash ₹%s)\nExpenses: ₹%s\nCheck-outs: %d | No-shows: %d%s",
            site('hotel_name', 'Hotel'),
            \Carbon\Carbon::parse($businessDate)->format('d M Y'),
            round($occupancyRate),
            $occupiedRooms,
            $totalRooms,
            number_format($adr),
            number_format($revenue),
            number_format($expectedCash),
            number_format($expensesToday),
            $checkedOutCount,
            $noShows->count(),
            $variance !== null && abs($variance) > 0.01 ? "\n⚠ Cash variance: ₹" . number_format($variance, 2) : ''
        );

        // 5. Create Audit Record (locks the day)
        NightAudit::create([
            'hotel_id' => $hotel->id,
            'audit_date' => $businessDate,
            'performed_by_admin_id' => $admin->id,
            'revenue_total' => $revenue,
            'occupancy_rate' => $occupancyRate,
            'no_shows_count' => $noShows->count(),
            'checked_out_count' => $checkedOutCount,
            'cash_expected' => $expectedCash,
            'cash_counted' => $countedCash,
            'cash_variance' => $variance,
            'status' => 'completed',
            'notes' => trim(($request->input('notes') ? $request->input('notes') . "\n\n" : '') . $flash),
        ]);

        // 6. Owner flash report on WhatsApp
        $ownerPhone = trim((string) site('owner_whatsapp', ''));
        if ($ownerPhone !== '') {
            \App\Jobs\SendWhatsAppMessageJob::dispatch($ownerPhone, 'daily_flash', [
                site('hotel_name', 'Hotel'),
                \Carbon\Carbon::parse($businessDate)->format('d M Y'),
                round($occupancyRate) . '%',
                '₹' . number_format($adr),
                '₹' . number_format($revenue),
            ]);
        }

        // 7. Advance Business Date — this closes the day (see day_is_closed()).
        $nextDate = \Carbon\Carbon::parse($businessDate)->addDay()->format('Y-m-d');
        DB::table('site_settings')->updateOrInsert(
            ['key' => 'business_date'],
            ['value' => $nextDate, 'updated_at' => now()]
        );

        $msg = "Night Audit for {$businessDate} completed. Business date is now {$nextDate}.";
        if ($variance !== null && abs($variance) > 0.01) {
            $msg .= ' Cash variance of ₹' . number_format($variance, 2) . ' recorded.';
        }

        return redirect()->route('admin.night-audit.index')->with('success', $msg);
    }
}
