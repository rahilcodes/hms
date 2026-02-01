@extends('layouts.admin')

@section('header_title')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.lost-found.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-slate-900 leading-tight">Manage Item #{{ $lost_found_item->id }}</h1>
            <p class="text-xs text-slate-500 font-medium whitespace-nowrap">Edit Details</p>
        </div>
    </div>
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- MAIN FORM --}}
        <div class="lg:col-span-2">
            <form action="{{ route('admin.lost-found.update', ['lost_found_item' => $lost_found_item->id]) }}" method="POST"
                enctype="multipart/form-data" class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm"
                x-data="{ status: '{{ $lost_found_item->status }}' }">
                @csrf
                @method('PUT')

                <div class="p-8 space-y-8">

                    {{-- STATUS BAR --}}
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-2">Item
                            Status</label>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['found', 'claimed', 'disposed', 'donated'] as $s)
                                <label class="cursor-pointer">
                                    <input type="radio" name="status" value="{{ $s }}" x-model="status" class="sr-only peer">
                                    <div
                                        class="px-2 py-2 text-center rounded-xl text-xs font-bold border-2 transition-all 
                                                                    peer-checked:border-amber-500 peer-checked:bg-amber-50 peer-checked:text-amber-700
                                                                    border-transparent bg-white text-slate-500 hover:bg-white hover:border-slate-200 shadow-sm">
                                        {{ ucfirst($s) }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- CLAIMANT DETAILS (Conditional) --}}
                    <div x-show="status === 'claimed'" x-transition
                        class="p-6 bg-emerald-50 rounded-2xl border border-emerald-100 space-y-4">
                        <h4 class="text-sm font-black text-emerald-800 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Claimant Details
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-emerald-600/70 uppercase tracking-widest ml-1 mb-1">Claimed
                                    By (Name)</label>
                                <input type="text" name="claimed_by_name"
                                    value="{{ old('claimed_by_name', $lost_found_item->claimed_by_name) }}"
                                    class="w-full px-4 py-3 bg-white border border-emerald-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-emerald-600/70 uppercase tracking-widest ml-1 mb-1">Date
                                    of Claim</label>
                                <input type="datetime-local" name="claimed_date"
                                    value="{{ old('claimed_date', $lost_found_item->claimed_date?->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i')) }}"
                                    class="w-full px-4 py-3 bg-white border border-emerald-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition outline-none">
                            </div>
                        </div>
                    </div>

                    {{-- DETAILS --}}
                    <div class="space-y-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Item
                                Name</label>
                            <input type="text" name="item_name" value="{{ old('item_name', $lost_found_item->item_name) }}"
                                required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Category</label>
                                <select name="category" required
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">
                                    @foreach(['electronics', 'clothing', 'documents', 'valuables', 'others'] as $cat)
                                        <option value="{{ $cat }}" {{ $lost_found_item->category === $cat ? 'selected' : '' }}>
                                            {{ ucfirst($cat) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Found
                                    Location</label>
                                <input type="text" name="found_location"
                                    value="{{ old('found_location', $lost_found_item->found_location) }}" required
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">{{ old('description', $lost_found_item->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
                    <button type="button"
                        onclick="if(confirm('Delete this item record?')) document.getElementById('delete-form').submit()"
                        class="text-rose-500 font-bold text-xs hover:text-rose-700 transition">Delete Record</button>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.lost-found.index') }}"
                            class="px-6 py-3 text-slate-500 hover:text-slate-800 font-bold text-sm transition">Cancel</a>
                        <button type="submit"
                            class="px-8 py-3 bg-amber-500 text-white rounded-xl font-black text-sm shadow-xl shadow-amber-200 hover:bg-amber-600 hover:-translate-y-0.5 transition transform">
                            Update Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- SIDEBAR INFO --}}
        <div class="space-y-6">
            {{-- IMAGE CARD --}}
            <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm p-2">
                @if($lost_found_item->image_path)
                    <img src="{{ asset('storage/' . $lost_found_item->image_path) }}" class="w-full rounded-2xl">
                @else
                    <div class="w-full aspect-video bg-slate-50 rounded-2xl flex items-center justify-center text-slate-300">
                        <span class="text-xs font-bold">No Image</span>
                    </div>
                @endif
            </div>

            {{-- METADATA --}}
            <div class="bg-white rounded-3xl border border-slate-200 p-6 shadow-sm">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-4">Meta Information</h4>
                <div class="space-y-4">
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Found By</span>
                        <span class="font-bold text-slate-900">{{ $lost_found_item->foundBy->name ?? 'Unknown' }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500">Found Date</span>
                        <span class="font-bold text-slate-900">{{ $lost_found_item->found_date->format('M d, Y') }}</span>
                    </div>
                    @if($lost_found_item->room)
                        <div class="flex justify-between text-xs pt-4 border-t border-slate-100">
                            <span class="text-slate-500">Room</span>
                            <a href="#" class="font-bold text-blue-600">{{ $lost_found_item->room->room_number }}</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <form id="delete-form" action="{{ route('admin.lost-found.destroy', ['lost_found_item' => $lost_found_item->id]) }}"
        method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

@endsection