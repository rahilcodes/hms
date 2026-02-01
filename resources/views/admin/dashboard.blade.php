@extends('layouts.admin')

@section('header_title', 'Dashboard Hub')

@section('content')
    @php $hotel = auth('admin')->user()->hotel; @endphp

    {{-- DASHBOARD METRICS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">

        {{-- 1. ARRIVALS --}}
        @if($hotel->hasFeature('front-desk'))
            <a href="{{ route('admin.front-desk') }}"
                class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-blue-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Front Desk</span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-500 mb-1">Check-ins Today</h4>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['check_ins'] }}</h3>
                </div>
            </a>

            {{-- 2. DEPARTURES --}}
            <a href="{{ route('admin.front-desk') }}"
                class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-orange-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Front Desk</span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-500 mb-1">Check-outs Today</h4>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['check_outs'] }}</h3>
                </div>
            </a>

        @endif
        {{-- 3. NEW BOOKINGS --}}
        @if($hotel->hasFeature('front-desk'))
            <a href="{{ route('admin.bookings.index') }}"
                class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-purple-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reservations</span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-500 mb-1">New Bookings Today</h4>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['new_bookings'] }}</h3>
                        @if($stats['pending_bookings'] > 0)
                            <span
                                class="text-xs font-bold text-rose-500 bg-rose-50 px-2 py-1 rounded-lg">{{ $stats['pending_bookings'] }}
                                Pending</span>
                        @endif
                    </div>
                </div>
            </a>

        @endif
        {{-- 4. TODAY REVENUE --}}
        @if($hotel->hasFeature('financials'))
            <a href="{{ route('admin.analytics') }}"
                class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-emerald-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Financials</span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-500 mb-1">Revenue Collected Today</h4>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter">
                        ₹{{ number_format($stats['revenue_today']) }}</h3>
                </div>
            </a>

            {{-- 5. TODAY EXPENSES --}}
            <a href="{{ route('admin.analytics') }}"
                class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-rose-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 group-hover:bg-rose-600 group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Financials</span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-500 mb-1">Expenses Incurred Today</h4>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter">
                        ₹{{ number_format($stats['expenses_today']) }}</h3>
                </div>
            </a>

        @endif
        {{-- 6. HOUSEKEEPING --}}
        @if($hotel->hasFeature('housekeeping'))
            <a href="{{ route('admin.housekeeping.index') }}"
                class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8 hover:shadow-xl hover:border-cyan-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-12 h-12 bg-cyan-50 rounded-2xl flex items-center justify-center text-cyan-600 group-hover:bg-cyan-600 group-hover:text-white transition-all duration-500">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                        </div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Housekeeping</span>
                    </div>
                    <h4 class="text-sm font-bold text-slate-500 mb-1">Rooms to Clean</h4>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-4xl font-black text-slate-900 tracking-tighter">{{ $stats['rooms_dirty'] }}</h3>
                        <span class="text-xs font-bold text-cyan-600 bg-cyan-50 px-2 py-1 rounded-lg">Action Required</span>
                    </div>
                </div>
            </a>
        @endif

    </div>

    {{-- RECENT ACTIVITY & ALERTS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">

        {{-- TABLE --}}
        {{-- LIVE PULSE FEED --}}
        <div class="lg:col-span-2 h-[500px]">
            <x-admin.live-pulse />
        </div>

        {{-- SMART ALERTS --}}
        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm p-10">
            <h3 class="text-lg font-black text-slate-900 tracking-tight mb-2">Operational Alerts</h3>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-10">Real-time Awareness</p>

            <div class="space-y-8">
                @forelse($alerts as $alert)
                    <div class="flex gap-5 group">
                        <div
                            class="w-1.5 h-12 rounded-full {{ $alert['level'] === 'warning' ? 'bg-amber-400 shadow-lg shadow-amber-200' : 'bg-blue-400 shadow-lg shadow-blue-200' }} group-hover:scale-y-110 transition-transform">
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-800 leading-tight mb-1.5">{{ $alert['message'] }}</p>
                            <p class="text-xs text-slate-400 font-bold leading-relaxed">{{ $alert['suggestion'] }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 opacity-20">
                        <p class="text-xs font-bold uppercase tracking-widest italic leading-relaxed">No critical
                            messages<br>Environment stable</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

@endsection