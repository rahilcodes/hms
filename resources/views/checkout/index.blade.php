@extends('layouts.app')

@section('content')
    <div class="bg-slate-50 min-h-screen py-10" x-data="{ 
                        baseTotal: {{ $total }},
                        checkIn: '{{ $check_in }}',
                        checkOut: '{{ $check_out }}',
                        get nights() {
                            return Math.ceil((new Date(this.checkOut) - new Date(this.checkIn)) / (1000 * 60 * 60 * 24));
                        },
                        extraPersons: {
                            @foreach($selectedRooms as $item)
                                '{{ $item['room']->id }}': 0,
                            @endforeach
                        },
                        roomPrices: {
                            @foreach($selectedRooms as $item)
                                '{{ $item['room']->id }}': {{ $item['extra_person_price'] }},
                            @endforeach
                        },
                        get extraPersonTotal() {
                            let total = 0;
                            for (let id in this.extraPersons) {
                                total += (this.extraPersons[id] * this.roomPrices[id] * this.nights);
                            }
                            return total;
                        },
                        selectedUpsells: [], // Format: { service_id, room_type_id, qty, ...service_data }
                        get upsellTotal() {
                            return this.selectedUpsells.reduce((acc, item) => {
                                let cost = parseFloat(item.price) * (item.qty || 1);
                                if (item.price_unit === 'per_night') cost *= this.nights;
                                return acc + cost;
                            }, 0);
                        },
                        discountAmount: 0,
                        couponId: null,
                        get finalTotal() {
                            return Math.max(0, this.baseTotal + this.extraPersonTotal + this.upsellTotal - this.discountAmount);
                        },
                        paymentEnabled: {{ $paymentSettings['enabled'] ? 'true' : 'false' }},
                        paymentMode: '{{ $paymentSettings['mode'] }}',
                        depositType: '{{ $paymentSettings['deposit_type'] }}',
                        depositValue: {{ $paymentSettings['deposit_value'] }},
                        get amountToPay() {
                            if (!this.paymentEnabled || this.paymentMode === 'hotel_only') return 0;
                            if (this.paymentMode === 'online_only') return this.finalTotal;
                            if (this.paymentMode === 'partial_deposit') {
                                if (this.depositType === 'percentage') {
                                    return (this.finalTotal * this.depositValue) / 100;
                                }
                                return Math.min(this.depositValue, this.finalTotal);
                            }
                            return 0;
                        },
                        toggleUpsell(service, roomTypeId) {
                            const index = this.selectedUpsells.findIndex(u => u.id === service.id && u.room_type_id === roomTypeId);
                            if (index > -1) {
                                this.selectedUpsells.splice(index, 1);
                            } else {
                                this.selectedUpsells.push({ ...service, room_type_id: roomTypeId, qty: 1 });
                            }
                        },
                        isUpsellSelected(serviceId, roomTypeId) {
                            return this.selectedUpsells.some(u => u.id === serviceId && u.room_type_id === roomTypeId);
                        },
                        getUpsellQty(serviceId, roomTypeId) {
                            const item = this.selectedUpsells.find(u => u.id === serviceId && u.room_type_id === roomTypeId);
                            return item ? (item.qty || 1) : 0;
                        },
                        updateUpsellQty(serviceId, roomTypeId, delta) {
                            const index = this.selectedUpsells.findIndex(u => u.id === serviceId && u.room_type_id === roomTypeId);
                            if (index > -1) {
                                const newQty = (this.selectedUpsells[index].qty || 1) + delta;
                                if (newQty <= 0) {
                                    this.selectedUpsells.splice(index, 1);
                                } else {
                                    this.selectedUpsells[index].qty = newQty;
                                }
                            }
                        }
                     }">
        <div class="max-w-6xl mx-auto px-6">

            <div class="flex items-center gap-4 mb-8">
                <a href="{{ url()->previous() }}"
                    class="w-10 h-10 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-500 hover:text-blue-600 hover:border-blue-200 transition shadow-sm">
                    &larr;
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Complete Your Booking</h1>
                    <p class="text-sm text-slate-500">Secure checkout for your stay</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT COLUMN: FORM --}}
                <div class="lg:col-span-2 space-y-6">

                    <form method="POST" action="{{ route('checkout.store') }}" id="checkout-form" class="space-y-6">
                        @csrf
                        <input type="hidden" name="check_in" value="{{ $check_in }}">
                        <input type="hidden" name="check_out" value="{{ $check_out }}">
                        <input type="hidden" name="services_json" :value="JSON.stringify(selectedUpsells)">
                        <input type="hidden" name="discount_amount" :value="discountAmount">
                        <input type="hidden" name="coupon_id" :value="couponId">

                        @foreach($rooms as $roomTypeId => $qty)
                            <input type="hidden" name="rooms[{{ $roomTypeId }}]" value="{{ $qty }}">
                        @endforeach

                        {{-- SECT 1: GUEST & STAY CONFIG --}}
                        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden reveal">
                            <div class="px-8 py-6 bg-slate-900 text-white">
                                <h2 class="text-xl font-black tracking-tight flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center text-sm">1
                                    </div>
                                    Reservation details
                                </h2>
                            </div>

                            <div class="p-8 space-y-8">
                                {{-- Primary Guest --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Primary
                                            Guest Name</label>
                                        <input type="text" name="name" placeholder="John Doe" required
                                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 font-bold outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition text-slate-900 text-sm shadow-inner">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">WhatsApp
                                            / Phone</label>
                                        <input type="tel" name="phone" placeholder="+91 98765 43210" required
                                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 font-bold outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 focus:bg-white transition text-slate-900 text-sm shadow-inner">
                                    </div>
                                </div>

                                {{-- Per-Room Configuration --}}
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Room Personalization</p>
                                        <a href="{{ route('checkout.add-room', request()->all()) }}" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:text-blue-700 flex items-center gap-1 group">
                                            <svg class="w-3 h-3 group-hover:scale-110 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            Add Another Room
                                        </a>
                                    </div>

                                    @foreach($selectedRooms as $item)
                                        <div class="p-6 bg-slate-50 border border-slate-100 rounded-[2rem] space-y-4"
                                            x-data="{ openAddons: false }">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center border border-slate-100 shadow-sm">
                                                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                            </path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">
                                                            {{ $item['qty'] }}x {{ $item['room']->name }}
                                                        </h4>
                                                        <p
                                                            class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                                            Base Stay: ₹{{ number_format($item['total']) }}</p>
                                                    </div>
                                                </div>

                                                <button type="button" @click="openAddons = !openAddons"
                                                    class="flex items-center gap-2 px-4 py-2 rounded-full border border-slate-200 text-[10px] font-black uppercase tracking-widest transition"
                                                    :class="openAddons ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white text-slate-500 hover:border-blue-300 hover:text-blue-600'">
                                                    <span x-text="openAddons ? 'Close' : 'Add Extras'"></span>
                                                </button>
                                            </div>

                                            {{-- Extras for this room category --}}
                                            <div x-show="openAddons" x-transition
                                                class="pt-4 border-t border-slate-200 space-y-4">
                                                {{-- Extra Persons --}}
                                                @if($item['max_extra_persons'] > 0)
                                                    <div class="flex items-center justify-between bg-white p-3 rounded-2xl border border-slate-100">
                                                        <div>
                                                            <p class="text-[11px] font-black text-slate-900 uppercase">Extra Persons</p>
                                                            <p class="text-[9px] font-bold text-slate-400">
                                                                ₹{{ number_format($item['extra_person_price']) }} / night / guest
                                                            </p>
                                                        </div>
                                                        
                                                        <div class="flex items-center gap-3 bg-slate-50 rounded-xl p-1 border border-slate-200" x-data="{ max: {{ $item['max_extra_persons'] }} }">
                                                            {{-- Hidden Input for Form Submission --}}
                                                            <input type="hidden" name="extra_persons[{{ $item['room']->id }}]" x-model="extraPersons['{{ $item['room']->id }}']">
                                                            
                                                            <button type="button" 
                                                                    @click="if(extraPersons['{{ $item['room']->id }}'] > 0) extraPersons['{{ $item['room']->id }}']--"
                                                                    class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition disabled:opacity-50"
                                                                    :disabled="extraPersons['{{ $item['room']->id }}'] <= 0">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg>
                                                            </button>
                                                            
                                                            <span class="text-xs font-black text-slate-900 min-w-[3rem] px-2 text-center whitespace-nowrap" x-text="extraPersons['{{ $item['room']->id }}'] + ' Psn'"></span>
                                                            
                                                            <button type="button" 
                                                                    @click="if(extraPersons['{{ $item['room']->id }}'] < max) extraPersons['{{ $item['room']->id }}']++"
                                                                    class="w-7 h-7 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition disabled:opacity-50"
                                                                    :disabled="extraPersons['{{ $item['room']->id }}'] >= max">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"></path></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Upsells for this room --}}
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    @foreach($upsells as $service)
                                                        <div class="p-3 bg-white border border-slate-100 rounded-2xl flex flex-col justify-between transition shadow-sm relative overflow-hidden group"
                                                            :class="isUpsellSelected({{ $service->id }}, '{{ $item['room']->id }}') ? 'border-blue-500 ring-1 ring-blue-500 bg-blue-50' : 'hover:border-blue-300'">

                                                            <div class="flex items-center justify-between cursor-pointer"
                                                                @click="toggleUpsell({{ json_encode($service) }}, '{{ $item['room']->id }}')">
                                                                <div class="flex items-center gap-3">
                                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center transition"
                                                                        :class="isUpsellSelected({{ $service->id }}, '{{ $item['room']->id }}') ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-600'">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                            viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4">
                                                                            </path>
                                                                        </svg>
                                                                    </div>
                                                                    <div>
                                                                        <p
                                                                            class="text-[10px] font-black text-slate-900 leading-tight uppercase">
                                                                            {{ $service->name }}
                                                                        </p>
                                                                        <p
                                                                            class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">
                                                                            ₹{{ number_format($service->price) }} /
                                                                            {{ str_replace('per_', '', $service->price_unit) }}
                                                                        </p>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    x-show="!isUpsellSelected({{ $service->id }}, '{{ $item['room']->id }}')">
                                                                    <div
                                                                        class="w-5 h-5 rounded-full border border-slate-200 flex items-center justify-center group-hover:border-blue-400">
                                                                        <svg class="w-3 h-3 text-slate-300 group-hover:text-blue-500"
                                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                        </svg>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            {{-- Quantity Selector --}}
                                                            <template
                                                                x-if="isUpsellSelected({{ $service->id }}, '{{ $item['room']->id }}')">
                                                                <div
                                                                    class="mt-3 pt-3 border-t border-blue-100 flex items-center justify-between animate-in slide-in-from-top-1">
                                                                    <span
                                                                        class="text-[9px] font-bold text-blue-800 uppercase tracking-wider">
                                                                        {{ $service->price_unit === 'per_hour' ? 'Hours' : 'Quantity' }}
                                                                    </span>
                                                                    <div
                                                                        class="flex items-center gap-3 bg-white rounded-lg p-1 shadow-sm border border-blue-100">
                                                                        <button type="button"
                                                                            @click.stop="updateUpsellQty({{ $service->id }}, '{{ $item['room']->id }}', -1)"
                                                                            class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-md transition">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="3" d="M20 12H4"></path>
                                                                            </svg>
                                                                        </button>
                                                                        <span
                                                                            class="text-xs font-black text-slate-700 w-4 text-center"
                                                                            x-text="getUpsellQty({{ $service->id }}, '{{ $item['room']->id }}')"></span>
                                                                        <button type="button"
                                                                            @click.stop="updateUpsellQty({{ $service->id }}, '{{ $item['room']->id }}', 1)"
                                                                            class="w-6 h-6 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                                                viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                                    stroke-width="3" d="M12 4v16m8-8H4"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- SECT 2: PAYMENT METHOD --}}
                        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden reveal reveal-delay-200"
                            x-cloak>
                            <div class="px-8 py-6 bg-slate-100 text-slate-900 border-b border-slate-200">
                                <h2 class="text-xl font-black tracking-tight flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center text-sm text-white">
                                        2</div>
                                    Payment options
                                </h2>
                            </div>

                            {{-- PROMO CODE --}}
                            <div class="px-8 pt-8 pb-8 border-b border-slate-100" x-data="{ 
                                promoCode: '', 
                                discount: 0,
                                error: '',
                                appliedCode: null,
                                loading: false,
                                async applyPromo() {
                                    if (!this.promoCode) {
                                        this.error = 'Please enter a promo code';
                                        return;
                                    }

                                    this.loading = true;
                                    this.error = '';

                                    // Get parent component's finalTotal
                                    const parentEl = this.$el.closest('[x-data]');
                                    const finalTotal = Alpine.$data(parentEl).finalTotal;

                                    console.log('Applying promo:', this.promoCode);
                                    console.log('Amount:', finalTotal);

                                    try {
                                        const response = await fetch('/api/promo/validate', {
                                            method: 'POST',
                                            headers: { 
                                                'Content-Type': 'application/json',
                                                'Accept': 'application/json'
                                            },
                                            body: JSON.stringify({ 
                                                code: this.promoCode.toUpperCase(),
                                                amount: finalTotal
                                            })
                                        });

                                        console.log('Response status:', response.status);
                                        console.log('Response ok:', response.ok);

                                        if (!response.ok) {
                                            const errorText = await response.text();
                                            console.error('Error response:', errorText);
                                            this.error = 'Server error: ' + response.status;
                                            return;
                                        }

                                        const data = await response.json();
                                        console.log('Response data:', data);

                                        if (data.valid) {
                                            this.discount = data.discount;
                                            this.appliedCode = this.promoCode.toUpperCase();
                                            // Update parent component
                                            Alpine.$data(parentEl).discountAmount = data.discount;
                                            Alpine.$data(parentEl).couponId = data.coupon_id;
                                            this.promoCode = ''; // Clear input on success
                                        } else {
                                            this.error = data.message || 'Invalid code';
                                        }
                                    } catch (e) {
                                        this.error = 'Network error: ' + e.message;
                                        console.error('Promo validation error:', e);
                                    } finally {
                                        this.loading = false;
                                    }
                                }
                            }">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Have
                                    a Promo Code?</label>
                                <div class="flex items-center gap-2">
                                    <div class="relative flex-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                                </path>
                                            </svg>
                                        </div>
                                        <input type="text" x-model="promoCode" placeholder="Enter code here..."
                                            :disabled="appliedCode !== null"
                                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold placeholder:text-slate-400 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white outline-none transition disabled:opacity-60 uppercase">
                                    </div>
                                    <button type="button" @click="applyPromo" :disabled="loading || appliedCode !== null"
                                        class="px-5 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition shadow-lg active:scale-95 disabled:opacity-50 flex items-center gap-2">
                                        <span x-show="!loading">Apply</span>
                                        <span x-show="loading">
                                            <svg class="animate-spin h-3 w-3 text-white" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>

                                <div x-show="error" x-transition
                                    class="mt-3 flex items-center gap-2 text-[10px] font-bold text-rose-500 bg-rose-50 px-3 py-2 rounded-lg border border-rose-100">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span x-text="error"></span>
                                </div>

                                <div x-show="appliedCode" x-transition
                                    class="mt-3 flex items-center justify-between p-3 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-100">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-bold">Code <span x-text="appliedCode"
                                                class="font-black"></span> applied!</span>
                                    </div>
                                    <span class="text-sm font-black">-₹<span
                                            x-text="discount.toLocaleString()"></span></span>
                                </div>
                            </div>

                            <div class="px-8 py-6 space-y-4">
                                {{-- MODE: HOTEL ONLY or DISABLED --}}
                                <template x-if="!paymentEnabled || paymentMode === 'hotel_only'">
                                    <label
                                        class="relative flex items-start p-5 bg-gradient-to-br from-blue-50 to-white rounded-2xl border border-blue-100 cursor-pointer shadow-sm group hover:border-blue-300 transition-all">
                                        <div class="absolute top-5 right-5 hidden md:block">
                                            <div
                                                class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-blue-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2-2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex items-center h-5 mt-1">
                                            <input type="radio" name="payment_type" value="offline" checked
                                                class="h-5 w-5 text-blue-600 border-slate-300 focus:ring-blue-500">
                                        </div>
                                        <div class="ml-4 pr-10">
                                            <span class="block text-base font-black text-slate-900 tracking-tight">Pay at
                                                Property</span>
                                            <span class="block text-sm font-medium text-slate-500 mt-1 leading-relaxed">
                                                No advance payment required. You can settle the full amount when you arrive
                                                at the reception.
                                            </span>
                                            <div
                                                class="mt-3 inline-flex items-center gap-2 px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-bold uppercase tracking-wide">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Book Risk-Free
                                            </div>
                                        </div>
                                    </label>
                                </template>

                                {{-- MODE: PARTIAL DEPOSIT --}}
                                <template x-if="paymentEnabled && paymentMode === 'partial_deposit'">
                                    <label
                                        class="relative block p-0 bg-white rounded-2xl border border-slate-200 cursor-pointer shadow-sm overflow-hidden group hover:border-blue-400 hover:ring-2 hover:ring-blue-500/10 transition-all">
                                        <div class="p-5 flex items-start gap-4">
                                            <div class="mt-1">
                                                <input type="radio" name="payment_type" value="online" checked
                                                    class="h-5 w-5 text-blue-600 border-slate-300 focus:ring-blue-500">
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-base font-black text-slate-900 tracking-tight">Pay
                                                        Deposit Online</span>
                                                    <span
                                                        class="text-[10px] font-black text-white bg-slate-900 px-2.5 py-1 rounded-lg uppercase tracking-wider">Required</span>
                                                </div>
                                                <p class="text-sm text-slate-500 leading-relaxed">
                                                    Securely pay the commitment deposit now to confirm your booking.
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="px-5 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pay
                                                    Now</span>
                                                <span class="text-xl font-black text-blue-600">₹<span
                                                        x-text="amountToPay.toLocaleString()"></span></span>
                                            </div>
                                            <div class="h-8 w-px bg-slate-200 mx-4"></div>
                                            <div class="flex flex-col text-right">
                                                <span
                                                    class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">At
                                                    Hotel</span>
                                                <span class="text-sm font-bold text-slate-600">₹<span
                                                        x-text="(finalTotal - amountToPay).toLocaleString()"></span></span>
                                            </div>
                                        </div>
                                    </label>
                                </template>

                                {{-- MODE: ONLINE ONLY --}}
                                <template x-if="paymentEnabled && paymentMode === 'online_only'">
                                    <label
                                        class="relative block p-0 bg-white rounded-2xl border border-slate-200 cursor-pointer shadow-sm overflow-hidden group hover:border-blue-400 hover:ring-2 hover:ring-blue-500/10 transition-all">
                                        <div class="p-5 flex items-start gap-4">
                                            <div class="mt-1">
                                                <input type="radio" name="payment_type" value="online" checked
                                                    class="h-5 w-5 text-blue-600 border-slate-300 focus:ring-blue-500">
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-base font-black text-slate-900 tracking-tight">Full
                                                        Payment Online</span>
                                                    <span
                                                        class="text-[10px] font-black text-white bg-blue-600 px-2.5 py-1 rounded-lg uppercase tracking-wider">Instant
                                                        Confirmation</span>
                                                </div>
                                                <p class="text-sm text-slate-500 leading-relaxed">
                                                    Securely pay for your entire stay now.
                                                </p>
                                            </div>
                                        </div>

                                        <div
                                            class="px-5 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                                            <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Total
                                                Payable Now</span>
                                            <span class="text-xl font-black text-blue-600">₹<span
                                                    x-text="finalTotal.toLocaleString()"></span></span>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full btn-primary py-3.5 text-lg shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40">
                            Confirm Booking
                        </button>

                        <p class="text-center text-xs text-slate-400">
                            By clicking Confirm, you agree to our booking terms. Your data is secure.
                        </p>

                    </form>
                </div>

                {{-- RIGHT COLUMN: SUMMARY --}}
                <div class="lg:col-span-1">
                    <div
                        class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 sticky top-24 reveal reveal-delay-200">
                        <h3
                            class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6 pb-4 border-b border-slate-100">
                            Booking Summary</h3>

                        {{-- DATES --}}
                        <div class="flex justify-between mb-6 text-sm">
                            <div>
                                <p class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($check_in)->format('d M') }}
                                </p>
                                <p class="text-slate-500 text-xs">Check-in</p>
                            </div>
                            <div class="text-slate-300">&rarr;</div>
                            <div class="text-right">
                                <p class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($check_out)->format('d M') }}
                                </p>
                                <p class="text-slate-500 text-xs">Check-out</p>
                            </div>
                        </div>

                        {{-- ITEMS --}}
                        <div class="space-y-4 mb-6">
                            @foreach($selectedRooms as $item)
                                <div class="flex justify-between text-sm">
                                    <span class="text-slate-600">{{ $item['qty'] }}x {{ $item['room']->name }}</span>
                                    <span class="font-bold text-slate-900">₹{{ number_format($item['total'], 0) }}</span>
                                </div>
                            @endforeach

                            {{-- EXTRA PERSONS IN SUMMARY --}}
                            <template x-if="extraPersonTotal > 0">
                                <div
                                    class="flex justify-between text-sm animate-in fade-in slide-in-from-right-2 duration-300 bg-slate-50 p-2 rounded-lg border border-slate-100">
                                    <span class="text-slate-600">Extra Guest Charges</span>
                                    <span class="font-bold text-slate-900">₹<span
                                            x-text="extraPersonTotal.toLocaleString()"></span></span>
                                </div>
                            </template>

                            {{-- DYNAMIC UPSELLS IN SUMMARY --}}
                            <template x-for="item in selectedUpsells" :key="item.id">
                                <div
                                    class="flex justify-between text-sm animate-in fade-in slide-in-from-right-2 duration-300">
                                    <div class="flex flex-col">
                                        <span class="text-blue-600 font-medium">+ <span x-text="item.name"></span></span>
                                        <span class="text-[10px] text-slate-400" x-show="item.price_unit !== 'fixed'">
                                            <span x-text="item.qty"></span> x ₹<span
                                                x-text="parseFloat(item.price).toLocaleString()"></span>
                                            <span x-show="item.price_unit === 'per_night'"> x <span x-text="nights"></span>
                                                nights</span>
                                        </span>
                                    </div>
                                    <span class="font-bold text-slate-900">₹<span
                                            x-text="(parseFloat(item.price) * (item.qty || 1) * (item.price_unit === 'per_night' ? nights : 1)).toLocaleString()"></span></span>
                                </div>
                            </template>
                            
                            {{-- DISCOUNT IN SUMMARY --}}
                            <template x-if="discountAmount > 0">
                                <div class="flex justify-between text-sm animate-in fade-in slide-in-from-right-2 duration-300 p-2 bg-emerald-50 rounded-lg border border-emerald-100">
                                    <span class="text-emerald-700 font-bold">Promo Discount</span>
                                    <span class="font-black text-emerald-700">-₹<span x-text="discountAmount.toLocaleString()"></span></span>
                                </div>
                            </template>
                        </div>

                        {{-- TOTAL --}}
                        <div class="border-t border-slate-100 pt-4 flex justify-between items-center mb-4">
                            <span class="font-bold text-slate-900">Grand Total</span>
                            <span class="text-2xl font-bold text-slate-900">₹<span
                                    x-text="finalTotal.toLocaleString()"></span></span>
                        </div>

                        {{-- DEPOSIT BREAKDOWN in Summary --}}
                        <template x-if="paymentEnabled && paymentMode !== 'hotel_only'">
                            <div class="space-y-2 mb-6 animate-in slide-in-from-top-2 duration-300">
                                <div
                                    class="flex justify-between items-center p-3 bg-blue-600 rounded-xl text-white shadow-lg shadow-blue-200">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Pay Now</span>
                                    <span class="text-lg font-black">₹<span
                                            x-text="amountToPay.toLocaleString()"></span></span>
                                </div>
                                <div
                                    class="flex justify-between items-center p-3 bg-slate-50 border border-slate-100 rounded-xl text-slate-400">
                                    <span class="text-[10px] font-black uppercase tracking-widest">Balance at
                                        Property</span>
                                    <span class="text-sm font-bold">₹<span
                                            x-text="(finalTotal - amountToPay).toLocaleString()"></span></span>
                                </div>
                            </div>
                        </template>

                        {{-- TRUST --}}
                        <div class="bg-slate-50 rounded-lg p-3 text-xs text-slate-500 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Free Cancellation
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                                Instant Confirmation
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection