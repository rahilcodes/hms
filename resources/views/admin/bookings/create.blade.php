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
                            <label class="inline-flex items-center cursor-pointer">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest mr-3">Corporate
                                    Booking?</span>
                                <input type="checkbox" name="is_corporate" x-model="isCorporate" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>

                            <div x-show="isCorporate" class="mt-4 animate-in fade-in slide-in-from-top-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Link Corporate Profile</label>
                                <select name="company_id"
                                    class="w-full p-2.5 bg-blue-50/50 border border-blue-100 rounded focus:ring-blue-500 focus:border-blue-500 text-xs font-bold">
                                    <option value="">-- Select Company --</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }} (Limit:
                                            ₹{{ number_format($company->credit_limit) }})</option>
                                    @endforeach
                                </select>
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
                            <input type="text" name="guest_phone" placeholder="+1 234 567 890"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                        </div>
                    </div>
                </div>

                {{-- STAY DETAILS --}}
                <div class="border-b border-gray-100 pb-6 mb-6" x-data="{ 
                                    isBulk: false,
                                    items: [
                                        { room_type_id: '{{ $roomTypes->first()->id ?? 0 }}', rooms: 1, extra_persons: 0 }
                                    ],
                                    roomCapacities: {
                                        @foreach($roomTypes as $type)
                                            '{{ $type->id }}': {{ $type->max_extra_persons }},
                                        @endforeach
                                    },
                                    addItem() {
                                        this.items.push({ room_type_id: '{{ $roomTypes->first()->id ?? 0 }}', rooms: 1, extra_persons: 0 });
                                    },
                                    removeItem(index) {
                                        if (this.items.length > 1) this.items.splice(index, 1);
                                    }
                                }">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Stay & Inventory Allocation</h3>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Check-in Date</label>
                            <input type="date" name="check_in" required min="{{ date('Y-m-d') }}"
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Check-out Date</label>
                            <input type="date" name="check_out" required
                                class="w-full p-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-sm font-bold text-slate-900">
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
                                    <div class="md:col-span-4">
                                        <label
                                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1">Room
                                            Selection</label>
                                        <select :name="'items['+index+'][room_type_id]'" x-model="item.room_type_id"
                                            required required
                                            class="w-full p-3 bg-white border border-slate-200 rounded-xl text-sm font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 transition shadow-sm">
                                            @foreach($roomTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}
                                                    (₹{{ number_format($type->base_price) }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label
                                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 ml-1 text-center">Qty</label>
                                        <input type="number" :name="'items['+index+'][rooms]'" x-model="item.rooms" min="1"
                                            required
                                            class="w-full p-2.5 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-900 focus:ring-2 focus:ring-blue-500 transition text-center shadow-sm">
                                    </div>
                                    <div class="md:col-span-3" x-show="roomCapacities[item.room_type_id] > 0">
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
                                    <div class="md:col-span-3">
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
                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="w-full md:w-auto px-8 py-3.5 bg-blue-600 text-white font-bold text-sm uppercase tracking-wider rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 hover:-translate-y-0.5 transition-all">
                        Create Booking
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection