<?php

namespace App\Http\Controllers\Titanium;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subscription;
use App\Models\Feature;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    public function index()
    {
        $hotel = Auth::guard('titanium')->user()->hotel;

        if (!$hotel) {
            return back()->with('error', 'No linked hotel found.');
        }

        $subscription = $hotel->subscription ?: (object) [
            'status' => 'inactive',
            'plan_name' => 'No Plan',
            'price' => 0,
            'next_billing_date' => now()->addMonth()
        ];

        // Mock invoices (In real app, fetch from Stripe/Paddle)
        $invoices = [
            [
                'id' => 'INV-2024-001',
                'date' => Carbon::now()->subMonth()->format('M d, Y'),
                'amount' => '$' . number_format($subscription?->price ?? 0, 2),
                'status' => 'Paid'
            ],
            [
                'id' => 'INV-2023-012',
                'date' => Carbon::now()->subMonths(2)->format('M d, Y'),
                'amount' => '$' . number_format($subscription?->price ?? 0, 2),
                'status' => 'Paid'
            ]
        ];

        return view('titanium.subscription.index', compact('hotel', 'subscription', 'invoices'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:basic,pro,enterprise'
        ]);

        $hotel = Auth::guard('titanium')->user()->hotel;
        $plan = $request->plan;

        // 1. Update Subscription Record
        $prices = ['basic' => 49, 'pro' => 149, 'enterprise' => 499];

        $hotel->subscription()->updateOrCreate(
            ['hotel_id' => $hotel->id],
            [
                'plan_name' => ucfirst($plan) . ' Plan',
                'price' => $prices[$plan],
                'status' => 'active',
                'next_billing_date' => now()->addMonth()
            ]
        );

        // 2. Sync Features Logic
        $features = Feature::all();
        $enabledSlugs = match ($plan) {
            'basic' => ['housekeeping'],
            'pro' => ['housekeeping', 'financials', 'crm'],
            'enterprise' => $features->pluck('slug')->toArray(),
        };

        foreach ($features as $feature) {
            // Attach or Update Existing pivot
            // We use syncWithoutDetaching logic but allowing disable

            // Check if feature allows being enabled for this plan
            $shouldEnable = in_array($feature->slug, $enabledSlugs);

            if ($hotel->features()->where('feature_id', $feature->id)->exists()) {
                $hotel->features()->updateExistingPivot($feature->id, ['is_enabled' => $shouldEnable]);
            } else {
                $hotel->features()->attach($feature->id, ['is_enabled' => $shouldEnable]);
            }
        }

        return back()->with('success', 'Plan updated successfully to ' . ucfirst($plan));
    }

    public function toggle(Request $request)
    {
        $hotel = Auth::guard('titanium')->user()->hotel;

        if (!$hotel) {
            // Fallback for demo/seed data
            $hotel = \App\Models\Hotel::first();
        }

        if (!$hotel) {
            return back()->with('error', 'No hotel found.');
        }

        $subscription = $hotel->subscription()->latest()->first();

        if ($subscription && $subscription->status === 'active') {
            // Deactivate
            $subscription->update(['status' => 'cancelled']);
            return back()->with('success', 'Plan deactivated successfully.');
        } else {
            // Activate (Default to Basic if no subscription exists)
            $hotel->subscription()->updateOrCreate(
                ['hotel_id' => $hotel->id],
                [
                    'plan_name' => $subscription->plan_name ?? 'Basic Plan',
                    'price' => $subscription->price ?? 49.00,
                    'status' => 'active',
                    'billing_cycle' => 'monthly',
                    'starts_at' => now(),
                    'next_billing_date' => now()->addMonth()
                ]
            );

            // Ensure basic features are enabled if first time
            if (!$subscription) {
                $feature = Feature::where('slug', 'housekeeping')->first();
                if ($feature) {
                    $hotel->features()->syncWithoutDetaching([$feature->id => ['is_enabled' => true]]);
                }
            }

            return back()->with('success', 'Plan activated successfully.');
        }
    }
}
