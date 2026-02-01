@extends('layouts.admin')

@section('header_title')
    <div class="flex items-center gap-3">
        <div class="p-2 bg-amber-500 rounded-lg text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-900 leading-tight">Lost & Found</h1>
            <p class="text-xs text-slate-500 font-medium">Registry</p>
        </div>
    </div>
@endsection

@section('content')

    {{-- FILTERS --}}
    <div class="mb-6 flex flex-col md:flex-row gap-4 justify-between items-center">
        <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0">
            <a href="{{ route('admin.lost-found.index') }}"
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ !request('status') ? 'bg-slate-900 text-white shadow-lg shadow-slate-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                All Items
            </a>
            <a href="{{ route('admin.lost-found.index', ['status' => 'found']) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ request('status') === 'found' ? 'bg-amber-500 text-white shadow-lg shadow-amber-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Found
            </a>
            <a href="{{ route('admin.lost-found.index', ['status' => 'claimed']) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ request('status') === 'claimed' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Claimed
            </a>
            <a href="{{ route('admin.lost-found.index', ['status' => 'disposed']) }}"
                class="px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition {{ request('status') === 'disposed' ? 'bg-slate-500 text-white shadow-lg shadow-slate-200' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
                Disposed
            </a>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.lost-found.index') }}" method="GET" class="relative group w-full md:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search items..."
                    class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 group-focus-within:text-amber-500 transition"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
            <a href="{{ route('admin.lost-found.create') }}"
                class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white rounded-xl text-xs font-black hover:bg-amber-600 transition shadow-lg shadow-amber-200 flex-shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Log Item
            </a>
        </div>
    </div>

    {{-- GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($items as $item)
            <div
                class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition group">
                <div class="relative h-48 bg-slate-100 flex items-center justify-center overflow-hidden">
                    @if($item->image_path)
                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->item_name }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                    @else
                        <svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    @endif

                    {{-- Status Badge --}}
                    <div class="absolute top-3 right-3 px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-wide backdrop-blur-md shadow-sm border border-white/20
                                        {{ $item->status === 'found' ? 'bg-amber-500/90 text-white' : '' }}
                                        {{ $item->status === 'claimed' ? 'bg-emerald-500/90 text-white' : '' }}
                                        {{ $item->status === 'disposed' ? 'bg-slate-500/90 text-white' : '' }}">
                        {{ ucfirst($item->status) }}
                    </div>
                </div>

                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $item->category }}</p>
                            <h3 class="font-bold text-slate-900 group-hover:text-amber-600 transition">{{ $item->item_name }}
                            </h3>
                        </div>
                    </div>

                    <div class="space-y-2 mt-4">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-semibold">{{ $item->found_location }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium">{{ $item->found_date->format('M d, h:i A') }}</span>
                        </div>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('admin.lost-found.edit', ['lost_found_item' => $item->id]) }}"
                            class="text-xs font-bold text-amber-600 hover:text-amber-700 transition">Manage Item &rarr;</a>
                        @if($item->foundBy)
                            <div class="flex -space-x-2">
                                <div class="w-6 h-6 rounded-full bg-slate-200 border-2 border-white flex items-center justify-center text-[8px] font-bold text-slate-500"
                                    title="Found by {{ $item->foundBy->name }}">
                                    {{ substr($item->foundBy->name, 0, 1) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-dashed border-slate-300">
                <svg class="w-12 h-12 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="text-sm font-bold text-slate-900">No Items Found</h3>
                <p class="text-xs text-slate-500 mt-1">Registry is clean.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $items->links() }}
    </div>

@endsection