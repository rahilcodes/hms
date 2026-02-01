@extends('layouts.admin')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Room Types</h1>
            <p class="text-sm text-gray-500">Manage your hotel's inventory and pricing.</p>
        </div>
        <a href="{{ route('admin.room-types.create') }}"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium shadow-sm transition">
            + Add Room Type
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roomTypes as $room)
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col group hover:shadow-md transition">
                {{-- IMAGE --}}
                <div class="h-48 w-full bg-gray-200 relative">
                    @if($room->image)
                        <img src="{{ asset('storage/' . $room->image) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                    <div class="absolute top-3 right-3 bg-white px-2 py-1 rounded text-xs font-bold shadow text-gray-800">
                        {{ $room->total_rooms }} Units
                    </div>
                </div>

                {{-- CONTENT --}}
                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-gray-900 leading-tight">{{ $room->name }}</h3>
                        <div class="text-right">
                            <span class="text-lg font-bold text-blue-600 block">₹{{ number_format($room->base_price) }}</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Base Rate</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mb-4 text-[10px] font-bold text-slate-500 uppercase tracking-widest">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>{{ $room->base_occupancy }} Adults</span>
                        </div>
                        @if($room->max_extra_persons > 0)
                            <div class="flex items-center gap-1.5 text-blue-600">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span>{{ $room->max_extra_persons }} Extra + ₹{{ number_format($room->extra_person_price) }}</span>
                            </div>
                        @endif
                    </div>

                    <p class="text-sm text-gray-500 line-clamp-2 mb-4">
                        {{ $room->description ?? 'No description provided.' }}
                    </p>

                    @if($room->amenities)
                        <div class="flex flex-wrap gap-1 mb-4">
                            @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-600">{{ $amenity }}</span>
                            @endforeach
                            @if(count($room->amenities) > 3)
                                <span class="px-2 py-0.5 rounded text-xs bg-gray-50 text-gray-400">+{{ count($room->amenities) - 3 }}
                                    more</span>
                            @endif
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-100 flex gap-2">
                        <a href="{{ route('admin.room-types.edit', $room) }}"
                            class="flex-1 px-4 py-2 text-center bg-gray-50 border border-gray-300 rounded hover:bg-white hover:border-gray-400 text-gray-700 text-sm font-medium transition">
                            Edit Details
                        </a>
                        {{-- Delete functionality could be added here if needed, but not requested explicitly --}}
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div
                    class="flex flex-col items-center justify-center text-center p-12 bg-white rounded-xl border border-dashed border-gray-300">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900">No Room Types Defined</h3>
                    <p class="text-gray-500 mb-6">Start by creating your first room category.</p>
                    <a href="{{ route('admin.room-types.create') }}"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                        Create Room Type
                    </a>
                </div>
            </div>
        @endforelse
    </div>

@endsection