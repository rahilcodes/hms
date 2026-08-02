@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">A/R Aging Report</h1>
            <p class="text-slate-500 font-medium">Track outstanding corporate balances by duration.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.companies.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-bold rounded-lg shadow-sm hover:bg-slate-50 transition">
                Back to Companies
            </a>
        </div>
    </div>

    {{-- Summary Row --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        @php
            $totalAR = $companies->sum('balance');
            $over90 = $companies->where('category', '90+')->sum('balance');
        @endphp
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Outstanding</p>
            <h3 class="text-3xl font-black text-slate-900">₹{{ number_format($totalAR) }}</h3>
        </div>
        <div class="bg-rose-50 p-6 rounded-2xl border border-rose-100 shadow-sm">
            <p class="text-xs font-black text-rose-400 uppercase tracking-widest mb-1 text-opacity-80">Critical (90+ Days)</p>
            <h3 class="text-3xl font-black text-rose-600">₹{{ number_format($over90) }}</h3>
        </div>
    </div>

    {{-- Aging Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Company</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-center">0-30 Days</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-center">31-60 Days</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-center">61-90 Days</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-center">90+ Days</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Total Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($companies->sortByDesc('balance') as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="font-bold text-slate-900">{{ $item->name }}</p>
                            <p class="text-[10px] text-slate-400">Limit: ₹{{ number_format($item->credit_limit) }}</p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->category == '0-30')
                                <span class="font-bold text-slate-900 font-mono">₹{{ number_format($item->balance) }}</span>
                            @else
                                <span class="text-slate-200 font-mono">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->category == '30-60')
                                <span class="font-bold text-amber-600 font-mono">₹{{ number_format($item->balance) }}</span>
                            @else
                                <span class="text-slate-200 font-mono">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->category == '60-90')
                                <span class="font-bold text-orange-600 font-mono">₹{{ number_format($item->balance) }}</span>
                            @else
                                <span class="text-slate-200 font-mono">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->category == '90+')
                                <span class="font-bold text-rose-600 font-mono">₹{{ number_format($item->balance) }}</span>
                            @else
                                <span class="text-slate-200 font-mono">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-black text-slate-900 font-mono">₹{{ number_format($item->balance) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium italic">No outstanding corporate balances.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
