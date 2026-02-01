@extends('layouts.admin')

@section('header_title', 'Marketing & Promos')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Promo Codes</h2>
            <p class="text-slate-500 font-medium text-sm">Create and manage marketing incentives.</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}"
            class="px-6 py-4 bg-slate-900 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-black transition-all shadow-xl shadow-slate-200 active:scale-95">
            Create Promo Code
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Code</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Discount</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Validity</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                            Usage</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($coupons as $coupon)
                        <tr class="hover:bg-slate-50/50 transition duration-300">
                            <td class="px-8 py-6">
                                <span
                                    class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-black tracking-tight border border-blue-100">
                                    {{ $coupon->code }}
                                </span>
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-xs font-black text-slate-900">
                                    {{ $coupon->type === 'percentage' ? $coupon->value . '%' : '₹' . number_format($coupon->value) }}
                                </p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">
                                    Min Spend: ₹{{ number_format($coupon->min_spend) }}
                                </p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="text-[10px] font-black text-slate-600">
                                    @if($coupon->expires_at)
                                        <div
                                            class="flex items-center gap-1.5 {{ $coupon->expires_at->isPast() ? 'text-rose-500' : '' }}">
                                            <div
                                                class="w-1.5 h-1.5 rounded-full {{ $coupon->expires_at->isPast() ? 'bg-rose-500' : 'bg-emerald-500' }}">
                                            </div>
                                            {{ $coupon->expires_at->format('d M, Y') }}
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Perpetual</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <p class="text-[10px] font-black text-slate-900">{{ $coupon->used_count }} /
                                    {{ $coupon->usage_limit ?? '∞' }}</p>
                                <div class="w-16 h-1 bg-slate-100 rounded-full mx-auto mt-2 overflow-hidden">
                                    <div class="h-full bg-blue-500"
                                        style="width: {{ $coupon->usage_limit ? ($coupon->used_count / $coupon->usage_limit) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 {{ $coupon->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }} rounded-full text-[9px] font-black uppercase tracking-widest">
                                    <div
                                        class="w-1.5 h-1.5 rounded-full {{ $coupon->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}">
                                    </div>
                                    {{ $coupon->is_active ? 'Active' : 'Disabled' }}
                                </span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('admin.coupons.edit', $coupon) }}"
                                        class="p-2.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST"
                                        onsubmit="return confirm('Archive this promo code?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-32 text-center opacity-30">
                                <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-[2rem] mb-6">
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </div>
                                <p class="text-xl font-black text-slate-900 tracking-tight">No Active Campaigns</p>
                                <p class="text-xs font-medium text-slate-500 mt-1">Start by creating your first promo code.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($coupons->hasPages())
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100">
                {{ $coupons->links() }}
            </div>
        @endif
    </div>
@endsection