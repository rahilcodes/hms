<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class FinancialController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->query('range', 'today');
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        switch ($range) {
            case 'today':
                $start = now()->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'yesterday':
                $start = now()->yesterday()->startOfDay();
                $end = now()->yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $start = now()->subDays(7)->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'this_month':
                $start = now()->startOfMonth()->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'last_month':
                $start = now()->subMonth()->startOfMonth()->startOfDay();
                $end = now()->subMonth()->endOfMonth()->endOfDay();
                break;
            case 'last_3_months':
                $start = now()->subMonths(3)->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'custom':
                $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : now()->startOfDay();
                $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : now()->endOfDay();
                break;
        }

        $bookings = Booking::where(function ($q) use ($start, $end) {
            $q->whereBetween('created_at', [$start, $end])
                ->orWhere('meta->payments', 'LIKE', '%timestamp%'); // Find bookings with payments
        });

        if ($request->query('corporate_only') === '1') {
            $bookings->whereNotNull('company_id');
        }

        $bookings = $bookings->with('company')->get();

        // 1. FETCH EXPENSES
        $laundryBatches = \App\Models\LaundryBatch::with('vendor')
            ->whereBetween('created_at', [$start, $end])
            ->get();

        $maintenanceLogs = \App\Models\MaintenanceLog::with('asset')
            ->whereBetween('created_at', [$start, $end])
            ->where('cost', '>', 0)
            ->get();

        $ledger = [];
        $stats = [
            'total_income' => 0,
            'total_expense' => 0,
            'net_profit' => 0,

            // Income Breakdowns
            'cash' => 0,
            'upi' => 0,
            'card' => 0,
            'bank_transfer' => 0,
            'advance' => 0,
            'corporate_revenue' => 0,
            'private_revenue' => 0,
            'corporate_outstanding' => 0,

            // Expense Breakdowns
            'laundry_cost' => 0,
            'maintenance_cost' => 0,
        ];

        // 2. PROCESS BOOKINGS (INCOME)
        foreach ($bookings as $booking) {
            // Process Advance
            if ($booking->created_at->between($start, $end)) {
                $advance = $booking->meta['advance_paid'] ?? 0;
                if ($advance > 0) {
                    $stats['advance'] += $advance;
                    $stats['total_income'] += $advance;

                    if ($booking->company_id) {
                        $stats['corporate_revenue'] += $advance;
                    } else {
                        $stats['private_revenue'] += $advance;
                    }

                    $ledger[] = [
                        'date' => $booking->created_at,
                        'reference' => '#' . ($booking->id + 1000),
                        'entity' => $booking->guest_name,
                        'category' => 'Advance Payment',
                        'type' => 'income',
                        'method' => 'Online/Initial',
                        'amount' => $advance
                    ];
                }
            }

            // Process Settlements
            $payments = $booking->meta['payments'] ?? [];
            foreach ($payments as $payment) {
                $payDate = Carbon::parse($payment['timestamp']);
                if ($payDate->between($start, $end)) {
                    $amt = (float) $payment['amount'];
                    $method = $payment['method'] ?? 'cash';

                    $stats['total_income'] += $amt;
                    $stats[$method] = ($stats[$method] ?? 0) + $amt;

                    if ($booking->company_id) {
                        $stats['corporate_revenue'] += $amt;
                    } else {
                        $stats['private_revenue'] += $amt;
                    }

                    $ledger[] = [
                        'date' => $payDate,
                        'reference' => '#' . ($booking->id + 1000),
                        'entity' => $booking->guest_name . ($booking->company ? ' (' . $booking->company->name . ')' : ''),
                        'category' => 'Settlement (' . ucfirst($payment['type'] ?? 'payment') . ')',
                        'type' => 'income',
                        'method' => strtoupper($method),
                        'amount' => $amt
                    ];
                }
            }
        }

        // 3. PROCESS LAUNDRY (EXPENSE)
        foreach ($laundryBatches as $batch) {
            $cost = $batch->total_cost;
            $stats['total_expense'] += $cost;
            $stats['laundry_cost'] += $cost;

            $ledger[] = [
                'date' => $batch->created_at,
                'reference' => 'LNDRY-' . $batch->id,
                'entity' => $batch->vendor->name ?? 'Internal Laundry',
                'category' => 'Laundry Expense',
                'type' => 'expense',
                'method' => 'BILL',
                'amount' => $cost
            ];
        }

        // 4. PROCESS MAINTENANCE (EXPENSE)
        foreach ($maintenanceLogs as $log) {
            $cost = $log->cost;
            $stats['total_expense'] += $cost;
            $stats['maintenance_cost'] += $cost;

            $ledger[] = [
                'date' => $log->created_at,
                'reference' => 'MAINT-' . $log->id,
                'entity' => $log->technician_name ?? 'Technician',
                'category' => 'Maintenance: ' . ($log->asset->name ?? 'General'),
                'type' => 'expense',
                'method' => 'BILL',
                'amount' => $cost
            ];
        }

        // Calculate Net Profit
        $stats['net_profit'] = $stats['total_income'] - $stats['total_expense'];

        // Calculate Corporate Outstanding for the range
        $stats['corporate_outstanding'] = Booking::whereNotNull('company_id')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->sum(fn($b) => $b->balance_amount);

        // Sort ledger by date desc
        usort($ledger, fn($a, $b) => $b['date'] <=> $a['date']);

        return view('admin.financials.index', compact('ledger', 'stats', 'start', 'end'));
    }

    public function export(Request $request)
    {
        $range = $request->query('range', 'today');
        $start = now()->startOfDay();
        $end = now()->endOfDay();

        switch ($range) {
            case 'today':
                $start = now()->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'yesterday':
                $start = now()->yesterday()->startOfDay();
                $end = now()->yesterday()->endOfDay();
                break;
            case 'last_7_days':
                $start = now()->subDays(7)->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'this_month':
                $start = now()->startOfMonth()->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'last_month':
                $start = now()->subMonth()->startOfMonth()->startOfDay();
                $end = now()->subMonth()->endOfMonth()->endOfDay();
                break;
            case 'last_3_months':
                $start = now()->subMonths(3)->startOfDay();
                $end = now()->endOfDay();
                break;
            case 'custom':
                $start = $request->query('start_date') ? Carbon::parse($request->query('start_date'))->startOfDay() : now()->subMonth()->startOfDay();
                $end = $request->query('end_date') ? Carbon::parse($request->query('end_date'))->endOfDay() : now()->endOfDay();
                break;
        }

        $bookings = Booking::all(); // For simplicity in export, we filter in loop

        $filename = "financial_report_" . $start->format('Y-m-d') . "_to_" . $end->format('Y-m-d') . ".csv";

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($bookings, $start, $end) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Booking ID', 'Guest Name', 'Company', 'Group ID', 'Transaction Type', 'Payment Mode', 'Amount (INR)']);

            foreach ($bookings as $booking) {
                // Advance
                if ($booking->created_at->between($start, $end)) {
                    $advance = $booking->meta['advance_paid'] ?? 0;
                    if ($advance > 0) {
                        fputcsv($file, [
                            $booking->created_at->format('Y-m-d H:i'),
                            '#' . ($booking->id + 1000),
                            $booking->guest_name,
                            $booking->company->name ?? '',
                            $booking->group_id ?? '',
                            'Advance Payment',
                            'Online',
                            $advance
                        ]);
                    }
                }

                // Settlements
                $payments = $booking->meta['payments'] ?? [];
                foreach ($payments as $payment) {
                    $payDate = Carbon::parse($payment['timestamp']);
                    if ($payDate->between($start, $end)) {
                        fputcsv($file, [
                            $payDate->format('Y-m-d H:i'),
                            '#' . ($booking->id + 1000),
                            $booking->guest_name,
                            $booking->company->name ?? '',
                            $booking->group_id ?? '',
                            'Settlement (' . ($payment['type'] ?? 'payment') . ')',
                            strtoupper($payment['method'] ?? 'CASH'),
                            $payment['amount']
                        ]);
                    }
                }
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
