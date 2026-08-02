<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TravelAgent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TravelAgentController extends Controller
{
    public function index(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->startOfMonth()->toDateString()));
        $to = Carbon::parse($request->input('to', now()->endOfMonth()->toDateString()));

        $agents = TravelAgent::withCount([
            'bookings as period_bookings' => fn ($q) => $q
                ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                ->whereNotIn('status', ['cancelled', 'expired']),
        ])
            ->withSum([
                'bookings as period_revenue' => fn ($q) => $q
                    ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                    ->whereNotIn('status', ['cancelled', 'expired']),
            ], 'total_amount')
            ->withSum([
                'bookings as period_commission' => fn ($q) => $q
                    ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                    ->whereNotIn('status', ['cancelled', 'expired']),
            ], 'agent_commission')
            ->orderBy('name')
            ->get();

        $totals = [
            'bookings' => $agents->sum('period_bookings'),
            'revenue' => $agents->sum('period_revenue'),
            'commission' => $agents->sum('period_commission'),
        ];

        return view('admin.agents.index', compact('agents', 'from', 'to', 'totals'));
    }

    public function create()
    {
        return view('admin.agents.create', ['agent' => null]);
    }

    public function edit(TravelAgent $agent)
    {
        return view('admin.agents.create', ['agent' => $agent]);
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:120',
            'agency_name' => 'nullable|string|max:150',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email',
            'gst_number' => 'nullable|string|max:20',
            'commission_percent' => 'required|numeric|min:0|max:50',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['hotel_id'] = auth('admin')->user()->hotel_id ?? 1;
        $data['is_active'] = $request->boolean('is_active', true);
        TravelAgent::create($data);

        return redirect()->route('admin.agents.index')->with('success', 'Travel agent added.');
    }

    public function update(Request $request, TravelAgent $agent)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        $agent->update($data);

        return redirect()->route('admin.agents.index')->with('success', 'Travel agent updated.');
    }
}
