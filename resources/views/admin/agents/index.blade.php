@extends('layouts.admin')

@section('header_title', 'Travel Agents')

@section('content')
    <div class="max-w-6xl mx-auto space-y-5">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-800">Travel Agents (B2B)</h2>
                <p class="text-xs text-slate-500 mt-0.5">Offline agents, net rates and commission tracking.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <input type="date" name="from" value="{{ $from->toDateString() }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <input type="date" name="to" value="{{ $to->toDateString() }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <button class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold">Apply</button>
                </form>
                <a href="{{ route('admin.agents.create') }}" class="px-4 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-black shadow">+ New Agent</a>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-3">
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Bookings</p>
                <p class="text-xl font-black text-slate-800 mt-1">{{ $totals['bookings'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Agent Revenue</p>
                <p class="text-xl font-black text-slate-800 mt-1">₹{{ number_format($totals['revenue'] ?? 0) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Commission Payable</p>
                <p class="text-xl font-black text-amber-600 mt-1">₹{{ number_format($totals['commission'] ?? 0) }}</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($agents as $agent)
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="font-black text-slate-800">
                            {{ $agent->name }}
                            @if(!$agent->is_active)
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full bg-slate-100 text-slate-400 ml-1">Inactive</span>
                            @endif
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $agent->agency_name ?: 'Independent' }} &bull; {{ $agent->phone }}
                            &bull; {{ rtrim(rtrim(number_format($agent->commission_percent, 2), '0'), '.') }}% commission
                        </p>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-5">
                        <div class="text-center">
                            <p class="text-sm font-black text-slate-800">{{ $agent->period_bookings }}</p>
                            <p class="text-[9px] font-bold uppercase text-slate-400">Bookings</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-black text-slate-800">₹{{ number_format($agent->period_revenue ?? 0) }}</p>
                            <p class="text-[9px] font-bold uppercase text-slate-400">Revenue</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm font-black text-amber-600">₹{{ number_format($agent->period_commission ?? 0) }}</p>
                            <p class="text-[9px] font-bold uppercase text-slate-400">Commission</p>
                        </div>
                        <a href="{{ route('admin.agents.edit', $agent) }}" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 text-[11px] font-bold">Edit</a>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-400 text-sm">
                    No travel agents yet. Add the agencies that send you business to start tracking commissions.
                </div>
            @endforelse
        </div>
    </div>
@endsection
