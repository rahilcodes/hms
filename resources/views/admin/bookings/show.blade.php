@extends('layouts.admin')

@section('header_title')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.bookings.index') }}" class="group p-2 bg-white border border-slate-200 hover:border-blue-300 hover:bg-blue-50/50 rounded-lg transition-all shadow-sm">
            <svg class="w-4 h-4 text-slate-500 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
        </a>
        <div>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Booking #{{ $booking->id + 1000 }} <span class="bg-indigo-600 text-white px-1.5 py-0.5 rounded text-[10px]">v4.0</span></span>
        </div>
    </div>
@endsection

@section('content')

    {{-- ZONE A: HEADER (High Level Context) --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 w-64 h-full bg-gradient-to-l from-blue-50/50 to-transparent pointer-events-none"></div>

        <div class="flex items-center gap-5 relative z-10">
            <div class="w-14 h-14 bg-gradient-to-br from-slate-800 to-slate-900 text-white rounded-xl flex items-center justify-center font-black text-2xl shadow-lg shadow-slate-200">
                {{ substr($booking->guest_name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-black text-slate-900 leading-none mb-1.5 flex items-center gap-3">
                    {{ $booking->guest_name }}
                    @if($booking->isCorporate())
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-indigo-50 text-indigo-600 border border-indigo-100 tracking-wide">Corporate</span>
                    @endif
                </h1>
                <div class="flex items-center gap-3 text-sm font-medium text-slate-500">
                     <span class="flex items-center gap-1.5 text-slate-700 font-bold">
                        <span class="w-2 h-2 rounded-full {{ match($booking->operational_status) { 'in_house' => 'bg-emerald-500 animate-pulse', 'checked_out' => 'bg-slate-400', 'cancelled' => 'bg-red-400', default => 'bg-amber-400' } }}"></span>
                        {{ str_replace('_', ' ', $booking->operational_status) }}
                     </span>
                     <span class="text-slate-300">|</span>
                     <span class="font-mono text-slate-600">{{ $booking->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 relative z-10 w-full md:w-auto">
            {{-- Quick Actions Toolbar --}}
             @if($booking->status === 'confirmed' && !$booking->checked_in_at)
                <button onclick="document.getElementById('checkInModal').classList.remove('hidden')" 
                    class="px-6 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    Check In Guest
                </button>
            @endif
            
            @if($booking->checked_in_at && !$booking->checked_out_at)
                <button onclick="document.getElementById('addServiceModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-white border border-slate-200 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-50 hover:border-blue-300 hover:text-blue-600 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Service
                </button>
                
                @if($booking->balance_amount > 0)
                    {{-- If balance remains, prompt to settle bill first --}}
                    <button onclick="toggleModal('settleBillModal', true)" 
                        type="button"
                        class="px-4 py-2 bg-rose-50 border border-rose-100 text-rose-600 text-sm font-bold rounded-xl hover:bg-rose-100 transition shadow-sm flex items-center gap-2 relative group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Check Out
                        <span class="absolute -top-1 -right-1 flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                        </span>
                    </button>
                @else
                    {{-- Balance is zero, proceed with checkout --}}
                    <form action="{{ route('admin.bookings.checkOut', $booking) }}" method="POST" onsubmit="return confirm('Confirm Checkout? This will release rooms.')">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-rose-50 border border-rose-100 text-rose-600 text-sm font-bold rounded-xl hover:bg-rose-100 transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Check Out
                        </button>
                    </form>
                @endif
            @endif
            <a href="{{ route('admin.bookings.invoice', $booking) }}" target="_blank" class="p-2.5 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition border border-transparent hover:border-slate-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            </a>
        </div>
    </div>

    {{-- ZONE B: STATS GRID (Horizontal) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {{-- Stat 1: Room --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Room(s)</p>
                @if($booking->assignedRooms->count() > 3)
                    <p class="text-lg font-black text-slate-900 cursor-help" title="{{ $booking->assignedRooms->pluck('room_number')->implode(', ') }}">
                        {{ $booking->assignedRooms->count() }} <span class="text-xs font-bold text-slate-400">Rooms</span>
                    </p>
                @elseif($booking->assignedRooms->count() > 0)
                    <p class="text-lg font-black text-slate-900">{{ $booking->assignedRooms->pluck('room_number')->implode(', ') }}</p>
                @else
                    <p class="text-lg font-bold text-amber-500">Unassigned</p>
                @endif
            </div>
        </div>

        {{-- Stat 2: Stay Duration --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Duration</p>
                <p class="text-lg font-black text-slate-900">{{ $booking->nights }} <span class="text-xs font-bold text-slate-400">Night(s)</span></p>
            </div>
        </div>

        {{-- Stat 3: Total Bill --}}
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Bill</p>
                <p class="text-lg font-black text-slate-900">₹{{ number_format($booking->total_bill) }}</p>
            </div>
        </div>

        {{-- Stat 4: Balance (Highlighted) --}}
        <div class="bg-slate-900 p-4 rounded-xl shadow-lg border border-slate-800 flex items-center gap-4 group">
            <div class="w-10 h-10 rounded-lg bg-slate-800 text-white flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Balance Due</p>
                <p class="text-lg font-black text-white">₹{{ number_format($booking->balance_amount) }}</p>
            </div>
        </div>
    </div>

    {{-- ZONE C: WORKSPACE (70/30 Grid) --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- LEFT COLUMN (70%) --}}
        <div class="lg:col-span-8 space-y-8">
            
            {{-- Guest Details Card --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Guest Information
                    </h3>
                    <button class="text-xs font-bold text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">Edit Details</button>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($booking->guests as $guest)
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-slate-100 border-2 border-white shadow-sm flex items-center justify-center text-lg font-bold text-slate-500">
                                {{ substr($guest->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $guest->name }}</h4>
                                <div class="space-y-1 mt-1">
                                    <p class="text-xs text-slate-500 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        {{ $guest->phone }}
                                    </p>
                                    <p class="text-xs text-slate-500 flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        {{ $guest->email ?? 'No Email Provided' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
            
            {{-- Pre-booked Services Section --}}
            @if(!empty($booking->services_json))
                <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-blue-50/30">
                        <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                             <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                            Pre-booked Services & Add-ons
                        </h3>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-[10px] font-black uppercase">Stored in Ledger</span>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($booking->services_json as $service)
                                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-slate-100 shadow-sm text-blue-600">
                                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        </div>
                                        <div>
                                             <h4 class="text-sm font-bold text-slate-900">{{ $service['name'] ?? 'Add-on' }}</h4>
                                             <p class="text-[10px] font-medium text-slate-500 uppercase tracking-wider">
                                                 {{ $service['qty'] ?? 1 }}x @ ₹{{ number_format($service['price'] ?? 0) }} 
                                                 @if(($service['price_unit'] ?? 'fixed') == 'per_night') / night @endif
                                             </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                         @php
                                            $total = ($service['price'] ?? 0) * ($service['qty'] ?? 1);
                                            if(($service['price_unit'] ?? 'fixed') == 'per_night') $total *= $booking->nights;
                                         @endphp
                                         <span class="text-sm font-black text-slate-900">₹{{ number_format($total) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            {{-- Timeline --}}
            <section class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col min-h-[500px]">
                 <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Stay Timeline
                    </h3>
                    <span class="px-2 py-0.5 bg-slate-100 text-slate-500 rounded text-xs font-bold">{{ $booking->activityLogs()->count() }} Events</span>
                </div>
                <div class="p-8 bg-slate-50/30 flex-1">
                    <div class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">
                         @forelse($booking->activityLogs()->latest()->get() as $log)
                            <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                {{-- Icon --}}
                                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-100 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 group-hover:scale-110 transition-transform">
                                    <svg class="fill-slate-500 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                                </div>
                                {{-- Card --}}
                                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-4 rounded-xl border border-slate-200 bg-white shadow-sm group-hover:border-blue-200 transition-colors">
                                    <div class="flex items-center justify-between space-x-2 mb-1">
                                        <div class="font-bold text-slate-900 text-sm">{{ $log->action }}</div>
                                        <time class="font-mono text-xs text-slate-500">{{ $log->created_at->format('M d, H:i') }}</time>
                                    </div>
                                    <div class="text-slate-600 text-xs leading-relaxed">
                                        {{ $log->description }}
                                    </div>
                                     @if($log->admin)
                                        <div class="mt-2 flex items-center gap-1.5 pt-2 border-t border-slate-50">
                                            <div class="w-4 h-4 rounded-full bg-slate-200 text-[9px] flex items-center justify-center font-bold text-slate-500">{{ substr($log->admin->name, 0, 1) }}</div>
                                            <span class="text-[10px] text-slate-400">{{ $log->admin->name }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 text-slate-400 text-sm italic">No activity recorded yet.</div>
                        @endforelse
                    </div>
                </div>
            </section>

        </div>

        {{-- RIGHT COLUMN (30%) - THE FOLIO WIDGET --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- THE FOLIO SUPER-CARD --}}
            <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 overflow-hidden sticky top-6">
                {{-- Header: Total Due --}}
                <div class="bg-slate-900 p-6 text-white text-center relative overflow-hidden">
                     <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-slate-900 z-0"></div>
                     <div class="relative z-10">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Current Balance</p>
                        <h2 class="text-4xl font-black tracking-tight mb-2">₹{{ number_format($booking->balance_amount) }}</h2>
                        @if($booking->balance_amount > 0)
                            <button onclick="toggleModal('settleBillModal', true)" class="bg-white text-slate-900 text-xs font-black px-6 py-2 rounded-full hover:scale-105 transition-transform shadow-lg shadow-white/10">
                                Settle Bill
                            </button>
                        @else
                            <span class="inline-block px-3 py-1 bg-emerald-500/20 text-emerald-400 text-xs font-bold rounded-full border border-emerald-500/30">
                                Fully Paid
                            </span>
                        @endif
                     </div>
                </div>

                {{-- Body: Standard List --}}
                <div class="bg-slate-50 border-b border-slate-200 px-4 py-2 flex justify-between items-center text-xs font-bold text-slate-500">
                    <span>Folio Ledger</span>
                    <span>{{ $booking->folioItems->count() }} Items</span>
                </div>

                <div class="max-h-[500px] overflow-y-auto custom-scrollbar bg-white">
                    <table class="w-full text-left">
                        <tbody class="divide-y divide-slate-100">
                            @foreach($booking->folioItems->sortBy('posted_at') as $item)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $iconBg = match($item->type) {
                                                    'room_rent' => 'bg-blue-50 text-blue-600',
                                                    'service' => 'bg-purple-50 text-purple-600',
                                                    'tax' => 'bg-amber-50 text-amber-600',
                                                    'payment' => 'bg-emerald-50 text-emerald-600',
                                                    'discount' => 'bg-rose-50 text-rose-600',
                                                    default => 'bg-slate-50 text-slate-600'
                                                };
                                            @endphp
                                            <div class="w-8 h-8 rounded {{ $iconBg }} flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    @if($item->type == 'payment')
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    @else
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                    @endif
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-slate-800">{{ $item->description }}</p>
                                                <p class="text-[10px] text-slate-400">
                                                    {{ $item->posted_at->format('M d, H:i') }}
                                                    @if($item->reference_date) | Night of {{ $item->reference_date->format('M d') }} @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <span class="text-sm font-bold {{ $item->type == 'payment' ? 'text-emerald-600' : 'text-slate-700' }} font-mono">
                                            {{ $item->type == 'payment' || $item->type == 'discount' ? '-' : '' }}₹{{ number_format($item->amount) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer: Summary --}}
                <div class="bg-slate-50 border-t border-slate-200 p-4 space-y-2">
                    <div class="flex justify-between text-xs text-slate-500">
                        <span>Subtotal</span>
                        <span class="font-mono font-bold">₹{{ number_format($booking->total_bill) }}</span>
                    </div>
                    <div class="flex justify-between text-xs text-emerald-600">
                        <span>Paid</span>
                        <span class="font-mono font-bold">-₹{{ number_format($booking->paid_amount) }}</span>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden min-h-[150px]">
                <div class="px-4 py-2 border-b border-slate-100 bg-slate-50/50">
                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">Private Notes</span>
                </div>
                <textarea class="w-full text-xs p-4 border-0 focus:ring-0 resize-none h-full bg-transparent placeholder:text-slate-300 outline-none hover:bg-slate-50/50 transition-colors" placeholder="Type internal notes regarding this stay..."></textarea>
            </div>

        </div>

    </div>

    {{-- MODALS --}}
    
    {{-- Check In Modal --}}
    <div id="checkInModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300">
        {{-- Content maintained --}}
         <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
             <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <span class="font-bold text-slate-800">Assign Rooms</span>
                <button onclick="toggleModal('checkInModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            
            {{-- Auto Assign Option --}}
            @if($availableRooms->count() > 0 && $booking->assignedRooms->isEmpty())
                <form action="{{ route('admin.bookings.auto-assign', $booking) }}" method="POST" class="px-4 pt-4">
                    @csrf
                    <button type="submit" class="w-full py-2 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl font-bold text-xs hover:bg-indigo-100 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Auto-Assign Best Room
                    </button>
                </form>
            @endif

            <form action="{{ route('admin.bookings.checkIn', $booking) }}" method="POST" class="p-4">
                @csrf
                <div class="grid grid-cols-4 gap-2 mb-4 max-h-60 overflow-y-auto custom-scrollbar p-1">
                    @php 
                        $assignedIds = $booking->assignedRooms->pluck('id')->toArray(); 
                        $roomsCount = is_array($booking->rooms) ? array_sum($booking->rooms) : 1;
                        $inputType = $roomsCount <= 1 ? 'radio' : 'checkbox';
                    @endphp
                    @foreach($availableRooms as $room)
                        <label class="cursor-pointer group relative">
                            <input type="{{ $inputType }}" name="room_ids[]" value="{{ $room->id }}" class="peer sr-only" {{ in_array($room->id, $assignedIds) ? 'checked' : '' }}>
                            <div class="py-3 text-center rounded-xl border border-slate-200 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:shadow-lg hover:border-blue-300 transition-all relative overflow-hidden bg-white">
                                <span class="block font-black text-sm text-slate-800 peer-checked:text-white">{{ $room->room_number }}</span>
                                <span class="text-[9px] uppercase font-bold text-slate-500 peer-checked:text-blue-100 opacity-70">{{ substr($room->housekeeping_status, 0, 1) }}</span>
                                
                                @if(in_array($room->id, $assignedIds))
                                    <div class="absolute top-0 right-0">
                                        <div class="bg-emerald-500 text-white text-[8px] font-black px-1.5 py-0.5 rounded-bl-lg shadow-sm z-10">
                                            ASSIGNED
                                        </div>
                                    </div>
                                @endif
                                <div class="absolute inset-0 bg-blue-600/0 peer-checked:bg-blue-600/0 transition-colors"></div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-200 transition-all active:scale-95">Confirm Check In</button>
            </form>
        </div>
    </div>

    {{-- Add Service Modal --}}
    <div id="addServiceModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden animate-in fade-in zoom-in duration-200" x-data="{ selectedId: null, qty: 1, unit: 'Item', price: 0 }">
             <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <span class="font-bold text-slate-800">Add Service</span>
                <button onclick="document.getElementById('addServiceModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form action="{{ route('admin.bookings.add-service', $booking) }}" method="POST" class="p-4">
                @csrf
                <input type="hidden" name="service_id" :value="selectedId">
                <input type="hidden" name="quantity" :value="qty">

                {{-- Service Grid --}}
                <div class="grid grid-cols-2 gap-3 mb-6 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    @foreach($services as $service)
                        @php
                            // Calculate current usage for this service
                            $currentUsage = 0;
                            // Need to loop through booking orders
                            foreach ($booking->roomServiceOrders as $order) {
                                $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                                if (is_array($items)) {
                                    foreach ($items as $item) {
                                        if (isset($item['id']) && $item['id'] == $service->id) {
                                            $currentUsage += intval($item['qty'] ?? 0);
                                        }
                                    }
                                }
                            }
                            
                            $maxAllowed = 999; // Default unlimited
                            if ($service->constraints && isset($service->constraints['max_quantity_rule']) && $service->constraints['max_quantity_rule'] === 'room_extra_capacity') {
                                $maxAllowed = 0;
                                foreach ($booking->assignedRooms as $room) {
                                    $maxAllowed += $room->roomType->max_extra_persons ?? 0;
                                }
                            }

                            $remaining = max(0, $maxAllowed - $currentUsage);
                            $isMaxReached = $remaining === 0;
                        @endphp

                        <div @if(!$isMaxReached) @click="selectedId = {{ $service->id }}; qty = 1; unit = '{{ $service->price_unit ?? 'Item' }}'; price = {{ $service->price }}; maxRemaining = {{ $remaining }}" @endif
                             :class="selectedId === {{ $service->id }} ? 'ring-2 ring-blue-500 bg-blue-50/50' : ('{{ $isMaxReached }}' ? 'opacity-50 cursor-not-allowed bg-slate-50' : 'hover:bg-slate-50 border-slate-100 cursor-pointer')"
                             class="flex items-center justify-between p-3 border rounded-xl transition-all group relative">
                            
                            <div class="flex items-center gap-3">
                                <div :class="selectedId === {{ $service->id }} ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500 group-hover:bg-white group-hover:shadow-sm'"
                                     class="w-10 h-10 rounded-lg flex items-center justify-center transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-slate-800">{{ $service->name }}</div>
                                    <div class="text-[10px] uppercase font-bold text-slate-400">
                                        @if($isMaxReached)
                                            <span class="text-red-500">Max Limit Reached</span>
                                        @else
                                            ₹{{ $service->price }} / {{ $service->price_unit ?? 'Item' }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center">
                                @if(!$isMaxReached)
                                    {{-- Selection State --}}
                                    <template x-if="selectedId !== {{ $service->id }}">
                                        <button type="button" class="w-8 h-8 rounded-full border border-slate-200 text-slate-400 flex items-center justify-center hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </button>
                                    </template>

                                    {{-- Active State (Stepper) --}}
                                    <template x-if="selectedId === {{ $service->id }}">
                                        <div class="flex items-center bg-white rounded-lg shadow-sm border border-blue-200">
                                            <button type="button" @click.stop="if(qty > 1) qty--; else selectedId = null" class="w-8 h-8 flex items-center justify-center text-blue-600 hover:bg-blue-50 rounded-l-lg transition">-</button>
                                            <span class="w-8 text-center text-xs font-bold text-slate-800" x-text="qty"></span>
                                            <button type="button" @click.stop="if(qty < maxRemaining) qty++" :class="qty >= maxRemaining ? 'opacity-50 cursor-not-allowed' : 'hover:bg-blue-50'" class="w-8 h-8 flex items-center justify-center text-blue-600 rounded-r-lg transition">+</button>
                                        </div>
                                    </template>
                                @else
                                    <span class="text-xs font-bold text-slate-400">Full</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Footer Action --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <div class="text-xs text-slate-500 font-bold">
                        <span x-show="selectedId">Total: <span class="text-slate-800 text-lg ml-1">₹<span x-text="qty * price"></span></span></span>
                        <span x-show="!selectedId">Select a service to add</span>
                    </div>
                    <button type="submit" :disabled="!selectedId" :class="!selectedId ? 'opacity-50 cursor-not-allowed bg-slate-300' : 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-200 active:scale-95'" class="py-2.5 px-6 text-white rounded-xl font-bold text-sm transition-all">
                        Add to Bill
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Settle Bill Modal --}}
    <div id="settleBillModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
             <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <span class="font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Settle Bill
                </span>
                <button type="button" onclick="toggleModal('settleBillModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form action="{{ route('admin.bookings.markPaid', $booking) }}" method="POST" class="p-4" x-data="{ type: 'full', customAmount: '' }">
                @csrf

                {{-- Payment Type Toggle --}}
                <div class="flex bg-slate-100 p-1 rounded-xl mb-4">
                    <button type="button" @click="type = 'full'" :class="type === 'full' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2 rounded-lg text-xs font-bold transition-all">Full Balance (₹{{ number_format($booking->balance_amount) }})</button>
                    <button type="button" @click="type = 'partial'" :class="type === 'partial' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="flex-1 py-2 rounded-lg text-xs font-bold transition-all">Partial Amount</button>
                </div>

                <input type="hidden" name="type" :value="type">
                
                {{-- Partial Amount Input --}}
                <div x-show="type === 'partial'" class="mb-4">
                    <label class="text-[10px] font-bold text-slate-500 uppercase">Amount to Collect</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">₹</span>
                        <input type="number" name="amount" step="0.01" max="{{ $booking->balance_amount }}" class="w-full text-sm pl-6 p-2.5 bg-slate-50 border border-slate-200 rounded-lg font-bold outline-none focus:border-blue-500 transition">
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="mb-4">
                    <label class="text-[10px] font-bold text-slate-500 uppercase mb-2 block">Payment Mode</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="method" value="cash" class="peer sr-only" checked>
                            <div class="text-center py-2 border border-slate-200 rounded-lg peer-checked:bg-emerald-50 peer-checked:border-emerald-500 peer-checked:text-emerald-700 hover:bg-slate-50 text-xs font-bold transition">Cash</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="method" value="upi" class="peer sr-only">
                            <div class="text-center py-2 border border-slate-200 rounded-lg peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-700 hover:bg-slate-50 text-xs font-bold transition">UPI / QR</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="method" value="card" class="peer sr-only">
                            <div class="text-center py-2 border border-slate-200 rounded-lg peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 hover:bg-slate-50 text-xs font-bold transition">Card</div>
                        </label>
                    </div>
                    {{-- Custom Method --}}
                    <div class="mt-2">
                        <input type="text" name="method_custom" placeholder="Other (e.g. Cheque, Bank Transfer)" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-lg font-bold outline-none focus:border-blue-500 transition placeholder:font-normal" oninput="if(this.value) { document.querySelectorAll('input[name=method]').forEach(r => r.checked = false); this.name='method'; } else { document.querySelector('input[value=cash]').checked = true; this.name='method_custom'; }">
                    </div>
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                    <label class="text-[10px] font-bold text-slate-500 uppercase">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full text-xs p-2.5 bg-slate-50 border border-slate-200 rounded-lg font-bold outline-none focus:border-blue-500 transition resize-none"></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition-all active:scale-95">Collect Payment</button>
            </form>
        </div>
     </div>

    {{-- Move Room Modal --}}
    <div id="moveRoomModal" class="fixed inset-0 z-[100] hidden flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4 transition-opacity duration-300">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform scale-95 opacity-0 transition-all duration-300">
             <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <span class="font-bold text-slate-800">Move Room</span>
                <button type="button" onclick="toggleModal('moveRoomModal', false)" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>
            <form action="{{ route('admin.bookings.process-check-in', $booking) }}" method="POST" class="p-4">
                @csrf
                <input type="hidden" name="is_check_in" value="0">
                 <div class="grid grid-cols-4 gap-2 mb-4 max-h-60 overflow-y-auto custom-scrollbar">
                    @foreach($availableRooms as $room)
                        <label class="cursor-pointer group">
                            <input type="radio" name="room_ids[]" value="{{ $room->id }}" class="peer sr-only">
                            <div class="py-3 text-center rounded-xl border border-slate-200 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:bg-slate-50 transition relative overflow-hidden">
                                <span class="block font-bold text-sm">{{ $room->room_number }}</span>
                                <span class="text-[9px] uppercase font-bold">{{ substr($room->housekeeping_status, 0, 1) }}</span>
                                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                        </label>
                    @endforeach
                </div>
                <button type="submit" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 transition active:scale-95">Confirm Move</button>
            </form>
        </div>
     </div>
    
    <script>
        function toggleModal(id, show) {
            const el = document.getElementById(id);
            if(show) {
                el.classList.remove('hidden');
                setTimeout(() => {
                    el.querySelector('div').classList.remove('scale-95', 'opacity-0');
                    el.querySelector('div').classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                el.querySelector('div').classList.remove('scale-100', 'opacity-100');
                el.querySelector('div').classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    el.classList.add('hidden');
                }, 300);
            }
        }
        
        // Override default onclicks for animation
        document.querySelectorAll('[onclick*="classList.remove(\'hidden\')"]').forEach(btn => {
            const id = btn.getAttribute('onclick').match(/'([^']+)'/)[1];
            btn.onclick = (e) => { e.preventDefault(); toggleModal(id, true); };
        });

        // Add Service Modal Logic
        const serviceSelect = document.getElementById('serviceSelect');
        const qtyLabel = document.getElementById('qtyLabel');
        const unitDisplay = document.getElementById('unitDisplay');

        if(serviceSelect) {
            serviceSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                const unit = option.getAttribute('data-unit') || 'Item';
                qtyLabel.innerText = `Quantity (${unit})`;
                unitDisplay.innerText = unit + '(s)';
            });
            // Trigger once
            serviceSelect.dispatchEvent(new Event('change'));
        }
    </script>
@endsection