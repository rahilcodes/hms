@extends('layouts.admin')

@section('content')

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.bookings.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1 mb-2">
                &larr; Back to list
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Create New Booking</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="{{ route('admin.bookings.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- GUEST INFO & CORPORATE --}}
                <div class="border-b border-gray-100 pb-6 mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Guest & Corporate Context</h3>
                        <div x-data="{ isCorporate: false }">
                            <label class="inline-flex items-center cursor-pointer group bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100 hover:border-blue-200 transition duration-300">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest mr-3 group-hover:text-blue-600 transition">Corporate
                                    Booking?</span>
                                <input type="checkbox" name="is_corporate" x-model="isCorporate" class="sr-only peer">
                                <div
                                    class="relative w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 shadow-inner">
                                </div>
                            </label>

                            <div x-show="isCorporate" class="mt-4 animate-in fade-in slide-in-from-top-2 p-4 bg-blue-50/30 rounded-2xl border border-blue-100/50">
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-semibold text-gray-700">Link Corporate Profile</label>
                                    <a href="{{ route('admin.companies.create') }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1 group">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                        <span class="underline underline-offset-2 decoration-blue-200 group-hover:decoration-blue-400 transition">Create New Profile</span>
                                    </a>
                                </div>
                                <select name="company_id"
                                    class="w-full p-3 bg-white border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900 shadow-sm appearance-none cursor-pointer" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%231e293b%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right .7rem top 50%; background-size: .65rem auto;">
                                    <option value="" disabled selected class="text-slate-400">Select an existing corporate profile...</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }} (Available Limit: ₹{{ number_format($company->credit_limit) }})</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] font-bold text-slate-500 mt-2 ml-1">Corporate bookings will be tracked against the selected company's credit limit.</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Primary Guest Name</label>
                            <input type="text" name="guest_name" required placeholder="John Doe"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="guest_email" required placeholder="john@example.com"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                            <input type="text" name="guest_phone" placeholder="+91 98765 43210"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Travel Agent <span class="text-slate-400 font-normal">(optional)</span></label>
                            <select name="agent_id"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                                <option value="">— Direct booking —</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}">{{ $agent->agency_name ?: $agent->name }} ({{ rtrim(rtrim(number_format($agent->commission_percent, 2), '0'), '.') }}%)</option>
                                @endforeach
                            </select>
                            <p class="text-[10px] font-bold text-slate-400 mt-1 ml-1">Commission is auto-calculated for the agent report.</p>
                        </div>
                    </div>
                </div>

                {{-- STAY DETAILS --}}
                <div class="border-b border-gray-100 pb-6 mb-6" x-data="{
                                    isBulk: false,
                                    isDayUse: false,
                                    dayUseHours: '4',
                                    checkIn: '{{ old('check_in', date('Y-m-d')) }}',
                                    checkOut: '{{ old('check_out', date('Y-m-d', strtotime('+1 day'))) }}',
                                    items: [
                                        { room_type_id: '', rooms: 1, extra_persons: 0, children: 0 }
                                    ],
                                    roomCapacities: {
                                        @foreach($roomTypes as $type)
                                            '{{ $type->id }}': {{ $type->max_extra_persons }},
                                        @endforeach
                                    },
                                    availabilities: {},
                                    isFetching: false,
                                    get minCheckOut() {
                                        if (!this.checkIn) return '{{ date('Y-m-d', strtotime('+1 day')) }}';
                                        let d = new Date(this.checkIn);
                                        d.setDate(d.getDate() + 1);
                                        return d.toISOString().split('T')[0];
                                    },
                                    countSelected(roomTypeId) {
                                        if(!roomTypeId) return 0;
                                        return this.items.filter(i => i.room_type_id == roomTypeId).length;
                                    },
                                    hasOverAllocation() {
                                        return this.items.some(item => {
                                            if (!item.room_type_id) return false;
                                            let available = this.availabilities[item.room_type_id];
                                            return available !== undefined && this.countSelected(item.room_type_id) > available;
                                        });
                                    },
                                    async fetchAvailabilities() {
                                        if (!this.checkIn || !this.checkOut || this.checkOut <= this.checkIn) {
                                            this.availabilities = {};
                                            return;
                                        }
                                        this.isFetching = true;
                                        try {
                                            const res = await fetch(`/admin/bookings/availability?check_in=${this.checkIn}&check_out=${this.checkOut}`, {
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'X-Requested-With': 'XMLHttpRequest'
                                                },
                                                credentials: 'same-origin'
                                            });
                                            if (res.ok) {
                                                this.availabilities = await res.json();
                                            } else {
                                                alert('Availability API failed: HTTP ' + res.status);
                                            }
                                        } catch (e) {
                                            alert('Availability check crashed: ' + e.message);
                                            console.error('Availability check failed', e);
                                        } finally {
                                            this.isFetching = false;
                                        }
                                    },
                                    addItem() {
                                        this.items.push({ room_type_id: '', rooms: 1, extra_persons: 0, children: 0 });
                                    },
                                    removeItem(index) {
                                        if (this.items.length > 1) this.items.splice(index, 1);
                                    },
                                    init() {
                                        this.fetchAvailabilities();
                                        this.$watch('checkIn', value => {
                                            if (this.checkOut <= value) {
                                                this.checkOut = this.minCheckOut;
                                            }
                                            this.fetchAvailabilities();
                                        });
                                        this.$watch('checkOut', value => {
                                            if (this.checkOut <= this.checkIn) {
                                                this.checkOut = this.minCheckOut;
                                            }
                                            this.fetchAvailabilities();
                                        });
                                        this.$watch('isDayUse', value => {
                                            if (value) {
                                                this.checkOut = this.minCheckOut;
                                                this.fetchAvailabilities();
                                            }
                                        });
                                    }
                                }">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Stay & Inventory Allocation</h3>
                        <div class="flex items-center gap-2">
                            <label
                                class="inline-flex items-center cursor-pointer group bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100 hover:border-amber-200 transition duration-300">
                                <span
                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest mr-3 group-hover:text-amber-600 transition">Day-Use
                                    (Hourly)</span>
                                <input type="checkbox" x-model="isDayUse" class="sr-only peer">
                                <div
                                    class="relative w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-amber-500 shadow-inner">
                                </div>
                            </label>
                            <label
                                class="inline-flex items-center cursor-pointer group bg-slate-50 px-4 py-2 rounded-2xl border border-slate-100 hover:border-blue-200 transition duration-300">
                                <span
                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest mr-3 group-hover:text-blue-600 transition">Multi-Room
                                    / Mixed Types</span>
                                <input type="checkbox" x-model="isBulk" name="is_bulk" class="sr-only peer">
                                <div
                                    class="relative w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600 shadow-inner">
                                </div>
                            </label>
                        </div>
                    </div>

                    <input type="hidden" name="booking_type" :value="isDayUse ? 'day_use' : 'overnight'">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1" x-text="isDayUse ? 'Date' : 'Check-in Date'">Check-in Date</label>
                            <input type="date" name="check_in" required min="{{ date('Y-m-d') }}"
                                x-model="checkIn"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                        </div>
                        <div x-show="!isDayUse">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Check-out Date</label>
                            <input type="date" name="check_out" :required="!isDayUse" :min="minCheckOut"
                                x-model="checkOut"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                        </div>
                        <div x-show="isDayUse" x-cloak>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Day-Use Slot</label>
                            <select name="day_use_hours" x-model="dayUseHours"
                                class="w-full p-3 bg-amber-50 border border-amber-200 rounded-xl focus:ring-amber-500 text-sm font-bold text-slate-900">
                                <option value="4">4 Hours (short slab)</option>
                                <option value="8">8 Hours (full-day slab)</option>
                            </select>
                            <p class="text-[10px] font-bold text-amber-600 mt-1 ml-1">Priced from the room type's day-use slab. Same-day check-in &amp; check-out.</p>
                        </div>
                    </div>

                    {{-- Dynamic Room Rows --}}
                    <div class="space-y-4">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="p-5 bg-slate-50/50 rounded-[2rem] border border-slate-200 relative animate-in fade-in zoom-in-95 duration-200"
                                x-data="{ showServices: false }">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                    class="absolute -top-2 -right-2 w-7 h-7 bg-rose-500 text-white rounded-full flex items-center justify-center shadow-lg hover:bg-rose-600 transition transform hover:scale-110 z-10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                                    <div class="md:col-span-6 relative">
                                        <label
                                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Room
                                            Selection</label>
                                        <select :name="'items['+index+'][room_type_id]'" x-model="item.room_type_id"
                                            required
                                            class="w-full p-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                                            <option value="" disabled selected>Select Room Type</option>
                                            @foreach($roomTypes as $type)
                                                <option value="{{ $type->id }}"
                                                    x-text="'{{ $type->name }} (₹{{ number_format($type->base_price) }}) - ' + (isFetching ? 'Checking...' : (availabilities['{{ $type->id }}'] !== undefined ? availabilities['{{ $type->id }}'] + ' Available' : 'Checking...'))"
                                                    :disabled="availabilities['{{ $type->id }}'] !== undefined && availabilities['{{ $type->id }}'] <= countSelected('{{ $type->id }}') && item.room_type_id != '{{ $type->id }}'"
                                                    >{{ $type->name }} (₹{{ number_format($type->base_price) }})</option>
                                            @endforeach
                                        </select>
                                        <div class="mt-1.5 ml-1" x-show="item.room_type_id">
                                            <p class="text-[9px] font-bold uppercase tracking-wider"
                                                :class="(availabilities[item.room_type_id] !== undefined && countSelected(item.room_type_id) > availabilities[item.room_type_id]) ? 'text-rose-500' : 'text-emerald-500'">
                                                <span x-text="availabilities[item.room_type_id] !== undefined ? availabilities[item.room_type_id] : '...'"></span> Total Available
                                                <span x-show="countSelected(item.room_type_id) > 1" x-text="'(You selected ' + countSelected(item.room_type_id) + ')'"></span>
                                            </p>
                                            <p class="text-[9px] font-bold text-rose-500 uppercase tracking-wider mt-0.5" 
                                                x-show="availabilities[item.room_type_id] !== undefined && countSelected(item.room_type_id) > availabilities[item.room_type_id]">
                                                Over capacity! Please remove extra rows.
                                            </p>
                                        </div>
                                        <input type="hidden" :name="'items['+index+'][rooms]'" value="1">
                                    </div>
                                    <div class="md:col-span-2" x-show="roomCapacities[item.room_type_id] > 0">
                                        <label
                                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Extra
                                            Persons</label>
                                        <select :name="'items['+index+'][extra_persons]'" x-model="item.extra_persons"
                                            class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                                            <template
                                                x-for="i in Array.from({length: parseInt(roomCapacities[item.room_type_id] || 0) + 1}, (_, i) => i)">
                                                <option :value="i" x-text="i + ' Person' + (i !== 1 ? 's' : '')"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Children
                                            5-12</label>
                                        <select :name="'items['+index+'][children]'" x-model="item.children"
                                            class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                                            <template x-for="i in [0,1,2,3,4]">
                                                <option :value="i" x-text="i + (i === 1 ? ' Child' : ' Children')"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="button" @click="showServices = !showServices"
                                            class="w-full p-2.5 border border-slate-200 rounded-xl text-[10px] font-black uppercase tracking-widest transition flex items-center justify-center gap-2"
                                            :class="showServices ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-500 hover:border-blue-300 hover:text-blue-600'">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            <span x-text="showServices ? 'Hide Add-ons' : 'Add Services'"></span>
                                        </button>
                                    </div>
                                </div>

                                {{-- Per-Room Add-ons --}}
                                <div x-show="showServices" x-transition
                                    class="mt-4 pt-4 border-t border-slate-200 space-y-3">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                                        Select Add-ons for this specific room</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($services as $service)
                                            <div
                                                class="p-2.5 bg-white border border-slate-100 rounded-xl flex items-center justify-between hover:border-blue-100 transition shadow-sm">
                                                <div class="flex items-center gap-2.5">
                                                    <div
                                                        class="w-7 h-7 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-black text-slate-900 leading-tight">
                                                            {{ $service->name }}
                                                        </p>
                                                        <p
                                                            class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter">
                                                            ₹{{ number_format($service->price) }} /
                                                            {{ str_replace('per_', '', $service->price_unit) }}
                                                        </p>
                                                    </div>
                                                </div>
                                                @if($service->price_unit === 'fixed')
                                                    <input type="checkbox" :name="'items['+index+'][services][{{ $service->id }}]'"
                                                        value="1"
                                                        class="w-4 h-4 text-blue-600 border-slate-200 rounded focus:ring-blue-500">
                                                @else
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-[8px] font-black text-slate-300 uppercase">Qty</span>
                                                        <input type="number"
                                                            :name="'items['+index+'][services][{{ $service->id }}]'" value="0"
                                                            min="0"
                                                            class="w-10 p-1 bg-slate-50 border border-slate-200 rounded text-[10px] font-bold text-center focus:ring-2 focus:ring-blue-500 outline-none">
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </template>

                        <button type="button" @click="addItem()" x-show="isBulk"
                            class="w-full py-4 border-2 border-dashed border-slate-200 rounded-[2rem] text-[10px] font-black text-slate-400 uppercase tracking-widest hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 transition flex items-center justify-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Another Room Category to Stay
                        </button>
                    </div>
                </div>

                {{-- PRICING --}}
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Pricing</h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Custom Total Amount (₹)</label>
                        <input type="number" name="total_amount" step="0.01" placeholder="Leave blank to auto-calculate"
                            class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                        <p class="text-xs text-gray-500 mt-2 italic">
                            If left blank, the system will auto-calculate: (Base Price × Rooms × Nights)
                            + Extra Guests + Selected Add-ons.
                        </p>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-6 border-t border-gray-100 flex justify-end items-center gap-4">
                    <p class="text-[10px] font-bold text-rose-500 uppercase tracking-wider" x-show="hasOverAllocation()">
                        Cannot submit: Over Capacity
                    </p>
                    <button type="submit"
                        :disabled="hasOverAllocation()"
                        :class="hasOverAllocation() ? 'opacity-50 cursor-not-allowed bg-slate-400 shadow-none' : 'bg-blue-600 hover:bg-blue-700 hover:-translate-y-0.5 shadow-blue-200 shadow-lg'"
                        class="w-full md:w-auto px-8 py-3.5 text-white font-bold text-sm uppercase tracking-wider rounded-xl transition-all">
                        Create Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection