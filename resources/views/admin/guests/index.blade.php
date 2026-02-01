@extends('layouts.admin')

@section('header_title', 'Guest CRM')

@section('content')

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-slate-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-slate-50/30">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Guest Database</h3>
                <p class="text-xs text-slate-400 font-medium tracking-tight">Enterprise guest search and relationship management</p>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <form action="{{ route('admin.guests.index') }}" method="GET" class="relative w-full md:w-auto">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, phone, or email..."
                        class="bg-white border border-slate-200 rounded-xl py-3 pl-10 pr-4 text-sm font-medium w-full md:w-80 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition-all shadow-sm">
                </form>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="px-8 py-5 text-left">Guest Identity</th>
                        <th class="px-8 py-5 text-left">Contact Info</th>
                        <th class="px-8 py-5 text-center">Visit count</th>
                        <th class="px-8 py-5 text-center">Lifetime Value</th>
                        <th class="px-8 py-5 text-left">Last Interaction</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($guests as $guest)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 {{ $guest->total_ltv > 5000 ? 'bg-amber-100 text-amber-600' : 'bg-blue-50 text-blue-600' }} rounded-xl flex items-center justify-center font-bold text-sm transition-colors group-hover:bg-white shadow-sm group-hover:shadow-md">
                                        {{ strtoupper(substr($guest->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold text-slate-900">{{ $guest->name }}</p>
                                            @if($guest->stay_count >= 3)
                                                <span class="px-1.5 py-0.5 bg-indigo-50 text-indigo-600 rounded text-[9px] font-black uppercase tracking-tighter">Frequent</span>
                                            @endif
                                            @if($guest->total_ltv > 5000)
                                                <span class="px-1.5 py-0.5 bg-amber-50 text-amber-600 rounded text-[9px] font-black uppercase tracking-tighter">VIP</span>
                                            @endif
                                        </div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                            Verified Stayer
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-xs font-bold text-slate-700">{{ $guest->phone ?? 'Private' }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $guest->email ?: 'No email linked' }}</p>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-xs font-black">
                                    {{ $guest->stay_count }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-center">
                                <p class="text-sm font-black text-slate-900 tracking-tight">₹{{ number_format($guest->total_ltv, 0) }}</p>
                                @if($guest->total_ltv > 10000)
                                    <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest opacity-80">Elite Tier</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-xs font-bold text-slate-700">
                                    {{ \Carbon\Carbon::parse($guest->last_stay)->format('d M, Y') }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">
                                    {{ \Carbon\Carbon::parse($guest->last_stay)->diffForHumans() }}</p>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="{{ route('admin.guests.show', ['phone' => $guest->phone ?: 'none']) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-200 shadow-sm">
                                    Manage Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-24 text-center">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                </div>
                                <p class="text-sm font-bold text-slate-500">No guest profiles matching your search.</p>
                                <a href="{{ route('admin.guests.index') }}" class="text-xs text-blue-600 hover:underline mt-2 inline-block">Clear all filters</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden">
            @forelse($guests as $guest)
                <div class="p-6 border-b border-slate-100 last:border-0 flex flex-col gap-4 hover:bg-slate-50 transition active:scale-[0.98]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 {{ $guest->total_ltv > 5000 ? 'bg-amber-100 text-amber-600' : 'bg-blue-50 text-blue-600' }} rounded-2xl flex items-center justify-center font-black text-xl shadow-sm">
                                {{ strtoupper(substr($guest->name, 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900">{{ $guest->name }}</h4>
                                <p class="text-xs font-bold text-slate-400">{{ $guest->phone }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-black text-slate-900">₹{{ number_format($guest->total_ltv, 0) }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Lifetime Value</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between pt-2 border-t border-slate-50">
                        <div class="flex gap-2">
                             <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-black uppercase tracking-wide">
                                {{ $guest->stay_count }} Stays
                            </span>
                             @if($guest->total_ltv > 5000)
                                <span class="px-2 py-1 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-wide">VIP</span>
                            @endif
                        </div>
                        
                         <a href="{{ route('admin.guests.show', ['phone' => $guest->phone ?: 'none']) }}"
                            class="px-5 py-2.5 bg-slate-900 text-white rounded-xl text-xs font-bold shadow-lg shadow-slate-200">
                            View Profile
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-10 text-center opacity-40">
                    <p class="text-sm font-bold text-slate-500">No guests found.</p>
                </div>
            @endforelse
        </div>

        @if($guests->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $guests->links() }}
            </div>
        @endif
    </div>

@endsection