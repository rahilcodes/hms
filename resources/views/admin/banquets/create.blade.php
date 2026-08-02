@extends('layouts.admin')

@section('header_title', $event ? 'Edit Event' : 'New Banquet Event')

@section('content')
    <div class="max-w-3xl mx-auto"
        x-data="{
            plates: {{ (int) old('food_plates', $event->food_plates ?? 0) }},
            rate: {{ (float) old('per_plate_rate', $event->per_plate_rate ?? 0) }},
            rent: {{ (float) old('hall_rent', $event->hall_rent ?? 0) }},
            deco: {{ (float) old('decoration_charge', $event->decoration_charge ?? 0) }},
            other: {{ (float) old('other_charges', $event->other_charges ?? 0) }},
            discount: {{ (float) old('discount', $event->discount ?? 0) }},
            advance: {{ (float) old('advance_paid', $event->advance_paid ?? 0) }},
            get total() { return Math.max(0, this.plates * this.rate + Number(this.rent) + Number(this.deco) + Number(this.other) - Number(this.discount)); },
            get balance() { return Math.max(0, this.total - this.advance); }
        }">

        <form method="POST"
            action="{{ $event ? route('admin.banquets.update', $event) : route('admin.banquets.store') }}"
            class="space-y-5">
            @csrf
            @if($event) @method('PUT') @endif

            @if($errors->any())
                <div class="bg-rose-50 border border-rose-200 text-rose-600 text-sm rounded-xl p-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-wide">Customer</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500">Name *</label>
                        <input name="customer_name" required value="{{ old('customer_name', $event->customer_name ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Phone *</label>
                        <input name="customer_phone" required value="{{ old('customer_phone', $event->customer_phone ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Email</label>
                        <input name="customer_email" type="email" value="{{ old('customer_email', $event->customer_email ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Customer GSTIN (for B2B invoice)</label>
                        <input name="customer_gstin" value="{{ old('customer_gstin', $event->customer_gstin ?? '') }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-bold text-slate-500">Corporate Account (optional)</label>
                        <select name="company_id" class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm bg-white">
                            <option value="">— None —</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected(old('company_id', $event->company_id ?? null) == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-wide">Event</h3>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500">Hall *</label>
                        <select name="banquet_hall_id" required class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm bg-white">
                            @foreach($halls as $hall)
                                <option value="{{ $hall->id }}" @selected(old('banquet_hall_id', $event->banquet_hall_id ?? null) == $hall->id)>
                                    {{ $hall->name }} ({{ $hall->capacity }} pax)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Event Type *</label>
                        <select name="event_type" required class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm bg-white">
                            @foreach(\App\Models\BanquetBooking::EVENT_TYPES as $type)
                                <option value="{{ $type }}" @selected(old('event_type', $event->event_type ?? 'wedding') === $type)>{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Date *</label>
                        <input name="event_date" type="date" required
                            value="{{ old('event_date', optional($event?->event_date)->toDateString()) }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                        @error('event_date') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-bold text-slate-500">Start</label>
                            <input name="start_time" type="time" value="{{ old('start_time', $event->start_time ?? '') }}"
                                class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500">End</label>
                            <input name="end_time" type="time" value="{{ old('end_time', $event->end_time ?? '') }}"
                                class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Guests Expected</label>
                        <input name="guests_expected" type="number" min="0" value="{{ old('guests_expected', $event->guests_expected ?? 0) }}"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Status *</label>
                        <select name="status" required class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm bg-white">
                            @foreach(\App\Models\BanquetBooking::STATUSES as $status)
                                <option value="{{ $status }}" @selected(old('status', $event->status ?? 'enquiry') === $status)>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5 space-y-4">
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-wide">Quote</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500">Per Plate ₹</label>
                        <input name="per_plate_rate" type="number" step="0.01" min="0" x-model.number="rate"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Plates</label>
                        <input name="food_plates" type="number" min="0" x-model.number="plates"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Hall Rent ₹</label>
                        <input name="hall_rent" type="number" step="0.01" min="0" x-model.number="rent"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Decoration ₹</label>
                        <input name="decoration_charge" type="number" step="0.01" min="0" x-model.number="deco"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Other ₹</label>
                        <input name="other_charges" type="number" step="0.01" min="0" x-model.number="other"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Discount ₹</label>
                        <input name="discount" type="number" step="0.01" min="0" x-model.number="discount"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500">Advance Paid ₹</label>
                        <input name="advance_paid" type="number" step="0.01" min="0" x-model.number="advance"
                            class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 p-4 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Estimated Total</p>
                        <p class="text-2xl font-black text-slate-800">₹<span x-text="total.toLocaleString('en-IN')"></span></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Balance</p>
                        <p class="text-lg font-black" :class="balance > 0 ? 'text-rose-500' : 'text-emerald-600'">₹<span x-text="balance.toLocaleString('en-IN')"></span></p>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500">Notes (menu, requirements…)</label>
                    <textarea name="notes" rows="3" class="mt-1 w-full px-3.5 py-3 rounded-xl border border-slate-200 text-sm">{{ old('notes', $event->notes ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3">
                <a href="{{ route('admin.banquets.index') }}" class="flex-1 py-3.5 rounded-xl bg-white border border-slate-200 text-center text-sm font-bold text-slate-500">Cancel</a>
                <button class="flex-1 py-3.5 rounded-xl bg-blue-600 text-white text-sm font-black shadow">{{ $event ? 'Update Event' : 'Save Event' }}</button>
            </div>
        </form>
    </div>
@endsection
