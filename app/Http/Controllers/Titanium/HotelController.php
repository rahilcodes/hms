<?php

namespace App\Http\Controllers\Titanium;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hotel;
use App\Models\Admin;
use App\Models\Subscription;
use App\Models\Feature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelController extends Controller
{
    public function create()
    {
        return view('titanium.hotels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Hotel Details
            'hotel_name' => 'required|string|max:255',
            'hotel_slug' => 'required|alpha_dash|unique:hotels,slug',
            'hotel_phone' => 'nullable|string|max:20',

            // Admin Details
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:admins,email',
            'admin_password' => 'required|string|min:8',

            // Plan
            'plan' => 'required|in:basic,pro,enterprise',
        ]);

        try {
            DB::transaction(function () use ($request) {
                // 1. Create Hotel
                $hotel = Hotel::create([
                    'name' => $request->hotel_name,
                    'slug' => $request->hotel_slug,
                    'address' => 'Not Provided', // Can update later
                    'city' => 'Not Provided',
                    'contact_email' => $request->admin_email,
                    'contact_phone' => $request->hotel_phone,
                    'website' => null,
                ]);

                // 2. Create Super Admin
                Admin::create([
                    'hotel_id' => $hotel->id,
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'password' => Hash::make($request->admin_password),
                    'role' => 'super_admin',
                ]);

                // 3. Create Subscription
                $planDetails = $this->getPlanDetails($request->plan);
                Subscription::create([
                    'hotel_id' => $hotel->id,
                    'plan_name' => ucfirst($request->plan) . ' Plan',
                    'price' => $planDetails['price'],
                    'billing_cycle' => 'monthly',
                    'starts_at' => now(),
                    'next_billing_date' => now()->addMonth(),
                    'status' => 'active',
                ]);

                // 4. Attach Default Features
                $this->attachDefaultFeatures($hotel, $request->plan);
            });

            return redirect()->route('titanium.dashboard')->with('success', 'Hotel ' . $request->hotel_name . ' onboarded successfully.');

        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Onboarding failed: ' . $e->getMessage()]);
        }
    }

    public function show(Hotel $hotel)
    {
        $hotel->load(['subscription', 'features']);
        $allFeatures = Feature::all();
        $templates = \App\Models\NotificationTemplate::all();

        return view('titanium.hotels.show', compact('hotel', 'allFeatures', 'templates'));
    }

    public function toggleFeature(Request $request, Hotel $hotel)
    {
        $request->validate([
            'feature_id' => 'required|exists:features,id',
            'is_enabled' => 'required|boolean'
        ]);

        $exists = $hotel->features()->where('feature_id', $request->feature_id)->exists();

        if ($exists) {
            $hotel->features()->updateExistingPivot($request->feature_id, [
                'is_enabled' => $request->is_enabled
            ]);
        } else {
            $hotel->features()->attach($request->feature_id, [
                'is_enabled' => $request->is_enabled
            ]);
        }

        return back()->with('success', 'Feature status updated.');
    }

    public function impersonate(Hotel $hotel)
    {
        $admin = $hotel->admins()->where('role', 'super_admin')->first();

        if (!$admin) {
            return back()->withErrors(['message' => 'No super admin found for this hotel.']);
        }

        Auth::guard('admin')->loginUsingId($admin->id);

        return redirect()->route('admin.dashboard')->with('success', 'Shadow login: You are now impersonating ' . $hotel->name);
    }

    // Helpers
    private function getPlanDetails($plan)
    {
        $prices = [
            'basic' => 49.00,
            'pro' => 149.00,
            'enterprise' => 499.00
        ];
        return ['price' => $prices[$plan]];
    }

    private function attachDefaultFeatures(Hotel $hotel, $plan)
    {
        $features = Feature::all();

        // Define what each plan gets by default
        // Basic: Housekeeping
        // Pro: Housekeeping, Financials
        // Enterprise: All
        $enabledSlugs = match ($plan) {
            'basic' => ['housekeeping'],
            'pro' => ['housekeeping', 'financials', 'crm'],
            'enterprise' => $features->pluck('slug')->toArray(),
        };

        foreach ($features as $feature) {
            // Check if feature allows being enabled for this plan
            $shouldEnable = in_array($feature->slug, $enabledSlugs);

            if ($hotel->features()->where('feature_id', $feature->id)->exists()) {
                // If it exists, we only ENABLE it if it's in the plan.
                // We do NOT disable it if it was manually enabled?
                // Actually, syncing plan usually means resetting to plan defaults OR just ensuring plan features are enabled.
                // For now, let's enforce plan defaults to ensure "Sync".
                $hotel->features()->updateExistingPivot($feature->id, ['is_enabled' => $shouldEnable]);
            } else {
                $hotel->features()->attach($feature->id, ['is_enabled' => $shouldEnable]);
            }
        }
    }

    public function updateSubscription(Request $request, Hotel $hotel)
    {
        $request->validate([
            'plan' => 'required|in:basic,pro,enterprise'
        ]);

        $plan = $request->plan;
        $prices = ['basic' => 49.00, 'pro' => 149.00, 'enterprise' => 499.00];

        // Update Subscription
        if ($hotel->subscription) {
            $hotel->subscription()->update([
                'plan_name' => ucfirst($plan) . ' Plan',
                'price' => $prices[$plan],
                'status' => 'active'
            ]);
        } else {
            // Create if missing
            Subscription::create([
                'hotel_id' => $hotel->id,
                'plan_name' => ucfirst($plan) . ' Plan',
                'price' => $prices[$plan],
                'billing_cycle' => 'monthly',
                'starts_at' => now(),
                'next_billing_date' => now()->addMonth(),
                'status' => 'active',
            ]);
        }

        // Sync Features
        $this->attachDefaultFeatures($hotel, $plan);

        return back()->with('success', 'Subscription plan updated to ' . ucfirst($plan));
    }
}
