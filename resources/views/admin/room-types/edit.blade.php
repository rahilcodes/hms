@extends('layouts.admin')

@section('content')

    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.room-types.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1 mb-2">
                &larr; Back to list
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Edit Room Type</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="{{ route('admin.room-types.update', $roomType) }}" method="POST" enctype="multipart/form-data"
                class="space-y-6"
                x-data="{ 
                    totalRooms: {{ $roomType->total_rooms ?? 0 }},
                    existingRooms: {{ $roomType->rooms->pluck('room_number') }},
                    getRoomValue(index) { return this.existingRooms[index] || ''; },
                    autoFill() {
                        if (!this.totalRooms) return;
                        let start = 101;
                        const first = document.querySelector('input[name=\'room_numbers[]\']');
                        if (first && first.value.match(/\d+/)) start = parseInt(first.value.match(/\d+/)[0]);
                        document.querySelectorAll('input[name=\'room_numbers[]\']').forEach((el, i) => {
                            if (!el.value) el.value = start + i;
                        });
                    }
                }"
            >
                @csrf
                @method('PUT')

                {{-- BASIC INFO --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Room Name</label>
                        <input type="text" name="name" value="{{ $roomType->name }}" required
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Base Price per Night (₹)</label>
                        <input type="number" name="base_price" value="{{ $roomType->base_price }}" required min="0"
                            step="0.01"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Rooms Available</label>
                        <input type="number" name="total_rooms" x-model="totalRooms" required min="1"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Base Prep (Adults)</label>
                        <input type="number" name="base_occupancy" value="{{ $roomType->base_occupancy }}" required min="1"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Max Extra Persons</label>
                        <input type="number" name="max_extra_persons" value="{{ $roomType->max_extra_persons }}" required
                            min="0"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Extra Person Price (₹)</label>
                    <input type="number" name="extra_person_price" value="{{ $roomType->extra_person_price }}" required
                        min="0" step="0.01"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Additional charge per extra guest per night.</p>
                </div>

                {{-- DETAILS --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">{{ $roomType->description }}</textarea>
                </div>

                {{-- IMAGE MANAGEMENT --}}
                <div class="space-y-6">
                    {{-- MAIN IMAGE --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Featured Image (Main Layouts)</label>
                        @if($roomType->image)
                            <div class="relative group w-48 mb-3">
                                <img src="{{ asset('storage/' . $roomType->image) }}" class="h-40 w-full object-cover rounded-xl border-2 border-slate-100 shadow-sm">
                                <button type="button" 
                                        onclick="removeRoomImage('main', '{{ $roomType->image }}')"
                                        class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition p-1.5 bg-white/90 backdrop-blur rounded-lg text-rose-600 shadow-xl hover:scale-110">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @endif
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

                    {{-- GALLERY --}}
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700">Gallery Photos</label>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Drag to rearrange
                                    order</p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100 italic">Recommended:
                                    1920x1080 • WebP/JPG</span>
                            </div>
                        </div>

                        <div id="image-gallery" class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach($roomType->gallery_json ?? [] as $path)
                                <div class="relative group draggable shadow-sm rounded-xl overflow-hidden border border-slate-100"
                                    data-path="{{ $path }}">
                                    <img src="{{ asset('storage/' . $path) }}" class="h-32 w-full object-cover">
                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                        <button type="button" 
                                                onclick="removeRoomImage('gallery', '{{ $path }}')"
                                                class="p-2 bg-white rounded-xl text-rose-600 shadow-xl hover:scale-110 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                    <div
                                        class="absolute top-2 left-2 p-1 bg-white/50 backdrop-blur rounded cursor-move md:opacity-0 group-hover:opacity-100 transition">
                                        <svg class="w-3 h-3 text-slate-700" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M7 2a2 2 0 10.001 4.001A2 2 0 007 2zm0 6a2 2 0 10.001 4.001A2 2 0 007 8zm0 6a2 2 0 10.001 4.001A2 2 0 007 14zm6-12a2 2 0 10.001 4.001A2 2 0 0013 2zm0 6a2 2 0 10.001 4.001A2 2 0 0013 8zm0 6a2 2 0 10.001 4.001A2 2 0 0013 14z">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                            @endforeach

                            <div
                                class="border-2 border-dashed border-slate-200 rounded-xl p-4 flex flex-col items-center justify-center text-center bg-slate-50 hover:bg-white hover:border-blue-400 transition cursor-pointer relative min-h-[128px]">
                                <input type="file" name="gallery[]" multiple accept="image/*"
                                    class="absolute inset-0 opacity-0 cursor-pointer">
                                <svg class="w-6 h-6 text-slate-400 mb-1" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Add
                                    Photos</span>
                            </div>
                        </div>
                    </div>
                </div>



                <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
                <script>
                    function removeRoomImage(type, path) {
                        if (confirm('Are you sure you want to remove this image?')) {
                            document.getElementById('remove-type').value = type;
                            document.getElementById('remove-path').value = path;
                            document.getElementById('remove-image-form').submit();
                        }
                    }

                    // FILE SELECTION PREVIEW
                    document.querySelectorAll('input[type="file"]').forEach(input => {
                        input.addEventListener('change', function(e) {
                            const files = e.target.files;
                            const container = e.target.parentElement;
                            let info = container.querySelector('.file-info');
                            
                            if (!info) {
                                info = document.createElement('div');
                                info.className = 'file-info mt-2 px-3 py-1 bg-blue-600 text-white text-[10px] font-bold rounded-lg uppercase tracking-widest';
                                container.appendChild(info);
                            }

                            if (files.length > 0) {
                                info.textContent = files.length === 1 ? files[0].name : files.length + ' Photos Selected';
                            } else {
                                info.remove();
                            }
                        });
                    });

                    document.addEventListener('DOMContentLoaded', function () {
                        const el = document.getElementById('image-gallery');
                        if (el) {
                            Sortable.create(el, {
                                animation: 150,
                                draggable: ".draggable",
                                onEnd: function () {
                                    const images = [];
                                    el.querySelectorAll('.draggable').forEach(item => {
                                        images.push(item.dataset.path);
                                    });

                                    fetch("{{ route('admin.room-types.reorder-images', $roomType) }}", {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ images: images })
                                    });
                                }
                            });
                        }
                    });
                </script>

                {{-- ROOM IDENTIFICATION (Alpine.js Version) --}}
                {{-- ROOM IDENTIFICATION (Alpine.js Version) --}}
                <div class="pt-10 border-t border-slate-100 mt-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Room Identification</label>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Assign specfic room numbers (e.g., 101, 205)</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" @click="autoFill()" class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-bold uppercase tracking-widest border border-slate-200 hover:bg-slate-200 transition">
                                Auto-Fill Serial
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
                        <template x-if="totalRooms > 0">
                            <template x-for="i in parseInt(totalRooms)" :key="i">
                                <div>
                                    <input type="text" name="room_numbers[]" 
                                           :value="getRoomValue(i-1)" 
                                           :placeholder="'Room ' + i"
                                           class="w-full text-center font-bold text-slate-700 bg-white border border-slate-200 rounded-lg py-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all"
                                    >
                                </div>
                            </template>
                        </template>
                        <template x-if="!totalRooms || totalRooms == 0">
                            <div class="col-span-3 md:col-span-5 text-center text-slate-400 text-xs italic py-4">
                                Enter "Total Rooms Available" above to generate room slots.
                            </div>
                        </template>
                    </div>
                </div>

                {{-- AMENITIES (Existing) --}}
                <div class="pt-10 border-t border-slate-100 mt-10">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase tracking-widest">Global Amenities Checklist</label>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Select all features available for this room type</p>
                        </div>
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100 italic">40+ Premium Options</span>
                    </div>

                    @php
                        $currentAmenities = $roomType->amenities ?? [];
                        $amenityGroups = [
                            'Essentials' => ['Free WiFi', 'Air Conditioning', 'Heating', 'Soundproofing', 'Desk', 'Safe', 'Ironing Facilities'],
                            'Entertainment' => ['Smart TV', 'Satellite Channels', 'Netflix Access', 'Bluetooth Speakers', 'Gaming Console', 'Newspapers'],
                            'In-Room Comfort' => ['Minibar', 'Coffee Machine', 'Tea Maker', 'Bottled Water', 'Kitchenette', 'Dining Area', 'Balcony', 'Terrace'],
                            'Luxury & Wellness' => ['Private Pool', 'Jacuzzi', 'Hot Tub', 'Sauna Access', 'Bathrobes', 'Slippers', 'Premium Toiletries', 'Pillow Menu'],
                            'Services' => ['Room Service', 'Wake-up Service', 'Laundry Service', 'Butler Service', 'Concierge Access', 'Daily Housekeeping'],
                            'Views & Outdoors' => ['City View', 'Ocean View', 'Garden View', 'Mountain View', 'Pool View', 'Patio']
                        ];
                    @endphp

                    <div class="space-y-10">
                        @foreach($amenityGroups as $group => $options)
                            <div>
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-5 flex items-center gap-4">
                                    {{ $group }}
                                    <div class="h-px bg-slate-100 flex-1"></div>
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($options as $amenity)
                                        <label class="group flex items-center gap-3 p-3.5 bg-slate-50 border border-slate-100 rounded-2xl cursor-pointer hover:bg-white hover:border-blue-400 hover:shadow-[0_10px_20px_-10px_rgba(59,130,246,0.15)] transition-all duration-300">
                                            <div class="relative flex items-center">
                                                <input type="checkbox" name="amenities[]" value="{{ $amenity }}" 
                                                    {{ in_array($amenity, $currentAmenities) ? 'checked' : '' }}
                                                    class="w-5 h-5 text-blue-600 border-slate-300 rounded-lg focus:ring-blue-500 transition cursor-pointer">
                                            </div>
                                            <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900 transition-colors">{{ $amenity }}</span>
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
                        Update Room Type
                    </button>
                </div>
            </form>

            {{-- GLOBAL DELETE FORM --}}
            <form id="remove-image-form" action="{{ route('admin.room-types.remove-image', $roomType) }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="type" id="remove-type">
                <input type="hidden" name="path" id="remove-path">
            </form>
        </div>
    </div>

@endsection