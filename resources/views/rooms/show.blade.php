@extends('layouts.app')

@push('scripts')
    <!-- Flatpickr CSS -->`n
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        /* Custom styling for blocked dates */
        .flatpickr-disabled-custom {
            position: relative;
        }

        /* Default disabled (e.g. past dates) - Just muted */
        .flatpickr-day.flatpickr-disabled {
            background: transparent !important;
            border-color: transparent !important;
            color: #cbd5e1 !important;
            /* Slate-300 */
        }

        /* Specifically Blocked/Booked dates - Gray background + Strikethrough */
        .flatpickr-day.flatpickr-disabled.flatpickr-disabled-custom {
            background-color: #f8fafc !important;
            /* Slate-50 */
            color: #94a3b8 !important;
            /* Slate-400 */
            text-decoration: line-through;
            border: 1px dashed #e2e8f0 !important;
        }

        .flatpickr-day.flatpickr-disabled.flatpickr-disabled-custom:hover {
            background-color: #f1f5f9 !important;
            cursor: not-allowed;
        }

        /* Premium calendar styling */
        .flatpickr-calendar {
            border-radius: 1rem !important;
            box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
            border: 1px solid #e2e8f0 !important;
            padding: 1rem !important;
        }

        .flatpickr-day.selected {
            background: #2563eb !important;
            border-color: #2563eb !important;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);
        }

        .flatpickr-day:hover:not(.flatpickr-disabled) {
            background: #dbeafe !important;
            border-color: #93c5fd !important;
        }

        .flatpickr-months {
            margin-bottom: 1rem;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unavailableDates = @json($unavailableDates ?? []);

            const checkInInput = document.querySelector('input[name="check_in"]');
            const checkOutInput = document.querySelector('input[name="check_out"]');

            if (!checkInInput || !checkOutInput) return;

            // Check-in calendar
            const checkInPicker = flatpickr(checkInInput, {
                minDate: 'today',
                maxDate: new Date().fp_incr(180),
                dateFormat: 'Y-m-d',
                disable: unavailableDates,
                onChange: function (selectedDates) {
                    if (selectedDates.length > 0) {
                        const minCheckOut = new Date(selectedDates[0]);
                        minCheckOut.setDate(minCheckOut.getDate() + 1);
                        checkOutPicker.set('minDate', minCheckOut);
                    }
                },
                onDayCreate: function (dObj, dStr, fp, dayElem) {
                    const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                    if (unavailableDates.includes(dateStr)) {
                        dayElem.className += ' flatpickr-disabled-custom';
                        const indicator = document.createElement('span');
                        indicator.className = 'blocked-indicator';
                        dayElem.appendChild(indicator);
                    }
                }
            });

            // Check-out calendar
            const checkOutPicker = flatpickr(checkOutInput, {
                minDate: new Date().fp_incr(1),
                maxDate: new Date().fp_incr(180),
                dateFormat: 'Y-m-d',
                disable: unavailableDates,
                onDayCreate: function (dObj, dStr, fp, dayElem) {
                    const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                    if (unavailableDates.includes(dateStr)) {
                        dayElem.className += ' flatpickr-disabled-custom';
                        const indicator = document.createElement('span');
                        indicator.className = 'blocked-indicator';
                        dayElem.appendChild(indicator);
                    }
                }
            });
        });
    </script>

@endpush

@section('content')

    {{-- HERO GALLERY (Immersive) --}}
    <div class="relative h-[65vh] md:h-[80vh] bg-slate-900 overflow-hidden group">
        @php
            $mainImg = $roomType->image ? asset('storage/' . $roomType->image) : 'https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=2670&auto=format&fit=crop';
        @endphp
        <img src="{{ $mainImg }}"
            class="w-full h-full object-cover opacity-70 transition-transform duration-[20s] group-hover:scale-110 ease-linear">

        {{-- GLASS GRADIENT OVERLAY --}}
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/20 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/60 via-transparent to-transparent"></div>

        {{-- CONTENT --}}
        <div class="absolute bottom-0 left-0 w-full p-8 md:p-20">
            <div class="max-w-7xl mx-auto reveal">
                <div class="flex flex-wrap items-center gap-4 mb-8">
                    <a href="{{ route('rooms') }}"
                        class="px-4 py-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-white text-xs font-black uppercase tracking-widest hover:bg-white/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M15 19l-7-7 7-7" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                            </path>
                        </svg>
                        Back to Selection
                    </a>
                    <span
                        class="px-3 py-1 bg-blue-600/90 backdrop-blur-md rounded-full text-white text-[10px] font-black uppercase tracking-widest">
                        {{ $roomType->total_rooms }} Rooms Available
                    </span>
                </div>

                <h1 class="text-5xl md:text-8xl font-bold text-white mb-6 font-display tracking-tight leading-none">
                    {{ $roomType->name }}
                </h1>
                <div class="flex items-center gap-6 text-slate-300">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span class="text-sm font-bold uppercase tracking-widest">Base: {{ $roomType->base_occupancy }}
                            Guests</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- STICKY NAV --}}
    <div class="bg-white/80 backdrop-blur-xl border-b border-slate-100 sticky top-20 z-40 shadow-sm hidden md:block">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex gap-10">
                <a href="#overview"
                    class="text-xs font-black text-slate-900 border-b-2 border-blue-600 h-20 flex items-center tracking-widest uppercase">Overview</a>
                <a href="#amenities"
                    class="text-xs font-black text-slate-400 hover:text-slate-900 h-20 flex items-center transition tracking-widest uppercase">Amenities</a>
                <a href="#reviews"
                    class="text-xs font-black text-slate-400 hover:text-slate-900 h-20 flex items-center transition tracking-widest uppercase">Reviews</a>
            </div>
            <div class="flex items-center gap-8">
                <div class="flex flex-col items-end">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Starting from</span>
                    <span class="text-2xl font-black text-slate-900">₹{{ number_format($roomType->price, 0) }}<span
                            class="text-sm font-bold text-slate-400">/night</span></span>
                </div>
                @if($maxAvailable > 0)
                    <a href="#book-now"
                        class="px-8 py-3 bg-blue-600 text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-900 transition-all duration-500 shadow-xl shadow-blue-500/20">Book
                        This Room</a>
                @else
                    <button disabled
                        class="px-8 py-3 bg-slate-100 text-slate-400 rounded-2xl text-xs font-black uppercase tracking-widest cursor-not-allowed">Sold
                        Out</button>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-20">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">

            {{-- LEFT CONTENT --}}
            <div class="lg:col-span-2 space-y-20">

                {{-- OVERVIEW --}}
                <div id="overview" class="reveal">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-1 bg-blue-600 rounded-full"></div>
                        <span class="text-xs font-black text-blue-600 uppercase tracking-widest">Sovereign Experience</span>
                    </div>
                    <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-8 font-display leading-tight">Architecture
                        of Pure Comfort</h2>
                    <div class="prose prose-slate prose-xl text-slate-500 max-w-none">
                        {{ $roomType->description ?? 'Experience luxury redefined in our meticulously crafted rooms.' }}
                    </div>
                </div>

                {{-- GALLERY GRID (If exists) --}}
                @if($roomType->gallery_json && count($roomType->gallery_json) > 0)
                    <div class="grid grid-cols-2 gap-4 reveal">
                        @foreach($roomType->gallery_json as $index => $img)
                            <div
                                class="rounded-[2rem] overflow-hidden {{ $index === 0 ? 'col-span-2 aspect-[16/9]' : 'aspect-square' }} relative group">
                                <img src="{{ asset('storage/' . $img) }}"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                                <div class="absolute inset-0 bg-slate-900/10 group-hover:bg-transparent transition-colors"></div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- AMENITIES --}}
                <div id="amenities" class="p-10 bg-slate-50 rounded-[3rem] reveal reveal-delay-100 border border-slate-100">
                    <h3 class="text-2xl font-black text-slate-900 mb-10 font-display uppercase tracking-tight">Included
                        Luxuries</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                        @foreach($roomType->amenities ?? [] as $amenity)
                            @php
                                $icon = match (strtolower($amenity)) {
                                    'free wifi', 'wifi' => '<path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"></path>',
                                    'air conditioning', 'a/c' => '<path d="M4 14h16M4 18h16M4 10h16M4 6h16"></path>',
                                    'heating' => '<path d="M12 22V12M12 12L8 16M12 12l4 4M20 4a2 2 0 0 0-2 2v6a2 2 0 0 0 4 0V6a2 2 0 0 0-2-2z"></path>',
                                    'soundproofing' => '<path d="M3 10v4M7 8v8M11 6v12M15 8v8M19 10v4"></path>',
                                    'desk' => '<path d="M4 4h16v12H4zM4 16l-2 4M20 16l2 4M8 16v4M16 16v4"></path>',
                                    'safe' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>',
                                    'ironing facilities' => '<path d="M7 18h10M5 15h14M12 5v10"></path>',
                                    'tv', 'smart tv' => '<path d="M2 8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8zM7 21h10M12 18v3"></path>',
                                    'satellite channels', 'netflix access' => '<path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12zM22 12h-4M2 12h4M12 2v4M12 18v4"></path>',
                                    'bluetooth speakers' => '<path d="M5 12.55a11 11 0 0 1 14.08 0M12 20h.01"></path>',
                                    'minibar' => '<path d="M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2zM4 9h16M10 14h4M10 17h4"></path>',
                                    'coffee machine', 'tea maker' => '<path d="M6 2h12v12H6zm0 12v6a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-6M8 6h8M8 10h8"></path>',
                                    'bottled water' => '<path d="M9 3h6v4H9zm1 4v14h4V7z"></path>',
                                    'kitchenette', 'dining area' => '<path d="M3 11a5 5 0 0 1 5-5h8a5 5 0 0 1 5 5v10H3V11z"></path>',
                                    'balcony', 'terrace', 'patio' => '<path d="M3 21h18M3 10h18M7 10v11M17 10v11M12 10v11"></path>',
                                    'private pool', 'pool view' => '<path d="M2 12c4.33-2 8.67-2 13 0s8.67 2 13 0M2 17c4.33-2 8.67-2 13 0s8.67 2 13 0"></path>',
                                    'jacuzzi', 'hot tub', 'sauna access' => '<path d="M4 12c0-4.4 3.6-8 8-8s8 3.6 8 8M12 12v9M8 17h8"></path>',
                                    'bathrobes', 'slippers', 'premium toiletries', 'pillow menu' => '<path d="M20 7h-9L8 10l-1-1V5a2 2 0 0 1 2-2H20a2 2 0 0 1 2 2v15a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9"></path>',
                                    'room service' => '<path d="M2 18h20M5 18l1.5-4.5M19 18l-1.5-4.5M12 12V4M12 4L9 7M12 4l3 3"></path>',
                                    'wake-up service', 'daily housekeeping' => '<path d="M12 3v3M12 18v3M4.22 4.22l2.12 2.12M17.66 17.66l2.12 2.12M3 12h3M18 12h3M4.22 19.78l2.12-2.12M17.66 6.34l2.12-2.12"></path>',
                                    'laundry service', 'butler service', 'concierge access' => '<path d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18zM12 7v5l3 3"></path>',
                                    'city view' => '<path d="M3 21h18M5 21V7l8-4v18M13 21V11l8-4v14M9 9v0M9 12v0M9 15v0M17 13v0M17 16v0"></path>',
                                    'ocean view' => '<path d="M2 12h20M2 12c0-3 3-6 10-6s10 3 10 6M12 18s-4-4-4-7 4-7 4-7 4 4 4 7-4 7-4 7z"></path>',
                                    'garden view' => '<path d="M12 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM2 22l10-4 10 4"></path>',
                                    'mountain view' => '<path d="M2 20L10 4l8 16M14 20l4-8 4 8H2z"></path>',
                                    default => '<path d="M5 13l4 4L19 7"></path>'
                                };
                            @endphp
                            <div class="flex flex-col gap-4">
                                <div
                                    class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-blue-600 shadow-sm border border-slate-100">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5"
                                        viewBox="0 0 24 24">
                                        {!! $icon !!}
                                    </svg>
                                </div>
                                <span
                                    class="text-[10px] font-black text-slate-900 uppercase tracking-widest leading-tight">{{ $amenity }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- REVIEWS (Simplified/Luxury) --}}
                <div id="reviews" class="reveal reveal-delay-200">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-2xl font-black text-slate-900 font-display uppercase tracking-tight">Guest
                            Chronicles</h3>
                        <div class="flex gap-1 text-amber-500 text-sm">★★★★★</div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative">
                            <span
                                class="absolute top-6 right-8 text-6xl text-slate-100 font-serif overflow-hidden h-10 select-none">“</span>
                            <p class="text-slate-600 font-medium mb-6 relative z-10">"The redefined hospitality here is
                                unmatched. Every corner of the room spoke of pure luxury."</p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400">
                                    SJ</div>
                                <span class="text-xs font-black text-slate-900 uppercase tracking-widest">Sarah
                                    Jenkins</span>
                            </div>
                        </div>
                        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm relative">
                            <span
                                class="absolute top-6 right-8 text-6xl text-slate-100 font-serif overflow-hidden h-10 select-none">“</span>
                            <p class="text-slate-600 font-medium mb-6 relative z-10">"Waking up to that view was the
                                highlight of our stay. Impeccable service."</p>
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-[10px] font-black text-slate-400">
                                    DC</div>
                                <span class="text-xs font-black text-slate-900 uppercase tracking-widest">David Chen</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT SIDEBAR (Titanium Booking Widget) --}}
            <div class="lg:col-span-1">
                <div class="sticky top-40 bg-white rounded-[2.5rem] shadow-[0_40px_80px_-20px_rgba(0,0,0,0.1)] p-8 border border-slate-100 reveal"
                    id="book-now">
                    <div class="flex items-center justify-between mb-8 pb-8 border-b border-slate-50">
                        <div>
                            <span class="text-4xl font-black text-slate-900">₹{{ number_format($totalPrice, 0) }}</span>
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">/ stay</span>
                        </div>
                        @if($maxAvailable <= 0)
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-slate-50 text-slate-400 rounded-full text-[10px] font-black uppercase tracking-widest border border-slate-100">
                                Sold Out
                            </div>
                        @elseif($maxAvailable <= 3)
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-100">
                                Only {{ $maxAvailable }} Left
                            </div>
                        @else
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">
                                Available
                            </div>
                        @endif
                    </div>

                    <div id="calendar-wrapper">
                        <form action="{{ route('checkout.show') }}" method="GET" class="space-y-6">
                            <input type="hidden" name="room_type_id" value="{{ $roomType->id }}">

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Arrival</label>
                                    <input type="text" name="check_in" x-ref="checkIn" value="{{ request('check_in') }}"
                                        placeholder="Select date"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none transition-all"
                                        required>
                                </div>
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Departure</label>
                                    <input type="text" name="check_out" x-ref="checkOut" value="{{ request('check_out') }}"
                                        placeholder="Select date"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none transition-all"
                                        required>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Attendance</label>
                                <div class="relative">
                                    <select name="rooms"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-2 focus:ring-blue-600 focus:bg-white outline-none appearance-none transition-all">
                                        <option value="1" {{ request('rooms') == 1 ? 'selected' : '' }}>1 Room, 2 Guests
                                        </option>
                                        <option value="2" {{ request('rooms') == 2 ? 'selected' : '' }}>2 Rooms, 4 Guests
                                        </option>
                                    </select>
                                    <div
                                        class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            @if($maxAvailable > 0)
                                <button type="submit"
                                    class="w-full py-5 bg-blue-600 text-white rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-blue-500/30 hover:bg-slate-900 transition-all duration-500 transform hover:-translate-y-1">
                                    Initialize Booking
                                </button>
                            @else
                                <button type="button" disabled
                                    class="w-full py-5 bg-slate-100 text-slate-400 rounded-2xl text-xs font-black uppercase tracking-[0.2em] cursor-not-allowed">
                                    Not Available
                                </button>
                                <p class="text-[9px] text-center text-red-500 font-bold uppercase tracking-widest mt-4">
                                    This suite is sold out for the selected dates.
                                </p>
                            @endif
                        </form>

                        <div class="mt-8 pt-8 border-t border-slate-50 flex flex-col items-center gap-3">
                            <div class="flex items-center gap-2">
                                <div class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-600"></span>
                                </div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Secure
                                    Reservation
                                    Link</span>
                            </div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest opacity-50">Free
                                cancellation protection active</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- RELATED ROOMS --}}
        @if($relatedRooms->count() > 0)
            <section class="py-24 bg-slate-50 border-t border-slate-200">
                <div class="max-w-7xl mx-auto px-6">
                    <h2 class="text-3xl font-bold text-slate-900 mb-12 text-center font-display">You May Also Like</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @foreach($relatedRooms as $item)
                            <div class="titanium-card overflow-hidden group flex flex-col transition hover:-translate-y-1">
                                <div class="h-48 bg-slate-200 relative overflow-hidden">
                                    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=800&auto=format&fit=crop"
                                        class="w-full h-full object-cover">
                                </div>
                                <div class="p-6">
                                    <h3 class="font-bold text-slate-900 mb-2">{{ $item->name }}</h3>
                                    <p class="text-sm text-slate-500 mb-4">{{ Str::limit($item->description, 60) }}</p>
                                    <a href="{{ route('rooms.show', $item->id) }}"
                                        class="text-blue-600 font-bold text-sm hover:underline">View Details &rarr;</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

@endsection
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>