@extends('layouts.admin')

@section('content')

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.room-types.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1 mb-2">
                &larr; Back to list
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Add New Room Type</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="{{ route('admin.room-types.store') }}" method="POST" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                {{-- BASIC INFO --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Room Name</label>
                        <input type="text" name="name" placeholder="e.g. Deluxe Suite" required
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Base Price per Night (₹)</label>
                        <input type="number" name="base_price" placeholder="4500" required min="0" step="0.01"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Rooms Available</label>
                        <input type="number" name="total_rooms" value="5" required min="1"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Base Prep (Adults)</label>
                        <input type="number" name="base_occupancy" value="2" required min="1"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Max Extra Persons</label>
                        <input type="number" name="max_extra_persons" value="0" required min="0"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Extra Person Price (₹)</label>
                    <input type="number" name="extra_person_price" value="0" required min="0" step="0.01"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Additional charge per extra guest per night.</p>
                </div>

                {{-- DETAILS --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" placeholder="Describe the room features, view, and ambiance..."
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                {{-- IMAGE MANAGEMENT --}}
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Featured Image (Main Layouts)</label>
                        <div
                            class="border-2 border-dashed border-slate-200 rounded-2xl p-6 flex flex-col items-center justify-center text-center bg-slate-50 hover:bg-white hover:border-blue-400 transition cursor-pointer relative">
                            <input type="file" name="image" accept="image/*"
                                class="absolute inset-0 opacity-0 cursor-pointer">
                            <svg class="w-8 h-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Click to Upload
                                Featured</p>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <label class="block text-sm font-semibold text-gray-700">Gallery Photos</label>
                            <span
                                class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100 italic">Recommended:
                                1920x1080 • WebP/JPG</span>
                        </div>
                        <div
                            class="border-2 border-dashed border-slate-200 rounded-2xl p-8 flex flex-col items-center justify-center text-center bg-slate-50 hover:bg-white hover:border-blue-400 transition cursor-pointer relative">
                            <input type="file" name="gallery[]" multiple accept="image/*"
                                class="absolute inset-0 opacity-0 cursor-pointer">
                            <svg class="w-10 h-10 text-slate-400 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            <p class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Upload Multiple
                                Gallery Photos</p>
                        </div>
                    </div>
                </div>

                {{-- AMENITIES --}}
                <div class="pt-6 border-t border-slate-100">
                    <div class="flex items-center justify-between mb-6">
                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Global Amenities
                            Checklist</label>
                        <span
                            class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded tracking-tighter uppercase italic">40+
                            Premium Options Available</span>
                    </div>

                    @php
                        $amenityGroups = [
                            'Essentials' => ['Free WiFi', 'Air Conditioning', 'Heating', 'Soundproofing', 'Desk', 'Safe', 'Ironing Facilities'],
                            'Entertainment' => ['Smart TV', 'Satellite Channels', 'Netflix Access', 'Bluetooth Speakers', 'Gaming Console', 'Newspapers'],
                            'In-Room Comfort' => ['Minibar', 'Coffee Machine', 'Tea Maker', 'Bottled Water', 'Kitchenette', 'Dining Area', 'Balcony', 'Terrace'],
                            'Luxury & Wellness' => ['Private Pool', 'Jacuzzi', 'Hot Tub', 'Sauna Access', 'Bathrobes', 'Slippers', 'Premium Toiletries', 'Pillow Menu'],
                            'Services' => ['Room Service', 'Wake-up Service', 'Laundry Service', 'Butler Service', 'Concierge Access', 'Daily Housekeeping'],
                            'Views & Outdoors' => ['City View', 'Ocean View', 'Garden View', 'Mountain View', 'Pool View', 'Patio']
                        ];
                    @endphp

                    <div class="space-y-8">
                        @foreach($amenityGroups as $group => $options)
                            <div>
                                <h4
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 flex items-center gap-3">
                                    {{ $group }}
                                    <div class="h-px bg-slate-100 flex-1"></div>
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @foreach($options as $amenity)
                                        <label
                                            class="group flex items-center gap-3 p-3 bg-slate-50 border border-slate-100 rounded-xl cursor-pointer hover:bg-white hover:border-blue-400 hover:shadow-sm transition-all duration-300">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}"
                                                    class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 transition cursor-pointer">
                                            </div>
                                            <span
                                                class="text-xs font-bold text-slate-600 group-hover:text-slate-900 transition-colors">{{ $amenity }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="px-8 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-sm hover:bg-blue-700 transition">
                        Create Room Type
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection