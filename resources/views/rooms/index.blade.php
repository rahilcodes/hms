@extends('layouts.app')

@section('content')
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .animate-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        .fade-in {
            animation: fadeIn 1s ease-out forwards;
        }

        .slide-in-from-bottom-8 {
            animation: slideUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

    <div x-data="{ 
            cart: [],
            showCart: false,
            checkIn: '{{ $checkIn }}',
            checkOut: '{{ $checkOut }}',

            addToStay(room) {
                let existing = this.cart.find(i => i.id === room.id);
                if (existing) {
                    if (existing.qty < room.max_available) existing.qty++;
                    else window.showToast('Maximum available reached', 'error');
                } else {
                    this.cart.push({
                        id: room.id,
                        name: room.name,
                        price: room.price,
                        qty: 1,
                        max: room.max_available,
                        image: room.image
                    });
                    window.showToast(room.name + ' added to your stay');
                }
            },

            removeItem(id) {
                this.cart = this.cart.filter(i => i.id !== id);
            },

            get total() {
                return this.cart.reduce((sum, i) => sum + (i.price * i.qty), 0);
            },

            get checkoutUrl() {
                let params = new URLSearchParams();
                params.append('check_in', this.checkIn);
                params.append('check_out', this.checkOut);
                this.cart.forEach(i => {
                    params.append('rooms[' + i.id + ']', i.qty);
                });
                return '{{ route('checkout.show') }}?' + params.toString();
            }
        }" @keyup.escape="showCart = false" class="relative">

        {{-- IMMERSIVE HERO SECTION --}}
        <div class="relative min-h-[50vh] flex items-center bg-slate-900 overflow-hidden">
            {{-- Background Pattern/Image --}}
            <div class="absolute inset-0 opacity-40">
                <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=2670&auto=format&fit=crop"
                    class="w-full h-full object-cover grayscale">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/80 via-slate-900/40 to-slate-900"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-6 py-24 w-full">
                <div class="flex flex-col items-start gap-4 animate-in fade-in slide-in-from-bottom-8 duration-1000">
                    <span
                        class="px-4 py-1.5 bg-blue-600/20 text-blue-400 rounded-full text-[10px] font-black uppercase tracking-[0.3em] border border-blue-500/30 backdrop-blur-md">
                        The Luxe Collection
                    </span>
                    <h1 class="text-white text-5xl md:text-8xl font-black tracking-tighter leading-none font-display">
                        Signature <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-300">Stays</span>
                    </h1>
                    <p class="text-slate-400 text-lg md:text-xl font-medium max-w-2xl leading-relaxed mt-4">
                        Explore our curated collection of world-class accommodations, where architectural excellence meets
                        unmatched hospitality.
                    </p>
                </div>
            </div>
        </div>

        {{-- ULTRA-SLIM WORLD-CLASS SEARCH PILL --}}
        <div class="sticky top-[80px] z-30 px-6 -mt-8">
            <div class="max-w-4xl mx-auto">
                <div
                    class="bg-white/90 backdrop-blur-3xl rounded-full shadow-[0_20px_50px_-12px_rgba(0,0,0,0.15)] border border-white ring-1 ring-slate-900/5 p-1.5 transition-all duration-700 hover:shadow-[0_30px_60px_-12px_rgba(0,0,0,0.2)]">
                    <form action="{{ route('rooms') }}" method="GET" class="flex items-center gap-0">

                        {{-- DATES & ROOMS --}}
                        <div class="flex-1 grid grid-cols-3 w-full">

                            {{-- CHECK-IN --}}
                            <div class="relative px-6 py-2.5 hover:bg-slate-50 transition-colors duration-300 rounded-l-full cursor-pointer border-r border-slate-100/60"
                                onclick="document.getElementById('check_in').showPicker()">
                                <label
                                    class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5 opacity-70">Check-In</label>
                                <input type="date" name="check_in" id="check_in" x-model="checkIn"
                                    min="{{ now()->toDateString() }}"
                                    class="w-full bg-transparent border-none p-0 text-[11px] font-black focus:ring-0 outline-none cursor-pointer text-slate-900 uppercase"
                                    required>
                            </div>

                            {{-- CHECK-OUT --}}
                            <div class="relative px-6 py-2.5 hover:bg-slate-50 transition-colors duration-300 cursor-pointer border-r border-slate-100/60"
                                onclick="document.getElementById('check_out').showPicker()">
                                <label
                                    class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5 opacity-70">Check-Out</label>
                                <input type="date" name="check_out" id="check_out" x-model="checkOut"
                                    class="w-full bg-transparent border-none p-0 text-[11px] font-black focus:ring-0 outline-none cursor-pointer text-slate-900 uppercase"
                                    required>
                            </div>

                            {{-- ROOMS --}}
                            <div
                                class="relative px-6 py-2.5 hover:bg-slate-50 transition-colors duration-300 cursor-pointer group/input">
                                <label
                                    class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mb-0.5 opacity-70">Quick
                                    Search</label>
                                <div class="text-[11px] font-black text-slate-900 uppercase">Apply Filters</div>
                            </div>

                        </div>

                        {{-- SLIM CTA --}}
                        <div class="px-1">
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white rounded-full px-8 py-3.5 text-[10px] font-black uppercase tracking-widest transition-all shadow-lg shadow-blue-500/20 active:scale-95 group/btn flex items-center gap-2">
                                <span>Refresh</span>
                                <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-x-0.5" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                            </button>
                        </div>

                    </form>
                </div>

                {{-- SEARCH CONTEXT --}}
                <div class="mt-8 flex items-center justify-between px-6">
                    <div class="flex items-center gap-4">
                        <span
                            class="flex items-center gap-2 text-[10px] font-black text-slate-400 uppercase tracking-widest px-3 py-1 bg-white border border-slate-100 rounded-full shadow-sm">
                            <div
                                class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_rgba(16,185,129,0.5)]">
                            </div>
                            {{ count($results) }} Selection{{ count($results) != 1 ? 's' : '' }} Found
                        </span>
                        @if($checkIn && $checkOut)
                            <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest italic">
                                Prices for {{ \Carbon\Carbon::parse($checkIn)->diffInDays($checkOut) }} Nights
                            </span>
                        @endif
                    </div>

                    {{-- CART TRIGGER --}}
                    <div x-show="cart.length > 0" x-cloak x-transition>
                        <button @click="showCart = true"
                            class="flex items-center gap-3 bg-slate-900 text-white pl-4 pr-1.5 py-1.5 rounded-full shadow-xl hover:bg-blue-600 transition group">
                            <span class="text-[10px] font-black uppercase tracking-widest">My Stay</span>
                            <div
                                class="bg-white/20 px-3 py-1.5 rounded-full text-[10px] font-black group-hover:bg-white group-hover:text-blue-600">
                                <span x-text="cart.length"></span> Items
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ROOMS GRID --}}
        <div class="max-w-7xl mx-auto px-6 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">

                @forelse($results as $item)
                    @php
                        $room = $item['roomType'];
                        $available = $item['max_available'];
                        $price = $item['total_price'];
                    @endphp

                    <div
                        class="group flex flex-col bg-white rounded-[2.5rem] overflow-hidden border border-slate-100 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.1)] transition-all duration-700 hover:-translate-y-2">

                        {{-- IMAGE GALLERY STYLE --}}
                        <div class="relative h-96 overflow-hidden">
                            <a href="{{ route('rooms.show', $room->id) }}" class="absolute inset-0 z-10"></a>

                            <img src="{{ $room->image_url }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110 {{ $available <= 0 ? 'grayscale' : '' }}">

                            {{-- OVERLAY --}}
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-60">
                            </div>

                            {{-- TOP BADGES --}}
                            <div class="absolute top-6 left-6 right-6 flex justify-between items-start z-20">
                                @if($available <= 0)
                                    <span
                                        class="bg-slate-500/90 backdrop-blur-xl text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg">
                                        Sold Out
                                    </span>
                                @elseif($available <= 3)
                                    <span
                                        class="bg-red-500/90 backdrop-blur-xl text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg">
                                        Only {{ $available }} Left
                                    </span>
                                @else
                                    <span
                                        class="bg-emerald-500/90 backdrop-blur-xl text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-lg">
                                        Available
                                    </span>
                                @endif

                                @if($price > 0)
                                    <div
                                        class="bg-white/95 backdrop-blur-xl px-4 py-2 rounded-2xl shadow-xl flex flex-col items-end">
                                        <span
                                            class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">
                                            @if($checkIn && $checkOut) Total Stay @else Nightly From @endif
                                        </span>
                                        <span class="text-lg font-black text-slate-900 leading-none tracking-tighter">
                                            ₹{{ number_format($price, 0) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- ROOM INFO OVERLAY --}}
                            <div class="absolute bottom-8 left-8 right-8 z-20">
                                <h3
                                    class="text-3xl font-black text-white tracking-tighter mb-2 group-hover:text-blue-400 transition-colors">
                                    {{ $room->name }}
                                </h3>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="text-[9px] font-black text-white/80 uppercase tracking-widest flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        {{ $room->square_feet ?? '450' }} SQ FT
                                    </span>
                                    <div class="w-1 h-1 bg-white/40 rounded-full"></div>
                                    <span
                                        class="text-[9px] font-black text-white/80 uppercase tracking-widest flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path
                                                d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m12-9a4 4 0 11-8 0 4 4 0 018 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 7h.01m4 4h.01">
                                            </path>
                                        </svg>
                                        Up to {{ $room->base_occupancy }} Guests
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- CONTENT --}}
                        <div class="p-8 flex-1 flex flex-col">

                            <p class="text-slate-500 text-sm font-medium leading-relaxed mb-8 line-clamp-2">
                                {{ $room->description ?? 'Experience luxury redefined. A perfect blend of comfort and style for the discerning traveler.' }}
                            </p>

                            {{-- TITANIUM AMENITIES GRID --}}
                            <div class="grid grid-cols-4 gap-4 mb-10">
                                @foreach(array_slice($room->amenities ?? ['WiFi', 'AC', 'TV', 'Minibar'], 0, 4) as $amenity)
                                    @php
                                        $icon = match (strtolower($amenity)) {
                                            'free wifi', 'wifi' => '<path d="M5 12.55a11 11 0 0 1 14.08 0M1.42 9a16 16 0 0 1 21.16 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"></path>',
                                            'air conditioning', 'a/c' => '<path d="M4 14h16M4 18h16M4 10h16M4 6h16"></path>',
                                            'tv', 'smart tv' => '<path d="M2 8a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8zM7 21h10M12 18v3"></path>',
                                            'minibar' => '<path d="M6 3h12a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V5a2 2 0 0 1-2-2zM4 9h16M10 14h4M10 17h4"></path>',
                                            default => '<path d="M5 13l4 4L19 7"></path>'
                                        };
                                    @endphp
                                    <div class="flex flex-col items-center gap-2 group/icon">
                                        <div
                                            class="w-10 h-10 bg-slate-50 border border-slate-100 rounded-xl flex items-center justify-center text-slate-400 group-hover/icon:text-blue-600 group-hover/icon:bg-blue-50 transition-all duration-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                                viewBox="0 0 24 24">
                                                {!! $icon !!}
                                            </svg>
                                        </div>
                                        <span
                                            class="text-[8px] font-black text-slate-400 uppercase tracking-widest text-center">{{ $amenity }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- CTAs --}}
                            <div class="mt-auto">
                                @if($available > 0)
                                    <button @click="addToStay({
                                                            id: '{{ $room->id }}',
                                                            name: '{{ $room->name }}',
                                                            price: {{ $price }},
                                                            max_available: {{ $available }},
                                                            image: '{{ $room->image_url }}'
                                                        })"
                                        class="w-full flex items-center justify-center py-4 rounded-2xl bg-slate-900 border-2 border-slate-900 text-xs font-black uppercase tracking-widest text-white hover:bg-blue-600 hover:border-blue-600 transition-all shadow-lg hover:shadow-blue-500/30 gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add to Stay
                                    </button>
                                @else
                                    <button disabled
                                        class="w-full flex items-center justify-center py-4 rounded-2xl bg-slate-100 border-2 border-slate-100 text-xs font-black uppercase tracking-widest text-slate-400 cursor-not-allowed">
                                        Sold Out
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                @empty
                    <div
                        class="col-span-full py-40 text-center bg-slate-50 rounded-[4rem] border-2 border-dashed border-slate-200">
                        <div
                            class="w-24 h-24 bg-white border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-sm">
                            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mb-4 font-display">No Suites Available</h3>
                        <p class="text-slate-400 font-medium max-w-sm mx-auto mb-10">
                            Adjust your travel dates or occupancy requirements to explore our collection.
                        </p>
                        <a href="{{ route('rooms') }}"
                            class="text-blue-600 font-black text-xs uppercase tracking-widest hover:text-blue-700 transition">Reset
                            All Filters</a>
                    </div>
                @endforelse

            </div>
        </div>

        {{-- CARVED DRAWER - STAY PLANNER --}}
        <div x-show="showCart" x-cloak class="fixed inset-0 z-[100] overflow-hidden">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="showCart = false">
            </div>
            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10 md:pl-0">
                <div x-show="showCart" x-transition:enter="transform transition ease-out duration-500"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-500" x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full" class="w-screen max-w-md">
                    <div
                        class="h-full flex flex-col bg-white shadow-2xl overflow-y-scroll no-scrollbar rounded-l-[3rem] border-l border-white/20">
                        <div class="px-8 py-10 bg-slate-900 text-white">
                            <div class="flex items-center justify-between mb-8">
                                <h2 class="text-3xl font-black tracking-tighter">Stay Planner</h2>
                                <button @click="showCart = false" class="p-2 hover:bg-white/10 rounded-full transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center gap-4 text-slate-400">
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black uppercase tracking-widest">Arrival</span>
                                    <span class="text-xs font-bold text-white uppercase"
                                        x-text="new Date(checkIn).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' })"></span>
                                </div>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                </svg>
                                <div class="flex flex-col">
                                    <span class="text-[8px] font-black uppercase tracking-widest">Departure</span>
                                    <span class="text-xs font-bold text-white uppercase"
                                        x-text="new Date(checkOut).toLocaleDateString('en-GB', { day: '2-digit', month: 'short' })"></span>
                                </div>
                            </div>
                        </div>

                        <div class="flex-1 py-8 px-8 space-y-6">
                            <template x-for="item in cart" :key="item.id">
                                <div
                                    class="group relative flex items-center gap-6 p-4 bg-slate-50 border border-slate-100 rounded-3xl transition hover:border-blue-200">
                                    <div class="w-20 h-20 bg-white rounded-2xl overflow-hidden shadow-sm flex-shrink-0">
                                        <img :src="item.image" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-black text-slate-900 truncate uppercase tracking-tight"
                                            x-text="item.name"></h4>
                                        <p class="text-[10px] font-bold text-slate-400 mt-1">₹<span
                                                x-text="item.price.toLocaleString()"></span> / Stay</p>

                                        <div class="mt-3 flex items-center gap-4">
                                            <div
                                                class="flex items-center bg-white border border-slate-200 rounded-full px-2 py-0.5">
                                                <button @click="if(item.qty > 1) item.qty--"
                                                    class="p-1 hover:text-blue-600 transition disabled:opacity-30"
                                                    :disabled="item.qty <= 1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M20 12H4"></path>
                                                    </svg>
                                                </button>
                                                <span class="text-[10px] font-black w-6 text-center"
                                                    x-text="item.qty"></span>
                                                <button @click="if(item.qty < item.max) item.qty++"
                                                    class="p-1 hover:text-blue-600 transition disabled:opacity-30"
                                                    :disabled="item.qty >= item.max">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="3" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button @click="removeItem(item.id)"
                                        class="text-slate-300 hover:text-rose-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <div x-show="cart.length === 0" class="py-20 text-center">
                                <div
                                    class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <h4 class="text-lg font-bold text-slate-900">Your stay is empty</h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Select a
                                    suite to begin</p>
                            </div>
                        </div>

                        <div class="p-8 bg-slate-50 border-t border-slate-100 rounded-t-[3rem]">
                            <div class="flex items-center justify-between mb-8">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total
                                        Valuation</p>
                                    <p class="text-3xl font-black text-slate-900 tracking-tighter">₹<span
                                            x-text="total.toLocaleString()"></span></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] font-bold text-blue-600 italic">Taxes Included</p>
                                </div>
                            </div>
                            <a :href="checkoutUrl"
                                class="w-full flex items-center justify-center py-5 bg-blue-600 rounded-[1.5rem] text-xs font-black uppercase tracking-[0.2em] text-white shadow-2xl shadow-blue-500/30 hover:bg-blue-700 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="cart.length === 0 ? 'pointer-events-none grayscale opacity-50' : ''">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkIn = document.getElementById('check_in');
            const checkOut = document.getElementById('check_out');

            function updateCheckoutMin() {
                if (!checkIn.value) return;
                const d = new Date(checkIn.value);
                d.setDate(d.getDate() + 1);
                const min = d.toISOString().split('T')[0];
                checkOut.min = min;
                if (checkOut.value && checkOut.value < min) checkOut.value = min;
            }

            if (checkIn && checkOut) {
                checkIn.addEventListener('change', updateCheckoutMin);
                updateCheckoutMin();
            }
        });
    </script>
@endsection