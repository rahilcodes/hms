@extends('layouts.admin')

@section('header_title', 'Inventory Matrix')

@section('content')
        
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter font-display mb-2">Inventory Matrix</h1>
                <p class="text-slate-500 font-medium">Bird's-eye view of room occupancy and availability.</p>
            </div>
            
            <form action="{{ route('admin.inventory.index') }}" method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 bg-white p-3 rounded-2xl border border-slate-100 shadow-sm">
                <div class="flex flex-col px-4 border-b sm:border-b-0 sm:border-r border-slate-100 pb-3 sm:pb-0">
                    <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" 
                           class="bg-transparent border-none p-0 text-xs font-black text-slate-900 focus:ring-0 outline-none">
                </div>
                <div class="flex flex-col px-4">
                    <label class="text-[8px] font-black text-slate-400 uppercase tracking-widest leading-none mb-2">Range</label>
                    <select name="days" class="bg-transparent border-none p-0 text-xs font-black text-slate-900 focus:ring-0 outline-none appearance-none pr-6 titanium-select cursor-pointer">
                        <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 Days</option>
                        <option value="15" {{ $days == 15 ? 'selected' : '' }}>15 Days</option>
                        <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Days</option>
                    </select>
                </div>
                <button type="submit" class="bg-slate-900 text-white px-8 py-4 sm:py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 transition-all flex items-center justify-center gap-2">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    Sync
                </button>
            </form>
        </div>

        {{-- MATRIX GRID --}}
        <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="sticky left-0 top-0 z-30 bg-slate-50 border-r border-b border-slate-100 p-6 text-left min-w-[200px] shadow-sm">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Date \ Room Type</span>
                            </th>
                            @foreach($roomTypes as $roomType)
                                <th class="sticky top-0 z-20 bg-slate-50 border-b border-slate-100 p-6 text-center min-w-[140px] shadow-sm">
                                    <span class="block text-sm font-black text-slate-900 tracking-tight">{{ $roomType->name }}</span>
                                    <span class="block text-[8px] font-black text-slate-400 uppercase tracking-widest mt-1">Cap: {{ $roomType->total_rooms }}</span>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dates as $date)
                            @php $dateStr = $date->toDateString(); @endphp
                            <tr class="group hover:bg-slate-50 transition-colors">
                                <td class="sticky left-0 z-10 bg-white group-hover:bg-slate-50 border-r border-b border-slate-100 p-6 flex items-center justify-between shadow-[5px_0_15px_-5px_rgba(0,0,0,0.02)]">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-slate-900 leading-none">{{ $date->format('d M') }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $date->format('l') }}</span>
                                    </div>
                                    @if($date->isToday())
                                        <span class="px-2 py-0.5 bg-blue-100 text-blue-600 rounded text-[8px] font-black uppercase tracking-widest">Today</span>
                                    @endif
                                </td>
                                @foreach($roomTypes as $roomType)
                                    @php 
                                        $data = $matrix[$dateStr][$roomType->id];
                                        $isFull = $data['sold'] >= $data['total'];
                                        $isLow = $data['sold'] > 0 && $data['sold'] < $data['total'] / 2;
                                        $isHigh = $data['sold'] >= $data['total'] / 2 && $data['sold'] < $data['total'];
                                    @endphp
                                    <td class="border-b border-slate-100 p-6 text-center">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <div class="flex items-baseline gap-0.5">
                                                <span class="text-base font-black @if($isFull) text-red-600 @elseif($isHigh) text-amber-500 @elseif($isLow) text-emerald-500 @else text-slate-300 @endif">
                                                    {{ $data['sold'] }}
                                                </span>
                                                <span class="text-[10px] font-bold text-slate-300">/{{ $data['total'] }}</span>
                                            </div>
                                            
                                            {{-- PROGRESS MINI-BAR --}}
                                            <div class="w-12 h-1 bg-slate-100 rounded-full overflow-hidden">
                                                <div class="h-full @if($isFull) bg-red-500 @elseif($isHigh) bg-amber-400 @else bg-emerald-400 @endif"
                                                     style="width: {{ ($data['sold'] / $data['total']) * 100 }}%"></div>
                                            </div>

                                            @if($isFull)
                                                <span class="text-[8px] font-black text-red-500 uppercase tracking-widest">Sold Out</span>
                                            @elseif($data['sold'] == 0)
                                                <span class="text-[8px] font-black text-slate-300 uppercase tracking-widest italic">Vacant</span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- LEGEND --}}
        <div class="mt-8 flex items-center gap-8 px-6">
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sold Out</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-amber-400 rounded-full"></div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">High Occupancy</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-emerald-400 rounded-full"></div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Available</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-3 h-3 bg-slate-200 rounded-full"></div>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Zero Sales</span>
            </div>
        </div>

<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
@endsection
