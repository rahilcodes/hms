@extends('layouts.admin')

@section('header_title', 'Front Desk Centre')

@section('content')

    <div class="mb-12 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Command Center</h2>
            <p class="text-slate-500 font-medium">Real-time management of guest arrivals and departures.</p>
        </div>
        <div class="bg-blue-600 text-white px-6 py-3 rounded-2xl shadow-xl shadow-blue-200 flex items-center gap-3">
            <div class="w-2 h-2 bg-white rounded-full animate-ping"></div>
            <span class="text-xs font-black uppercase tracking-widest">Live Monitoring Active</span>
        </div>
    </div>

    <div x-data="{ tab: 'arrivals' }">

        {{-- NAVIGATION TABS --}}
        <div class="flex gap-4 mb-12 border-b border-slate-100 pb-px">
            <button @click="tab = 'arrivals'"
                :class="tab === 'arrivals' ? 'text-blue-600 border-blue-600' : 'text-slate-400 border-transparent hover:text-slate-600'"
                class="pb-6 border-b-4 px-2 text-sm font-black uppercase tracking-widest transition-all relative">
                Arrivals
                <span :class="tab === 'arrivals' ? 'bg-blue-600' : 'bg-slate-200'"
                    class="ml-2 px-2 py-0.5 rounded-lg text-white text-[10px]">{{ $todayArrivals->count() }}</span>
            </button>
            <button @click="tab = 'departures'"
                :class="tab === 'departures' ? 'text-rose-600 border-rose-600' : 'text-slate-400 border-transparent hover:text-slate-600'"
                class="pb-6 border-b-4 px-2 text-sm font-black uppercase tracking-widest transition-all relative">
                Departures
                <span :class="tab === 'departures' ? 'bg-rose-600' : 'bg-slate-200'"
                    class="ml-2 px-2 py-0.5 rounded-lg text-white text-[10px]">{{ $todayDepartures->count() }}</span>
            </button>
        </div>

        {{-- ARRIVALS VIEW --}}
        <div x-show="tab === 'arrivals'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($todayArrivals as $booking)
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 hover:shadow-2xl hover:shadow-blue-100 transition-all duration-500 group relative overflow-hidden">
                        {{-- DECORATIVE ACCENT --}}
                        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-8 -mt-8 pointer-events-none"></div>

                        <div class="relative z-10 flex justify-between items-start mb-6">
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-black text-xs shadow-inner">
                                #{{ $booking->id + 1000 }}
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1">
                                    Room Type</p>
                                <p class="text-base font-black text-slate-900 leading-none truncate max-w-[120px]">
                                    {{ $booking->roomType->name ?? 'Suite' }}</p>
                            </div>
                        </div>

                        <h4 class="text-lg font-black text-slate-900 tracking-tight leading-tight mb-2 truncate">
                            {{ $booking->guest_name }}</h4>
                        <div class="flex items-center gap-2 mb-6">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            <p class="text-[10px] font-bold text-slate-500 italic uppercase tracking-wide">
                                {{ $booking->assignedRooms->count() > 0 
                                    ? 'Room: ' . $booking->assignedRooms->pluck('room_number')->implode(', ') 
                                    : 'Room Pending' }}
                            </p>
                        </div>

                        <div class="space-y-3 mb-8">
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Nights</p>
                                <p class="text-xs font-bold text-slate-900">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }}</p>
                            </div>
                        </div>

                        @if($booking->assignedRooms->count() >= $booking->rooms)
                            <form action="{{ route('admin.bookings.checkIn', $booking) }}" method="POST" x-data="{ loading: false }">
                                @csrf
                                <button type="submit" :disabled="loading" @click="loading = true"
                                    class="w-full py-4 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                    <svg x-show="loading" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="loading ? 'Checking in...' : 'Complete Check-In'"></span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.bookings.show', $booking) }}"
                                class="flex items-center justify-center w-full py-4 bg-slate-900 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg hover:bg-black hover:-translate-y-0.5 transition-all gap-2">
                                <span>Assign Room</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-32 flex flex-col items-center justify-center opacity-30">
                        <svg class="w-20 h-20 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-black uppercase tracking-widest italic text-slate-400">All Quiet on Arrival</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- DEPARTURES VIEW --}}
        <div x-show="tab === 'departures'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($todayDepartures as $booking)
                    <div class="bg-white rounded-3xl border border-slate-200 p-6 hover:shadow-2xl hover:shadow-rose-100 transition-all duration-500 group relative overflow-hidden">
                         {{-- DECORATIVE ACCENT --}}
                         <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-bl-full -mr-8 -mt-8 pointer-events-none"></div>

                        <div class="relative z-10 flex justify-between items-start mb-6">
                            <div class="w-14 h-14 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center font-black text-xs shadow-inner">
                                #{{ $booking->id + 1000 }}
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] text-slate-400 font-black uppercase tracking-widest leading-none mb-1">
                                    Room</p>
                                <p class="text-base font-black text-slate-900 leading-none">
                                    {{ $booking->assignedRooms->pluck('room_number')->first() ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <h4 class="text-lg font-black text-slate-900 tracking-tight leading-tight mb-2 truncate">
                            {{ $booking->guest_name }}</h4>
                        <div class="flex items-center gap-2 mb-6">
                            <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                            <p class="text-[10px] font-bold text-slate-500 italic uppercase tracking-wide">In-house</p>
                        </div>

                        <div class="space-y-3 mb-8">
                             <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Balance</p>
                                <p class="text-xs font-bold {{ $booking->balance_amount <= 0 ? 'text-emerald-500' : 'text-rose-500 animate-pulse' }}">
                                    {{ $booking->balance_amount <= 0 ? 'Settled' : 'Due: ₹' . number_format($booking->balance_amount) }}</p>
                            </div>
                        </div>

                        @if($booking->balance_amount > 0)
                            <a href="{{ route('admin.bookings.show', $booking) }}"
                                class="w-full block text-center py-4 bg-amber-500 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-amber-200/50 hover:bg-amber-600 hover:-translate-y-0.5 transition-all">
                                Settle & Checkout
                            </a>
                        @else
                            <form action="{{ route('admin.bookings.checkOut', $booking) }}" method="POST" x-data="{ loading: false }">
                                @csrf
                                <button type="submit" :disabled="loading" @click="loading = true"
                                    class="w-full py-4 bg-rose-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-rose-200 hover:bg-rose-700 hover:-translate-y-0.5 transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                    <svg x-show="loading" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="loading ? 'Checking out...' : 'Finalize Check-Out'"></span>
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-32 flex flex-col items-center justify-center opacity-30">
                        <svg class="w-20 h-20 mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <p class="text-sm font-black uppercase tracking-widest italic text-slate-400">All Departures Finalized</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

@endsection