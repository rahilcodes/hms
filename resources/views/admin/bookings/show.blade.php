@extends('layouts.admin')

@section('header_title')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.bookings.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <span>Manage Booking #{{ $booking->id + 1000 }}</span>
    </div>
@endsection

@section('content')

    {{-- TOP ACTION BAR --}}
    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-6 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">Reservation
                    Status</p>
                <div class="flex items-center gap-2">
                    @php
                        $opStatus = $booking->operational_status;
                        $statusStyle = match ($opStatus) {
                            'pending_checkin' => 'bg-amber-50 text-amber-600 border border-amber-100',
                            'in_house' => 'bg-blue-50 text-blue-600 border border-blue-100',
                            'pending_checkout' => 'bg-pink-50 text-pink-600 border border-pink-100',
                            'overdue_checkout' => 'bg-rose-50 text-rose-600 border border-rose-100',
                            'checked_out' => 'bg-slate-50 text-slate-500 border border-slate-100',
                            'cancelled' => 'bg-slate-100 text-slate-400',
                            default => 'bg-emerald-50 text-emerald-600 border border-emerald-100' // upcoming
                        };
                    @endphp
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $statusStyle }}">
                        {{ str_replace('_', ' ', $opStatus) }}
                    </span>
                    <div class="flex flex-col">
                        <p class="text-[10px] text-slate-500 font-medium italic">Reservation Created:
                            {{ $booking->created_at->format('M d, Y H:i') }}</p>
                        @if($booking->checked_in_at)
                            <p class="text-[10px] text-blue-600 font-bold italic">Checked-In:
                                {{ $booking->checked_in_at->format('M d, Y H:i') }}</p>
                        @endif
                        @if($booking->checked_out_at)
                            <p class="text-[10px] text-rose-600 font-bold italic">Checked-Out:
                                {{ $booking->checked_out_at->format('M d, Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto" x-data="{ showReschedule: false }">
            <a href="{{ route('admin.bookings.invoice', $booking) }}" target="_blank"
                class="hidden md:flex px-4 py-2 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 text-xs font-bold transition items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Invoice
            </a>
            @if($opStatus === 'upcoming' || $opStatus === 'pending_checkin')
                <button @click="showReschedule = true"
                    class="flex-1 md:flex-none px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-xl hover:bg-slate-50 text-xs font-bold transition">
                    Reschedule Stay
                </button>
            @endif

            @if(!$booking->checked_in_at && $booking->status === 'confirmed')
                <div x-data="{ showCheckIn: false }">
                    <button @click="showCheckIn = true" 
                        class="w-full px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:shadow-lg hover:shadow-blue-200 text-xs font-bold transition">
                        Check In Guest
                    </button>

                    {{-- CHECK-IN MODAL --}}
                    <div x-show="showCheckIn" x-cloak
                        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
                        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100">
                        <div @click.away="showCheckIn = false"
                            class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden transform border border-white/20">

                            <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Room Assignment</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Select Physical Rooms</p>
                                </div>
                                <button @click="showCheckIn = false" class="text-slate-400 hover:text-slate-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <form action="{{ route('admin.bookings.checkIn', $booking) }}" method="POST" class="p-8">
                                @csrf
                                <div class="mb-6">
                                    <p class="text-sm font-bold text-slate-700 mb-3">Available {{ $booking->roomType->name }}s</p>

                                    @if($availableRooms->count() > 0)
                                        <div class="grid grid-cols-3 gap-3 max-h-60 overflow-y-auto p-1">
                                            @foreach($availableRooms as $room)
                                                <label class="cursor-pointer">
                                                    <input type="checkbox" name="room_ids[]" value="{{ $room->id }}" class="peer sr-only">
                                                    <div class="p-3 text-center rounded-xl border-2 border-slate-100 bg-slate-50 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition hover:border-blue-200">
                                                        <span class="block text-lg font-black text-slate-800 peer-checked:text-blue-700">{{ $room->room_number }}</span>
                                                        <span class="block text-[9px] font-bold uppercase {{ $room->housekeeping_status === 'clean' ? 'text-emerald-500' : 'text-amber-500' }}">
                                                            {{ $room->housekeeping_status }}
                                                        </span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        <p class="text-[10px] text-slate-400 mt-2 italic">* Select {{ array_sum($booking->rooms ?? []) }} room(s) for this booking.</p>
                                    @else
                                        <div class="p-4 bg-rose-50 border border-rose-100 rounded-xl text-rose-600 text-xs font-bold text-center">
                                            No available rooms of this type found!
                                        </div>
                                    @endif
                                </div>

                                <button type="submit"
                                    class="w-full py-4 bg-blue-600 text-white rounded-2xl font-black text-sm shadow-xl hover:bg-blue-700 transition">
                                    Confirm Check-In
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            @if($booking->checked_in_at && !$booking->checked_out_at)
                <form action="{{ route('admin.bookings.checkOut', $booking) }}" method="POST" class="flex-1 md:flex-none">
                    @csrf
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-rose-600 text-white rounded-xl hover:shadow-lg hover:shadow-rose-200 text-xs font-bold transition">
                        Check Out
                    </button>
                </form>
            @endif

            @if($booking->status !== 'cancelled' && !$booking->checked_in_at)
                <form action="{{ route('admin.bookings.cancel', $booking) }}" method="POST" class="flex-1 md:flex-none"
                    onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                    @csrf
                    <button type="submit"
                        class="w-full px-5 py-2.5 bg-white border border-rose-100 text-rose-600 rounded-xl hover:bg-rose-50 text-xs font-bold transition">
                        Cancel
                    </button>
                </form>
            @endif

            {{-- RESCHEDULE MODAL --}}
            <div x-show="showReschedule" x-cloak
                class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100">
                <div @click.away="showReschedule = false"
                    class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden transform border border-white/20">
                    <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Reschedule Stay</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Adjust Date Range
                            </p>
                        </div>
                        <button @click="showReschedule = false"
                            class="p-2 hover:bg-slate-200/50 rounded-xl transition text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('admin.bookings.reschedule', $booking) }}" method="POST" class="p-10 space-y-8">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-6">
                            <div class="space-y-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">New
                                    Check-in</label>
                                <input type="date" name="check_in" value="{{ $booking->check_in->format('Y-m-d') }}"
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none">
                            </div>
                            <div class="space-y-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">New
                                    Check-out</label>
                                <input type="date" name="check_out" value="{{ $booking->check_out->format('Y-m-d') }}"
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none">
                            </div>
                        </div>
                        <button type="submit"
                            class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black text-sm shadow-2xl shadow-blue-500/20 hover:bg-blue-700 transition transform active:scale-95 duration-200">
                            Confirm New Dates
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- LEFT COLUMN: DETAILS --}}
        <div class="lg:col-span-8 space-y-8">

            {{-- STAY GEOMETRY --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="text-lg font-bold text-slate-900">Stay Timeline</h3>
                    <p class="text-xs text-slate-400 font-medium tracking-tight">Check-in and Check-out details</p>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-12 relative">
                    {{-- CONNECTOR ARROW --}}
                    <div class="hidden md:block absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 opacity-20">
                        <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </div>

                    <div class="flex items-center gap-6">
                        <div
                            class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">
                                Check-in</p>
                            <p class="text-xl font-bold text-slate-900 leading-tight">
                                {{ \Carbon\Carbon::parse($booking->check_in)->format('D, d M Y') }}
                            </p>
                            <p class="text-xs text-slate-500 font-medium mt-1">Arrival after 12:00 PM</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-6 md:justify-end text-right">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none mb-1">
                                Check-out</p>
                            <p class="text-xl font-bold text-slate-900 leading-tight">
                                {{ \Carbon\Carbon::parse($booking->check_out)->format('D, d M Y') }}
                            </p>
                            <p class="text-xs text-slate-500 font-medium mt-1">Departure before 11:00 AM</p>
                        </div>
                        <div
                            class="w-16 h-16 bg-orange-50 text-orange-600 rounded-3xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- ROOM CONFIGURATION --}}
                <div class="px-8 pb-8 pt-4 space-y-4">
                    <div
                        class="bg-slate-50 rounded-2xl p-6 border border-slate-100 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 bg-white rounded-xl border border-slate-200 flex items-center justify-center text-slate-400 shadow-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900">
                                    {{ $booking->roomType->name ?? 'Special Assignment' }}
                                </h4>
                                @if($booking->assignedRooms->count() > 0)
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        @foreach($booking->assignedRooms as $assigned)
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-black rounded-md border border-blue-200">
                                                {{ $assigned->room_number }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">
                                        {{ array_sum($booking->rooms ?? []) }} Unit(s) Allocated
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span
                                class="px-3 py-1 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-slate-600 shadow-sm">
                                Duration: {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }}
                                Nights
                            </span>
                        </div>
                    </div>

                    {{-- ADD-ON SERVICES --}}
                    @if(!empty($booking->services_json))
                        <div class="p-6 bg-blue-50/50 rounded-2xl border border-blue-100">
                            <h5 class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mb-4">Add-on Services & Enhancements</h5>
                            <div class="space-y-3">
                                @foreach($booking->services_json as $service)
                                    <div class="flex justify-between items-center text-xs">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>
                                            <span class="font-bold text-slate-900">{{ $service['name'] }}</span>
                                            @if(($service['qty'] ?? 1) > 1 || ($service['price_unit'] ?? 'fixed') !== 'fixed')
                                                <span class="px-1.5 py-0.5 bg-blue-100 text-blue-600 rounded text-[9px] font-black">
                                                    {{ $service['qty'] ?? 1 }} {{ str_replace('per_', '', $service['price_unit'] ?? 'fixed') }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="font-bold text-slate-700">₹{{ number_format($service['price'] * ($service['qty'] ?? 1), 0) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- CORPORATE & GROUP CONTEXT --}}
            @if($booking->company_id || $booking->group_id)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($booking->company_id)
                        <div class="bg-blue-600 rounded-3xl p-6 text-white shadow-lg shadow-blue-200">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-blue-100">Corporate Account</p>
                                    <h4 class="text-sm font-bold">{{ $booking->company->name }}</h4>
                                </div>
                            </div>
                            <div class="space-y-2 opacity-90">
                                <p class="text-[10px] flex justify-between">
                                    <span>Credit Limit:</span>
                                    <span class="font-bold">₹{{ number_format($booking->company->credit_limit) }}</span>
                                </p>
                                <p class="text-[10px] flex justify-between">
                                    <span>GST / ID:</span>
                                    <span class="font-bold">{{ $booking->company->gst_number ?? 'N/A' }}</span>
                                </p>
                            </div>
                            <a href="{{ route('admin.companies.edit', $booking->company_id) }}" class="mt-4 block text-center py-2 bg-white/10 hover:bg-white/20 rounded-xl text-[10px] font-bold transition">
                                View Corporate Profile
                            </a>
                        </div>
                    @endif

                    @if($booking->group_id)
                        <div class="bg-purple-600 rounded-3xl p-6 text-white shadow-lg shadow-purple-200">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-widest text-purple-100">Group ID</p>
                                    <h4 class="text-xs font-mono font-bold truncate w-32">{{ $booking->group_id }}</h4>
                                </div>
                            </div>

                            @if(count($groupMembers) > 0)
                                <div class="space-y-2 max-h-24 overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($groupMembers as $member)
                                        <a href="{{ route('admin.bookings.show', $member) }}" class="flex justify-between items-center p-2 bg-white/10 hover:bg-white/20 rounded-lg transition group/link">
                                            <span class="text-[9px] font-bold">{{ $member->roomType->name }}</span>
                                            <svg class="w-3 h-3 translate-x--2 opacity-0 group-hover/link:opacity-100 group-hover/link:translate-x-0 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-[10px] text-purple-100 italic">No other active rooms in group.</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30">
                    <h3 class="text-lg font-bold text-slate-900">Guest Roster</h3>
                    <p class="text-xs text-slate-400 font-medium tracking-tight">Registered guest details</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach($booking->guests as $guest)
                        <div class="px-8 py-5 flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 bg-slate-100 text-slate-400 rounded-xl flex items-center justify-center font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition-colors duration-200">
                                    {{ strtoupper(substr($guest->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $guest->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Primary Point of Contact</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-slate-700 leading-tight">{{ $guest->phone ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400 font-medium lowercase">
                                    {{ $guest->email ?? 'No email recorded' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="lg:col-span-4 space-y-8">

            {{-- FINANCIAL CARD --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
                <h4 class="text-sm font-bold text-slate-900 mb-8 pb-4 border-b border-slate-100">Financial Insights</h4>

                <div class="space-y-4 mb-8">
                    @php
                        $diningTotal = $booking->roomServiceOrders()->where('status', '!=', 'cancelled')->sum('total_amount');
                        $servicesTotal = collect($booking->services_json)->sum(fn($s) => ($s['price'] ?? 0) * ($s['qty'] ?? 1));
                    @endphp

                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 font-medium">Base Stay Charges</span>
                        <span class="text-slate-900 font-black">₹{{ number_format($booking->total_amount - $servicesTotal) }}</span>
                    </div>

                    @if($servicesTotal > 0)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 font-medium">Add-on Services</span>
                            <span class="text-blue-600 font-black">+₹{{ number_format($servicesTotal) }}</span>
                        </div>
                    @endif

                    @if($diningTotal > 0)
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-slate-500 font-medium">In-Room Dining</span>
                            <span class="text-rose-600 font-black">+₹{{ number_format($diningTotal) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center pt-4 border-t border-slate-100">
                        <span class="text-xs font-black text-slate-900">Net Total</span>
                        <span class="text-2xl font-black text-slate-900 tracking-tight">₹{{ number_format($booking->total_bill) }}</span>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Paid
                                (Advance)</span>
                            <span
                                class="text-xs font-bold text-emerald-600">₹{{ number_format($booking->paid_amount) }}</span>
                        </div>
                        <div class="flex justify-between items-center pt-3 border-t border-slate-200">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Balance
                                Pending</span>
                            <span
                                class="text-xs font-extrabold {{ $booking->balance_amount > 0 ? 'text-amber-600' : 'text-emerald-600' }}">₹{{ number_format($booking->balance_amount) }}</span>
                        </div>
                    </div>
                </div>

                @if($booking->balance_amount > 0 && $booking->status !== 'cancelled')
                    <div x-data="{ 
                        showModal: false, 
                        type: 'full', 
                        method: 'cash', 
                        amount: {{ $booking->balance_amount }},
                        balance: {{ $booking->balance_amount }}
                    }">
                        <button @click="showModal = true"
                            class="w-full py-4 bg-blue-600 text-white rounded-2xl font-bold text-sm shadow-xl shadow-blue-100 hover:bg-blue-700 transition transform active:scale-95 duration-200">
                            Mark Remaining as Paid
                        </button>

                        {{-- SETTLEMENT MODAL --}}
                        <div x-show="showModal" x-cloak
                            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md"
                            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100">
                            <div @click.away="showModal = false"
                                class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden transform border border-white/20">
                                <div class="px-10 py-8 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                                    <div>
                                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Settlement Intelligence</h3>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Record Payment Transaction</p>
                                    </div>
                                    <button @click="showModal = false" class="p-2 hover:bg-slate-200/50 rounded-xl transition text-slate-400">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <form action="{{ route('admin.bookings.markPaid', $booking) }}" method="POST" class="p-10 space-y-8">
                                    @csrf
                                    <div class="space-y-6">
                                        <div class="grid grid-cols-2 gap-4">
                                            <label class="relative block cursor-pointer">
                                                <input type="radio" name="type" value="full" x-model="type" class="sr-only" @change="amount = balance">
                                                <div :class="type === 'full' ? 'border-blue-500 bg-blue-50/50' : 'border-slate-100 bg-slate-50'" class="p-4 border-2 rounded-2xl transition">
                                                    <p class="text-[10px] font-black uppercase tracking-widest mb-1" :class="type === 'full' ? 'text-blue-600' : 'text-slate-400'">Full Clear</p>
                                                    <p class="text-sm font-bold text-slate-900">₹{{ number_format($booking->balance_amount) }}</p>
                                                </div>
                                            </label>
                                            <label class="relative block cursor-pointer">
                                                <input type="radio" name="type" value="partial" x-model="type" class="sr-only">
                                                <div :class="type === 'partial' ? 'border-blue-500 bg-blue-50/50' : 'border-slate-100 bg-slate-50'" class="p-4 border-2 rounded-2xl transition">
                                                    <p class="text-[10px] font-black uppercase tracking-widest mb-1" :class="type === 'partial' ? 'text-blue-600' : 'text-slate-400'">Partial Clear</p>
                                                    <p class="text-sm font-bold text-slate-900">Custom Amount</p>
                                                </div>
                                            </label>
                                        </div>

                                        <div x-show="type === 'partial'" x-transition class="space-y-2">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Payment Amount</label>
                                            <div class="relative">
                                                <span class="absolute left-6 top-1/2 -translate-y-1/2 font-bold text-slate-400 italic">₹</span>
                                                <input type="number" name="amount" x-model="amount" step="0.01" :max="balance"
                                                    class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none">
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mode of Payment</label>
                                            <div class="relative" x-data="{ open: false }">
                                                <button type="button" @click="open = !open" @click.away="open = false"
                                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold flex items-center justify-between group transition-all duration-300 hover:border-blue-200">
                                                    <div class="flex items-center gap-3 text-slate-900">
                                                        <span x-show="method === 'cash'">
                                                            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.197c1.033.258 2.03.35 2.895-.348 1.487-1.204 1.274-3.418.966-5.091L19.16 11.17M2.25 18.75 4.105 7.561a3.375 3.375 0 0 1 3.313-2.811h11.172a3.375 3.375 0 0 1 3.312 2.81L21.75 12.75M12 11.25a3.375 3.375 0 1 1 0-6.75 3.375 3.375 0 0 1 0 6.75Zm0 1.125a3.375 3.375 0 1 0-6.75 0 3.375 3.375 0 0 0 6.75 0Z" /></svg>
                                                        </span>
                                                        <span x-show="method === 'upi'">
                                                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                                                        </span>
                                                        <span x-show="method === 'card'">
                                                            <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" /></svg>
                                                        </span>
                                                        <span x-show="method === 'bank_transfer'">
                                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" /></svg>
                                                        </span>
                                                        <span x-text="{
                                                            'cash': 'Cash Payment',
                                                            'upi': 'UPI / QR Code',
                                                            'card': 'Credit/Debit Card',
                                                            'bank_transfer': 'Bank Transfer'
                                                        }[method]">Cash</span>
                                                    </div>
                                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-500 transition-transform duration-300" 
                                                         :class="open ? 'rotate-180' : ''"
                                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                    </svg>
                                                </button>

                                                <input type="hidden" name="method" x-model="method">

                                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 translate-y-2 scale-95" 
                                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                     x-transition:leave="transition ease-in duration-100" 
                                                     x-transition:leave-start="opacity-100 scale-100"
                                                     x-transition:leave-end="opacity-0 scale-95"
                                                     class="absolute left-0 right-0 mt-3 bg-white rounded-3xl shadow-2xl border border-slate-100 p-2 z-[100]">

                                                    @foreach([
                                                            'cash' => ['label' => 'Cash Payment', 'desc' => 'Collected in Cash', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.197c1.033.258 2.03.35 2.895-.348 1.487-1.204 1.274-3.418.966-5.091L19.16 11.17M2.25 18.75 4.105 7.561a3.375 3.375 0 0 1 3.313-2.811h11.172a3.375 3.375 0 0 1 3.312 2.81L21.75 12.75M12 11.25a3.375 3.375 0 1 1 0-6.75 3.375 3.375 0 0 1 0 6.75Zm0 1.125a3.375 3.375 0 1 0-6.75 0 3.375 3.375 0 0 0 6.75 0Z" />', 'color' => 'emerald'],
                                                            'upi' => ['label' => 'UPI / QR Code', 'desc' => 'Digital Transfer', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />', 'color' => 'blue'],
                                                            'card' => ['label' => 'Credit/Debit Card', 'desc' => 'Terminal / POS', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />', 'color' => 'purple'],
                                                            'bank_transfer' => ['label' => 'Bank Transfer', 'desc' => 'NEFT / IMPS', 'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z" />', 'color' => 'amber']
                                                        ] as $val => $data)
                                                            <div @click="method = '{{ $val }}'; open = false"
                                                                :class="method === '{{ $val }}' ? 'bg-blue-600 text-white shadow-xl shadow-blue-200' : 'hover:bg-slate-50 text-slate-600'"
                                                                class="flex items-center gap-4 p-4 rounded-2xl transition-all duration-200 cursor-pointer group/item">
                                                                <div :class="method === '{{ $val }}' ? 'bg-white/20' : 'bg-{{ $data['color'] }}-50 text-{{ $data['color'] }}-600'" 
                                                                     class="w-10 h-10 rounded-xl flex items-center justify-center">
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $data['icon'] !!}</svg>
                                                                </div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-xs font-black" :class="method === '{{ $val }}' ? 'text-white' : 'text-slate-900'">{{ $data['label'] }}</span>
                                                                    <span class="text-[9px] font-bold" :class="method === '{{ $val }}' ? 'text-blue-100' : 'text-slate-400'">{{ $data['desc'] }}</span>
                                                                </div>
                                                                <template x-if="method === '{{ $val }}'">
                                                                    <svg class="w-4 h-4 ml-auto text-white" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                </template>
                                                            </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit"
                                        class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black text-sm shadow-2xl shadow-blue-500/20 hover:bg-blue-700 transition transform active:scale-95 duration-200">
                                        Confirm & Process Settlement
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-blue-50/50 rounded-2xl border border-blue-100 flex gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-[10px] font-medium text-blue-700 leading-relaxed">
                            Payment collection required. Verify transaction before recording in system.
                        </p>
                    </div>
                @else
                    <div class="p-5 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-3xl border border-emerald-100 flex flex-col items-center justify-center gap-3 text-center">
                        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border border-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-emerald-900 uppercase tracking-widest leading-none mb-1">Status Paid</p>
                            <p class="text-[10px] font-bold text-emerald-600/80">Account Fully Settled</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- INTERNAL LOG (Audit Prep) --}}
            <div class="bg-slate-900 rounded-3xl border border-slate-800 shadow-xl p-8 text-white">
                <h4 class="text-sm font-bold mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 5.382V12c0 5.108 3.107 9.47 7.5 11.132 4.393-1.662 7.5-6.024 7.5-11.132V5.382l-.882-.398z" />
                    </svg>
                    System Logs
                </h4>
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-400 mt-1.5 flex-shrink-0"></div>
                        <p class="text-[10px] font-medium text-slate-400">Created via <span class="text-blue-400">Direct
                                Portal</span> on {{ $booking->created_at->format('M d, H:i') }}</p>
                    </div>
                </div>
                <button
                    class="w-full mt-6 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-[10px] font-bold text-slate-400 hover:text-white transition duration-200">
                    View Full Audit Log
                </button>
            </div>
        </div>
    </div>

@endsection