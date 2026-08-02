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
                $start = $request->query('start_date') ?Carbon::parse($request->query('start_date'))->startOfDay() : now()->startOfDay();
                $end = $request->query('end_date') ?Carbon::parse($request->query('end_date'))->endOfDay() : now()->endOfDay();
                break;
        }

        // 1. FETCH REVENUE (From Folio Ledger)
        $folioItems = \App\Models\FolioItem::whereBetween('posted_at', [$start, $end])
            ->with(['booking.company'])
            ->get();

        // 2. FETCH EXPENSES (From Expense Ledger)
        $expenses = \App\Models\Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $ledger = [];
        $stats = [
            'total_income' => 0,
            'total_expense' => 0,
            'net_profit' => 0,
            'cash' => 0, 'upi' => 0, 'card' => 0, 'bank_transfer' => 0, 'cheque' => 0,
            'corporate_revenue' => 0,
            'private_revenue' => 0,
            'taxes_collected' => 0,
        ];

        // 3. PROCESS REVENUE
        foreach ($folioItems as $item) {
            $isCharge = !in_array($item->type, ['payment', 'discount', 'payment_refund']);

            if ($isCharge) {
                // Earned Revenue
                $stats['total_income'] += $item->amount;
                if ($item->type === 'tax')
                    $stats['taxes_collected'] += $item->amount;

                if ($item->booking && $item->booking->company_id) {
                    $stats['corporate_revenue'] += $item->amount;
                }
                else {
                    $stats['private_revenue'] += $item->amount;
                }
            }
            else if ($item->type === 'payment') {
                // Cash Flow (Payments)
                // We'd ideally track method here. For now, we'll try to find the linked payment.
                $pId = $item->metadata['payment_id'] ?? null;
                $pMethod = 'cash';
                if ($pId) {
                    $payment = \App\Models\Payment::find($pId);
                    $pMethod = $payment ? strtolower($payment->provider) : 'cash';
                }

                $mKey = in_array($pMethod, ['cash', 'upi', 'card', 'bank_transfer', 'cheque']) ? $pMethod : 'cash';
                $stats[$mKey] += $item->amount;
            }

            $ledger[] = [
                'date' => $item->posted_at,
                'reference' => $item->booking_id ? '#' . ($item->booking_id + 1000) : 'N/A',
                'entity' => $item->booking ? $item->booking->guest_name : 'System',
                'category' => ucfirst(str_replace('_', ' ', $item->type)),
                'type' => $isCharge ? 'income' : 'payment',
                'method' => $isCharge ? 'ACCRUAL' : 'CASH',
                'amount' => $item->amount,
                'description' => $item->description
            ];
        }

        // 4. PROCESS EXPENSES
        foreach ($expenses as $expense) {
            $stats['total_expense'] += $expense->amount;

            $ledger[] = [
                'date' => $expense->expense_date,
                'reference' => $expense->reference_number ?? 'EXP-' . $expense->id,
                'entity' => $expense->category,
                'category' => 'Operational Expense',
                'type' => 'expense',
                'method' => strtoupper($expense->payment_method),
                'amount' => $expense->amount,
                'description' => $expense->description
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
                $start = $request->query('start_date') ?Carbon::parse($request->query('start_date'))->startOfDay() : now()->subMonth()->startOfDay();
                $end = $request->query('end_date') ?Carbon::parse($request->query('end_date'))->endOfDay() : now()->endOfDay();
                break;
        }

        // 1. Fetch Data
        $folioItems = \App\Models\FolioItem::whereBetween('posted_at', [$start, $end])
            ->with(['booking.company'])
            ->get();

        $expenses = \App\Models\Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $filename = "hms_ledger_export_" . $start->format('Y-m-d') . "_to_" . $end->format('Y-m-d') . ".csv";

        $headers = array(
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        );

        $callback = function () use ($folioItems, $expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Reference', 'Entity', 'Category', 'Type', 'Method', 'Amount (INR)', 'Description']);

            // Process Revenue/Payments
            foreach ($folioItems as $item) {
                $isCharge = !in_array($item->type, ['payment', 'discount', 'payment_refund']);
                fputcsv($file, [
                    $item->posted_at->format('Y-m-d H:i'),
                    $item->booking_id ? '#' . ($item->booking_id + 1000) : 'N/A',
                    $item->booking ? $item->booking->guest_name : 'System',
                    ucfirst(str_replace('_', ' ', $item->type)),
                    $isCharge ? 'INCOME' : 'PAYMENT',
                    $isCharge ? 'ACCRUAL' : 'CASH',
                    $item->amount,
                    $item->description
                ]);
            }

            // Process Expenses
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->expense_date->format('Y-m-d'),
                    $expense->reference_number ?? 'EXP-' . $expense->id,
                    $expense->category,
                    'Operational Expense',
                    'EXPENSE',
                    strtoupper($expense->payment_method),
                    $expense->amount,
                    $expense->description
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }
}
