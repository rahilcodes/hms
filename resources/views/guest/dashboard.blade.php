@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 pb-20">
    {{-- TITLE BAR --}}
    <div class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white font-black text-xl">
                    L
                </div>
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">Guest Portal</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Res #{{ $booking->id + 1000 }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if(count(Session::get('guest_available_bookings', [])) > 1)
                    <a href="{{ route('guest.select') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Switch Room
                    </a>
                @endif
                <form action="{{ route('guest.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- DASHBOARD HERO --}}
    <div class="max-w-7xl mx-auto px-6 mt-10">
        <div class="relative rounded-[3rem] overflow-hidden bg-slate-900 text-white min-h-[400px] flex items-center">
            @if($booking->roomType->image)
                <img src="{{ asset('storage/' . $booking->roomType->image) }}" 
                     class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-overlay">
            @endif
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 opacity-90"></div>
            
            <div class="relative z-10 p-12 lg:p-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center w-full">
                <div>
                    @php
                        $daysToStay = now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($booking->check_in)->startOfDay(), false);
                    @endphp
                    
                    <span class="inline-block px-4 py-1.5 bg-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest mb-6 {{ $booking->checked_in_at ? '' : 'animate-pulse' }}">
                        @if($booking->checked_in_at)
                            Successfully Checked In
                        @elseif($daysToStay > 0)
                            Starting in {{ $daysToStay }} Days
                        @elseif($daysToStay < 0)
                            Delayed Arrival
                        @else
                            Arriving Today!
                        @endif
                    </span>
                    
                    <h1 class="text-5xl lg:text-7xl font-black tracking-tighter mb-4 leading-none">
                        Welcome, <span class="text-blue-400">{{ explode(' ', $booking->guest_name)[0] }}</span>
                    </h1>
                    <p class="text-lg text-slate-300 font-medium max-w-md">
                        @if($booking->assignedRooms->count() > 0)
                            We've checked you into <span class="text-white font-black">Room {{ $booking->assignedRooms->first()->room_number }}</span>. Enjoy your stay.
                        @else
                            We're getting your <span class="text-white font-bold">{{ $booking->roomType->name }}</span> ready for an exceptional stay.
                        @endif
                    </p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="{{ route('guest.addons') }}" class="px-8 py-4 bg-white text-slate-900 rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-emerald-50 transition shadow-xl shadow-white/5">
                            Book Add-ons
                        </a>
                        <a href="{{ route('guest.dining') }}" class="px-8 py-4 bg-white/10 border border-white/20 text-white rounded-2xl text-sm font-black uppercase tracking-widest hover:bg-white/20 transition backdrop-blur-md">
                            Order Food
                        </a>
                    </div>
                </div>

                <div class="hidden lg:flex justify-end">
                    <div class="bg-white/10 backdrop-blur-2xl border border-white/10 rounded-[2.5rem] p-10 w-full max-w-sm">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Stay Geometry</p>
                        <div class="space-y-6">
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400">Check-in</p>
                                    <p class="text-lg font-black">{{ $booking->check_in->format('D, d M') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16l4-4m0 0l-4-4m4 4H3"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400">Check-out</p>
                                    <p class="text-lg font-black">{{ $booking->check_out->format('D, d M') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN STATS GRID --}}
    <div class="max-w-7xl mx-auto px-6 mt-12 grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- LEFT COLUMN: RESERVATION DETAILS --}}
        <div class="lg:col-span-8 space-y-8">
            {{-- FINANCE CARD --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between h-48 relative group cursor-pointer" x-data="{ billOpen: false }">
                    <div>
                         <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Bill</p>
                         <p class="text-3xl font-black text-slate-900">₹{{ number_format($booking->total_bill) }}</p>
                    </div>
                    <button @click="billOpen = true" class="text-xs font-bold text-blue-600 flex items-center gap-1 group-hover:gap-2 transition-all">
                        View Breakdown <span class="text-lg">→</span>
                    </button>

                    <!-- Bill Modal -->
                    <div x-show="billOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="billOpen = false"></div>
                        <div class="bg-white rounded-[2rem] max-w-md w-full relative z-10 shadow-2xl overflow-hidden">
                            <div class="bg-slate-900 p-8 text-white">
                                <h4 class="text-xl font-black">Booking Invoice</h4>
                                <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">#{{ $booking->uuid }}</p>
                            </div>
                            <div class="p-8 space-y-4">
                                <!-- Addons -->
                                @php
                                    $servicesTotal = collect($booking->services_json)->sum(function($s) {
                                        return ($s['price'] ?? 0) * ($s['qty'] ?? 1);
                                    });
                                    $baseRoomAmount = ($booking->total_amount - $servicesTotal) / 1.12;
                                @endphp

                                <!-- Room Charges -->
                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <div>
                                        <p class="font-bold text-slate-900">Room Charges</p>
                                        <p class="text-xs text-slate-500">{{ $booking->roomType->name }} x {{ $booking->nights }} Nights</p>
                                    </div>
                                    <p class="font-bold text-slate-900">₹{{ number_format($baseRoomAmount, 2) }}</p>
                                </div>

                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <div>
                                        <p class="font-bold text-slate-900">Add-on Services</p>
                                        <p class="text-xs text-slate-500">{{ count($booking->services_json ?? []) }} Items Requested</p>
                                    </div>
                                    <p class="font-bold text-slate-900">₹{{ number_format($servicesTotal, 2) }}</p>
                                </div>
                                
                                <!-- Taxes -->
                                <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                    <div>
                                        <p class="font-bold text-slate-900">Taxes & Fees</p>
                                        <p class="text-xs text-slate-500">GST (12%)</p>
                                    </div>
                                    <p class="font-bold text-slate-900">₹{{ number_format($booking->total_amount - $baseRoomAmount - $servicesTotal, 2) }}</p>
                                </div>

                                <!-- Dining Charges -->
                                @if($booking->total_bill > $booking->total_amount)
                                    <div class="flex justify-between items-center pb-4 border-b border-slate-100">
                                        <div>
                                            <p class="font-bold text-slate-900">In-Room Dining</p>
                                            <p class="text-xs text-slate-500">{{ $booking->roomServiceOrders->where('status', '!=', 'cancelled')->count() }} Order(s)</p>
                                        </div>
                                        <p class="font-bold text-slate-900">₹{{ number_format($booking->total_bill - $booking->total_amount, 2) }}</p>
                                    </div>
                                @endif

                                <div class="flex justify-between items-center pt-2">
                                    <p class="text-lg font-black text-slate-900">Grand Total</p>
                                    <p class="text-lg font-black text-slate-900">₹{{ number_format($booking->total_bill) }}</p>
                                </div>

                                <button @click="billOpen = false" class="w-full py-4 mt-4 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl transition uppercase tracking-widest text-xs">Close Receipt</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-emerald-50 p-8 rounded-[2rem] border border-emerald-100 flex flex-col justify-between h-48">
                    <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Paid (Advance)</p>
                    <p class="text-3xl font-black text-emerald-700">₹{{ number_format($booking->paid_amount) }}</p>
                </div>
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm flex flex-col justify-between h-48">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Balance at Desk</p>
                    <p class="text-3xl font-black text-rose-600">₹{{ number_format($booking->balance_amount) }}</p>
                </div>
            </div>

            {{-- SERVICES --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-10">
                <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Selected Accommodations</h3>
                
                <div class="space-y-6">
                    <div class="flex items-start gap-6 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                        <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-slate-900 text-lg">{{ $booking->roomType->name }}</h4>
                            @if($booking->assignedRooms->count() > 0)
                                <div class="flex gap-2 mt-1">
                                    @foreach($booking->assignedRooms as $room)
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-black rounded-lg border border-blue-200">Room {{ $room->room_number }}</span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-slate-500 font-medium">{{ array_sum($booking->rooms ?? []) }} Suite(s) Allocated</p>
                            @endif
                            
                            @php $extraG = $booking->meta['extra_persons'] ?? 0; @endphp
                            @if($extraG > 0)
                                <span class="inline-block mt-3 px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                    + {{ $extraG }} Extra Guest{{ $extraG > 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!empty($booking->services_json))
                        <div class="mt-10">
                            <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Enhancements & Services</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($booking->services_json as $service)
                                    <div class="p-6 border border-slate-100 rounded-3xl flex items-center gap-4 hover:border-blue-200 transition">
                                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900">{{ $service['name'] }}</p>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">
                                                {{ $service['qty'] ?? 1 }} Unit(s) Included
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- DIGITAL CONCIERGE --}}
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden p-10" x-data="{ 
                open: false, 
                checkInModal: false,
                type: '', 
                title: '',
                isCheckedIn: {{ $booking->checked_in_at ? 'true' : 'false' }} 
            }">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-900 rounded-xl flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-900 tracking-tight">Concierge Quick Actions</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tap to Request</span>
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Action Buttons -->
                    @foreach([
                        ['icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 'title' => 'Room Dining', 'url' => route('guest.dining'), 'color' => 'rose'],
                        ['icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6', 'title' => 'Upgrade Stay', 'url' => route('guest.addons'), 'color' => 'emerald'],
                        ['icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'title' => 'Housekeeping', 'type' => 'housekeeping', 'color' => 'blue'],
                        ['icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'title' => 'Report Issue', 'type' => 'maintenance', 'color' => 'amber'],
                    ] as $action)
                        @if(isset($action['url']))
                            <a href="{{ $action['url'] }}" 
                                @click.prevent="if(!isCheckedIn) { checkInModal = true } else { window.location.href = '{{ $action['url'] }}' }"
                                class="p-6 rounded-3xl border border-slate-100 bg-slate-50 hover:bg-{{ $action['color'] }}-50 hover:border-{{ $action['color'] }}-200 cursor-pointer transition group text-left relative overflow-hidden h-full flex flex-col justify-between">
                                 <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-{{ $action['color'] }}-500 mb-4 shadow-sm group-hover:scale-110 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"></path></svg>
                                 </div>
                                 <p class="font-bold text-slate-900 text-sm">{{ $action['title'] }}</p>
                            </a>
                        @else
                            <button @click="if(!isCheckedIn) { checkInModal = true } else { open = true; type = '{{ $action['type'] }}'; title = '{{ $action['title'] }}' }" 
                                class="p-6 rounded-3xl border border-slate-100 bg-slate-50 hover:bg-{{ $action['color'] }}-50 hover:border-{{ $action['color'] }}-200 cursor-pointer transition group text-left relative overflow-hidden h-full flex flex-col justify-between">
                                 <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-{{ $action['color'] }}-500 mb-4 shadow-sm group-hover:scale-110 transition text-left">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"></path></svg>
                                 </div>
                                 <p class="font-bold text-slate-900 text-sm">{{ $action['title'] }}</p>
                            </button>
                        @endif
                    @endforeach

                    <!-- Request Modal -->
                    <div x-show="open" x-cloak 
                        class="fixed inset-0 z-[100] flex items-center justify-center px-4"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="open = false"></div>
                        <div class="bg-white rounded-[2rem] p-8 max-w-sm w-full relative z-10 shadow-2xl transform transition-all">
                             <h4 class="text-xl font-black text-slate-900 mb-2">Request Service</h4>
                             <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mb-6">Type: <span x-text="title" class="text-blue-600"></span></p>
                             
                             <form method="POST" action="{{ route('guest.request.store') }}">
                                @csrf
                                <input type="hidden" name="type" :value="type">
                                
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Details (Optional)</label>
                                <textarea name="request" rows="3" class="w-full bg-slate-50 border-slate-200 rounded-xl mb-6 p-4 text-sm font-medium focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="E.g. Extra towels, Room Cleaning, Maintenance..."></textarea>
                                
                                <button type="submit" class="w-full py-4 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl transition shadow-lg shadow-slate-900/20">Submit Request</button>
                             </form>
                             <button @click="open = false" class="mt-4 w-full py-2 text-slate-400 hover:text-slate-600 text-[10px] font-black uppercase tracking-widest">Cancel</button>
                        </div>
                    </div>

                    <!-- Check-in Required Modal -->
                    <div x-show="checkInModal" x-cloak 
                        class="fixed inset-0 z-[100] flex items-center justify-center px-4"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0">
                        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="checkInModal = false"></div>
                        <div class="bg-white rounded-[2.5rem] p-10 max-w-sm w-full relative z-10 shadow-2xl text-center transform transition-all">
                            <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center text-amber-500 mx-auto mb-8 ring-8 ring-amber-50/50">
                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h4 class="text-2xl font-black text-slate-900 mb-4 tracking-tight">Check-in Required</h4>
                            <p class="text-slate-500 text-sm font-medium mb-10 leading-relaxed">
                                Please visit our reception to complete your check-in and room allocation before using these concierge features.
                            </p>
                            <button @click="checkInModal = false" class="w-full py-5 bg-slate-900 hover:bg-black text-white font-black rounded-2xl transition shadow-xl shadow-slate-900/20 uppercase tracking-widest text-xs">
                                Understood
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Requests List -->
                @if($booking->guestRequests->count() > 0 || $booking->roomServiceOrders->count() > 0)
                    <div class="mt-8 pt-8 border-t border-slate-100 space-y-8">
                        {{-- GUEST REQUESTS --}}
                        @if($booking->guestRequests->count() > 0)
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Recent Service Requests</h5>
                                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">Live Status</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($booking->guestRequests->sortByDesc('created_at')->take(3) as $req)
                                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                            <div class="flex items-center gap-4">
                                                <div class="w-3 h-3 rounded-full {{ $req->status === 'completed' ? 'bg-emerald-500' : ($req->status === 'in_progress' ? 'bg-blue-500 animate-pulse' : 'bg-slate-300') }} ring-2 ring-white shadow-sm"></div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-900 uppercase tracking-wide">{{ ucfirst($req->type) }}</p>
                                                    <p class="text-[10px] text-slate-400 font-medium truncate max-w-[150px]">{{ $req->request ?: 'Standard Request' }}</p>
                                                </div>
                                            </div>
                                            <span class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-lg {{ $req->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                                {{ str_replace('_', ' ', $req->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- DINING ORDERS --}}
                        @if($booking->roomServiceOrders->count() > 0)
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Recent Dining Orders</h5>
                                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">Kitchen Live</span>
                                </div>
                                <div class="space-y-3">
                                    @foreach($booking->roomServiceOrders->sortByDesc('created_at')->take(3) as $order)
                                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                                            <div class="flex items-center gap-4">
                                                <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-rose-500 shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-slate-900">Order #{{ 5000 + $order->id }}</p>
                                                    <p class="text-[10px] text-slate-400 font-medium whitespace-nowrap overflow-hidden text-ellipsis max-w-[150px]">
                                                        @foreach($order->items as $item)
                                                            {{ $item['qty'] }}x {{ $item['name'] }}{{ !$loop->last ? ', ' : '' }}
                                                        @endforeach
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-[10px] font-black text-slate-900">₹{{ number_format($order->total_amount) }}</p>
                                                <span class="text-[8px] font-black uppercase tracking-tighter {{ $order->status === 'delivered' ? 'text-emerald-500' : 'text-amber-500' }}">
                                                    {{ $order->status }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: INFO & SUPPORT --}}
        <div class="lg:col-span-4 space-y-8">
            <div class="bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="text-xl font-black mb-6">Concierge Support</h4>
                    <p class="text-slate-400 text-sm font-medium mb-10 leading-relaxed">
                        Our team is available 24/7 to assist with your arrival or any special requests.
                    </p>
                    <div class="space-y-4">
                        <a href="tel:+919876543210" class="flex items-center gap-4 p-4 bg-white/10 rounded-2xl hover:bg-white/20 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="text-sm font-bold">+91 98765 43210</span>
                        </a>
                        <div class="flex items-center gap-4 p-4 bg-white/10 rounded-2xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span class="text-sm font-bold">123 Luxury Lane, Kerala</span>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-600 rounded-full blur-3xl opacity-20"></div>
            </div>

            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm p-10">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b border-slate-50 pb-6">Stay Policy</h4>
                <ul class="space-y-6">
                    <li class="flex gap-4">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1.5 shrink-0"></div>
                        <p class="text-xs font-medium text-slate-500 leading-relaxed">Check-in begins at 12:00 PM. Early check-in is subject to availability.</p>
                    </li>
                    <li class="flex gap-4">
                        <div class="w-1.5 h-1.5 rounded-full bg-blue-600 mt-1.5 shrink-0"></div>
                        <p class="text-xs font-medium text-slate-500 leading-relaxed">Official Govt. ID is mandatory for all guests upon arrival.</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
