@extends('layouts.admin')

@section('header_title', isset($coupon) ? 'Edit Promo Code' : 'New Promo Code')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <a href="{{ route('admin.coupons.index') }}"
                class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Campaigns
            </a>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter mt-4">
                {{ isset($coupon) ? 'Edit Campaign' : 'Launch New Campaign' }}</h2>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden p-10">
            <form action="{{ isset($coupon) ? route('admin.coupons.update', $coupon) : route('admin.coupons.store') }}"
                method="POST" class="space-y-8">
                @csrf
                @if(isset($coupon)) @method('PUT') @endif

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Promo
                            Code</label>
                        <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required
                            placeholder="e.g. WELCOME10"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition uppercase">
                        @error('code') <p class="text-[10px] text-rose-500 font-bold mt-2 ml-1 uppercase tracking-tight">
                        {{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Discount
                                Type</label>
                            <select name="type"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition">
                                <option value="fixed" {{ old('type', $coupon->type ?? '') === 'fixed' ? 'selected' : '' }}>
                                    Fixed Amount (₹)</option>
                                <option value="percentage" {{ old('type', $coupon->type ?? '') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Discount
                                Value</label>
                            <input type="number" step="0.01" name="value" value="{{ old('value', $coupon->value ?? '') }}"
                                required placeholder="e.g. 10"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Min
                                Spend (₹)</label>
                            <input type="number" step="0.01" name="min_spend"
                                value="{{ old('min_spend', $coupon->min_spend ?? 0) }}" required
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Max
                                Discount (₹)</label>
                            <input type="number" step="0.01" name="max_discount"
                                value="{{ old('max_discount', $coupon->max_discount ?? '') }}"
                                placeholder="Optional (for %)"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Expiry
                                Date</label>
                            <input type="datetime-local" name="expires_at"
                                value="{{ old('expires_at', isset($coupon->expires_at) ? $coupon->expires_at->format('Y-m-d\TH:i') : '') }}"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition">
                            <p class="text-[9px] text-slate-400 font-bold mt-2 ml-1 uppercase tracking-tighter italic">Leave
                                blank for perpetual code</p>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Usage
                                Limit</label>
                            <input type="number" name="usage_limit"
                                value="{{ old('usage_limit', $coupon->usage_limit ?? '') }}" placeholder="Unlimited"
                                class="w-full px-5 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold text-slate-900 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <label class="flex items-center gap-4 cursor-pointer group">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $coupon->is_active ?? true) ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:bg-emerald-500 transition-all relative after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                            </div>
                            <span
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-slate-900 transition">Enable
                                this Campaign</span>
                        </label>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-xs uppercase tracking-[0.2em] hover:bg-black transition-all shadow-2xl shadow-slate-200 active:scale-95 mt-4">
                    {{ isset($coupon) ? 'Update Campaign settings' : 'Launch Campaign' }}
                </button>
            </form>
        </div>
    </div>
@endsection