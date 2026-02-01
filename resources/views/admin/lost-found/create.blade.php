@extends('layouts.admin')

@section('header_title')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.lost-found.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <div>
            <h1 class="text-lg font-bold text-slate-900 leading-tight">Log Found Item</h1>
            <p class="text-xs text-slate-500 font-medium">New Entry</p>
        </div>
    </div>
@endsection

@section('content')

    <form action="{{ route('admin.lost-found.store') }}" method="POST" enctype="multipart/form-data"
        class="max-w-2xl mx-auto bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
        @csrf

        <div class="p-8 space-y-8">
            {{-- ITEM DETAILS --}}
            <div>
                <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    Item Information
                </h3>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Item
                            Name / Title</label>
                        <input type="text" name="item_name" value="{{ old('item_name') }}" required
                            placeholder="e.g. Black Leather Wallet, iPhone 14, Blue Jacket"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Category</label>
                            <select name="category" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">
                                <option value="electronics">Electronics</option>
                                <option value="clothing">Clothing</option>
                                <option value="documents">Documents (ID/Passport)</option>
                                <option value="valuables">Valuables (Jewelry/Cash)</option>
                                <option value="others" selected>Others</option>
                            </select>
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Found
                                Date & Time</label>
                            <input type="datetime-local" name="found_date"
                                value="{{ old('found_date', now()->format('Y-m-d\TH:i')) }}" required
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            placeholder="Detailed description of the item (color, brand, distinguishing marks)..."
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- LOCATION --}}
            <div>
                <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </span>
                    Found Location
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Specific
                            Location</label>
                        <input type="text" name="found_location" value="{{ old('found_location') }}" required
                            placeholder="e.g. Under Bed, Lobby Sofa, Poolside"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none">
                    </div>
                    <div>
                        <label
                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1 mb-1">Associated
                            Room (Optional)</label>
                        <div class="relative">
                            <select name="room_id"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500 transition outline-none appearance-none">
                                <option value="">-- General Area --</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">Room {{ $room->room_number }}</option>
                                @endforeach
                            </select>
                            <svg class="w-5 h-5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- IMAGE --}}
            <div>
                <h3 class="text-lg font-black text-slate-900 mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-pink-100 text-pink-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                    Photo Evidence
                </h3>

                <div x-data="{ filename: null }" class="relative group">
                    <input type="file" name="image" id="file-upload" class="hidden" accept="image/*"
                        @change="filename = $event.target.files[0].name">

                    <label for="file-upload"
                        class="cursor-pointer block w-full border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center hover:border-amber-500 hover:bg-amber-50/50 transition duration-200">
                        <div class="space-y-2">
                            <svg class="mx-auto h-12 w-12 text-slate-300 group-hover:text-amber-500 transition"
                                stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-slate-600 justify-center">
                                <span
                                    class="font-bold text-amber-600 rounded-md focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-amber-500">Upload
                                    a file</span>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-slate-400 font-medium">PNG, JPG, GIF up to 5MB</p>
                        </div>
                    </label>

                    <div x-show="filename" x-cloak
                        class="mt-2 flex items-center gap-2 text-sm font-bold text-emerald-600 bg-emerald-50 px-3 py-2 rounded-lg border border-emerald-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-text="filename"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.lost-found.index') }}"
                class="px-6 py-3 text-slate-500 hover:text-slate-800 font-bold text-sm transition">Cancel</a>
            <button type="submit"
                class="px-8 py-3 bg-amber-500 text-white rounded-xl font-black text-sm shadow-xl shadow-amber-200 hover:bg-amber-600 hover:-translate-y-0.5 transition transform">
                Save Item
            </button>
        </div>
    </form>

@endsection