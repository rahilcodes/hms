<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use App\Models\RoomType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PricingRuleController extends Controller
{
    public function index()
    {
        $rules = PricingRule::with('roomType')
            ->orderBy('room_type_id')
            ->orderBy('type')
            ->get();

        return view('admin.pricing-rules.index', compact('rules'));
    }

    public function create()
    {
        $roomTypes = RoomType::orderBy('name')->get();

        return view('admin.pricing-rules.create', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'type' => 'required|in:weekend,season',
            'price' => 'required|numeric|min:0',
            'start_date' => 'nullable|date|required_if:type,season',
            'end_date' => 'nullable|date|after_or_equal:start_date|required_if:type,season',
        ]);

        if ($request->type === 'weekend') {
            PricingRule::where('room_type_id', $request->room_type_id)
                ->where('type', 'weekend')
                ->delete();
        }

        $rule = PricingRule::create($request->only([
            'room_type_id',
            'type',
            'price',
            'start_date',
            'end_date',
        ]));

        ActivityLog::log('Pricing Rule Added', "New pricing rule ({$request->type}) set for {$rule->roomType->name}", $rule);

        return redirect()
            ->route('admin.pricing-rules.index')
            ->with('success', 'Pricing rule saved');
    }

    public function destroy(PricingRule $pricingRule)
    {
        $pricingRule->delete();

        ActivityLog::log('Pricing Rule Deleted', "Pricing rule #{$pricingRule->id} was removed", $pricingRule);

        return back()->with('success', 'Pricing rule removed');
    }
}
