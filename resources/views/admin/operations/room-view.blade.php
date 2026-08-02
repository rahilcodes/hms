@extends('layouts.admin')

@section('title', 'Room Master View')

@section('content')
<div class="h-[calc(100vh-6rem)] flex flex-col">
    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4 px-1">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Room Master View</h1>
            <p class="text-slate-500 text-sm font-medium">{{ $today->format('D, d M Y') }}</p>
        </div>
        <div class="flex gap-2">
            {{-- LEGEND --}}
            <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-lg shadow-sm border border-slate-200 text-xs font-bold">
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-emerald-100 border border-emerald-300"></span> Vacant</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-blue-100 border border-blue-300"></span> Occupied</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-100 border border-amber-300"></span> Arrival</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-rose-100 border border-rose-300"></span> Departure</span>
                <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-purple-100 border border-purple-300"></span> Turnover</span>
            </div>
            <button onclick="window.location.reload()" class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            </button>
        </div>
    </div>

    {{-- UNASSIGNED ARRIVALS ALERT --}}
    @if($unassignedBookings->isNotEmpty())
        <div class="mb-6 mx-2 p-4 bg-amber-50 border border-amber-200 rounded-xl flex flex-col sm:flex-row items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-amber-100 rounded-full text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-800">{{ $unassignedBookings->count() }} Pending Arrivals</h3>
                    <p class="text-sm text-slate-600">These guests are arriving today but have no room assigned.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($unassignedBookings as $booking)
                    <div class="flex items-center gap-3 px-4 py-2 bg-white rounded-lg border border-amber-200 shadow-sm">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $booking->guest_name }}</p>
                            <p class="text-[10px] uppercase font-bold text-slate-400">{{ $booking->roomType->name }}</p>
                        </div>
                        <a href="{{ route('admin.bookings.smart-check-in', $booking->id) }}" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-md shadow-sm shadow-amber-200">
                            Assign Room
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- GRID --}}
    <div class="flex-1 overflow-y-auto pr-2 pb-8 mt-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            @foreach($rooms as $room)
                @php
                    $statusColor = match($room->status) {
                        'vacant' => 'from-emerald-50 to-white hover:to-emerald-50/30',
                        'vacant_dirty' => 'from-slate-50 to-white grayscale opacity-70',
                        'occupied' => 'from-blue-50 to-white hover:to-blue-50/30',
                        'arrival' => 'from-amber-50 to-white hover:to-amber-50/30 border-amber-200',
                        'occupied_arrival' => 'from-blue-50 to-white hover:to-blue-50/30',
                        'departure' => 'from-rose-50 to-white hover:to-rose-50/30 border-rose-200',
                        'turnover' => 'from-purple-50 to-white border-purple-200',
                        default => 'bg-white'
                    };

                    $hkColor = match($room->hk_status) {
                        'clean' => 'text-emerald-500 bg-emerald-50',
                        'dirty' => 'text-rose-500 bg-rose-50',
                        'maintenance' => 'text-slate-500 bg-slate-100',
                        'inspection' => 'text-amber-500 bg-amber-50',
                        default => 'text-slate-500 bg-slate-100'
                    };

                    $borderStatus = match($room->status) {
                        'vacant' => 'border-emerald-100',
                        'occupied', 'occupied_arrival' => 'border-blue-200',
                        'arrival' => 'border-amber-200',
                        'departure' => 'border-rose-200',
                        'turnover' => 'border-purple-200',
                        default => 'border-slate-200'
                    };
                @endphp

                <div class="relative group h-full">
                    @if($room->status !== 'vacant' && $room->status !== 'vacant_dirty' && $room->status !== 'turnover')
                        @php $activeBooking = $room->booking_stayover ?? $room->booking_arrival ?? $room->booking_departure; @endphp
                        <a href="{{ route('admin.bookings.show', $activeBooking->id) }}" class="block h-full no-underline">
                    @else
                        <div class="h-full">
                    @endif

                    <div class="h-full bg-gradient-to-br {{ $statusColor }} rounded-2xl border-2 {{ $borderStatus }} p-4 transition-all duration-300 transform group-hover:-translate-y-1 group-hover:shadow-xl group-hover:shadow-slate-200/50 flex flex-col justify-between">
                        
                        {{-- CARD HEADER --}}
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-xl font-black text-slate-900">{{ $room->number }}</h3>
                                    <span class="text-[9px] font-black uppercase tracking-tighter text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded leading-none">{{ $room->type }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest border border-current {{ $hkColor }}">
                                    {{ $room->hk_status }}
                                </span>
                            </div>
                        </div>

                        {{-- MAIN CONTENT AREA --}}
                        <div class="flex-1 min-h-[4rem]">
                            @if($room->status === 'vacant' || $room->status === 'vacant_dirty')
                                <div class="h-full flex flex-col items-center justify-center opacity-40">
                                    <p class="text-[11px] font-black uppercase tracking-widest text-slate-400">Available</p>
                                </div>
                            @elseif($room->status === 'turnover')
                                <div class="space-y-3">
                                    {{-- Multi-action for turnover --}}
                                    <a href="{{ route('admin.bookings.show', $room->booking_departure->id) }}" class="block p-2 bg-rose-50/50 border border-rose-100 rounded-xl hover:bg-rose-100 transition-colors">
                                        <div class="flex justify-between items-center">
                                            <p class="text-[8px] font-black text-rose-500 uppercase tracking-widest">OUT</p>
                                            <p class="text-[8px] font-bold text-slate-400">#{{ $room->booking_departure->id + 1000 }}</p>
                                        </div>
                                        <p class="text-xs font-black text-slate-800 truncate">{{ $room->booking_departure->guests->first()->name ?? 'Guest' }}</p>
                                    </a>
                                    <a href="{{ route('admin.bookings.show', $room->booking_arrival->id) }}" class="block p-2 bg-emerald-50/50 border border-emerald-100 rounded-xl hover:bg-emerald-100 transition-colors">
                                        <div class="flex justify-between items-center">
                                            <p class="text-[8px] font-black text-emerald-500 uppercase tracking-widest">IN</p>
                                            <p class="text-[8px] font-bold text-slate-400">#{{ $room->booking_arrival->id + 1000 }}</p>
                                        </div>
                                        <p class="text-xs font-black text-slate-800 truncate">{{ $room->booking_arrival->guests->first()->name ?? 'Guest' }}</p>
                                    </a>
                                </div>
                            @else
                                {{-- OCCUPIED / ARRIVAL / DEPARTURE --}}
                                @php $b = $room->booking_stayover ?? $room->booking_arrival ?? $room->booking_departure; @endphp
                                <div class="mb-2">
                                    <h4 class="text-base font-black text-slate-900 leading-tight mb-0.5">{{ $b->guests->first()->name ?? 'Guest' }}</h4>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold text-slate-500">{{ $b->nights }} Night Stay</span>
                                        @if($b->guests->first() && in_array('VIP', (array)($b->guests->first()->tags ?? [])))
                                            <span class="px-1.5 py-0.5 bg-amber-500 text-white text-[8px] font-black rounded uppercase tracking-widest leading-none">VIP</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- ADDONS & PRE-BOOKED SERVICES --}}
                                @php 
                                    $preBookedServices = collect($b->services_json ?? [])->pluck('name')->toArray();
                                    $roomServiceItems = $b->roomServiceOrders->flatMap->items->pluck('name')->unique()->toArray();
                                    $allAddons = array_unique(array_merge($preBookedServices, $roomServiceItems));
                                @endphp

                                @if(count($allAddons) > 0)
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach(array_slice($allAddons, 0, 3) as $addon)
                                            <span class="text-[8px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded leading-none border border-blue-100 truncate max-w-[80px]">{{ $addon }}</span>
                                        @endforeach
                                        @if(count($allAddons) > 3)
                                            <span class="text-[8px] font-bold text-slate-400">+{{ count($allAddons) - 3 }}</span>
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>

                        {{-- FOOTER AREA --}}
                        @if($room->status !== 'vacant' && $room->status !== 'vacant_dirty' && $room->status !== 'turnover')
                            @php $b = $room->booking_stayover ?? $room->booking_arrival ?? $room->booking_departure; @endphp
                            <div class="mt-3 pt-3 border-t border-slate-100 flex justify-between items-center">
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Status</span>
                                    <span class="text-[10px] font-black {{ $b->balance_amount > 0 ? 'text-rose-500' : 'text-emerald-500' }} uppercase tracking-wide">
                                        {{ $b->balance_amount > 0 ? 'Pending Bill' : 'Bill Cleared' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    @if($b->balance_amount > 0)
                                        <span class="text-xs font-black text-rose-600">₹{{ number_format($b->balance_amount) }}</span>
                                    @else
                                        <div class="w-5 h-5 bg-emerald-500 text-white rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="mt-3 pt-3 border-t border-slate-100/50 opacity-0">...</div>
                        @endif

                    </div>

                    @if($room->status !== 'vacant' && $room->status !== 'vacant_dirty' && $room->status !== 'turnover')
                        </a>
                    @else
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
