<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Hotel;

class SubscriptionController extends Controller
{
    /**
     * Display the current subscription.
     */
    public function index()
    {
        $hotel = auth('admin')->user()->hotel;
        $subscription = $hotel->subscription;

        // Mock invoices for display
        $invoices = [
            [
                'id' => 'INV-2024-001',
                'date' => \Carbon\Carbon::now()->subMonth()->format('M d, Y'),
                'amount' => '$' . number_format($subscription?->price ?? 49, 2),
                'status' => 'Paid'
            ],
            [
                'id' => 'INV-2023-012',
                'date' => \Carbon\Carbon::now()->subMonths(2)->format('M d, Y'),
                'amount' => '$' . number_format($subscription?->price ?? 49, 2),
                'status' => 'Paid'
            ]
        ];

        return view('admin.subscription.index', compact('subscription', 'invoices'));
    }
}
