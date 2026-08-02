@extends('layouts.admin')

@section('title', $isUpdate ? 'Manage Stay' : 'Smart Check-in')

@section('content')
<div class="h-[calc(100vh-6rem)] flex flex-col md:flex-row gap-6 p-2">
    {{-- LEFT: Booking Details --}}
    <div class="w-full md:w-1/3 bg-white border border-slate-200 rounded-2xl shadow-sm p-6 flex flex-col">
        <div class="mb-6">
            <span class="px-3 py-1 {{ $isUpdate ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }} rounded-full text-xs font-bold uppercase tracking-wide">
                {{ $isUpdate ? 'In House' : 'Check-in Pending' }}
            </span>
            <h1 class="mt-4 text-3xl font-black text-slate-800">{{ $booking->guest_name }}</h1>
            <p class="text-slate-500 font-medium">{{ $booking->guests->first()->phone ?? 'No Phone' }}</p>
        </div>

        <div class="space-y-4 flex-1">
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-slate-500 text-sm font-bold">Booking ID</span>
                <span class="text-slate-800 font-mono">#{{ $booking->id }}</span>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-slate-500 text-sm font-bold">Room Type</span>
                <span class="text-slate-800 font-bold">{{ $booking->roomType->name }}</span>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-slate-500 text-sm font-bold">Duration</span>
                <span class="text-slate-800 font-bold">{{ $booking->nights }} Nights</span>
            </div>
            <div class="flex justify-between border-b border-slate-100 pb-2">
                <span class="text-slate-500 text-sm font-bold">Dates</span>
                <span class="text-slate-800 text-right">
                    {{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }} 
                    &rarr; 
                    {{ \Carbon\Carbon::parse($booking->check_out)->format('d M') }}
                </span>
            </div>
             <div class="flex justify-between pt-2">
                <span class="text-slate-500 text-sm font-bold">Paid So Far</span>
                <span class="text-emerald-600 font-black">₹{{ number_format($booking->paid_amount) }}</span>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('admin.bookings.show', $booking->id) }}" target="_blank" class="block w-full text-center py-3 border border-slate-200 rounded-xl text-slate-600 font-bold hover:bg-slate-50">
                View Full Details
            </a>
        </div>
    </div>

    {{-- RIGHT: Actions --}}
    <div class="w-full md:w-2/3 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm p-8 flex flex-col" x-data="{ total_extras: 0 }">
        <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            {{ $isUpdate ? 'Manage Stay & Services' : 'Complete Check-in' }}
        </h2>

        <form action="{{ route('admin.bookings.process-check-in', $booking->id) }}" method="POST" class="flex-1 flex flex-col">
            @csrf

            {{-- 1. ROOM ASSIGNMENT --}}
            <div class="mb-8">
                <label class="block text-sm font-black text-slate-700 mb-2 uppercase tracking-wide">
                    {{ $isUpdate ? 'Move Room / Current Room' : 'Assign Room' }}
                </label>
                <div class="flex gap-4">
                    <div class="flex-1">
                        <select name="room_id" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-4 focus:ring-blue-100 font-bold text-slate-700">
                             @php $assignedId = $booking->assignedRooms->first()->id ?? null; @endphp
                             @foreach($availableRooms as $room)
                                <option value="{{ $room->id }}" {{ $assignedId == $room->id ? 'selected' : '' }}>
                                    Room {{ $room->room_number }} ({{ ucfirst($room->floor) }} Floor) 
                                    @if($assignedId == $room->id) (Current) @endif
                                    @if($room->housekeeping_status !== 'clean' && $assignedId != $room->id) - {{ ucfirst($room->housekeeping_status) }} ⚠️ @endif
                                </option>
                             @endforeach
                             @if($availableRooms->isEmpty())
                                <option disabled selected>No clean rooms available!</option>
                             @endif
                        </select>
                    </div>
                </div>
                @if($isUpdate)
                    <p class="text-xs text-amber-600 mt-2 font-bold">⚠️ Changing the room will mark the current room ({{ $booking->assignedRooms->first()->room_number ?? 'N/A' }}) as Dirty.</p>
                @else
                    <p class="text-xs text-slate-500 mt-2">Showing only available rooms for {{ $booking->roomType->name }}.</p>
                @endif
            </div>

            {{-- 2. UPSELLS / SERVICES --}}
            <div class="mb-8 flex-1">
                <label class="block text-sm font-black text-slate-700 mb-4 uppercase tracking-wide">Add Services</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($services as $service)
                        <div class="flex items-center justify-between p-4 bg-white border border-slate-200 rounded-xl hover:border-blue-400 transition cursor-pointer"
                             x-data="{ qty: 0, price: {{ $service->price }} }">
                            <div>
                                <h4 class="font-bold text-slate-700">{{ $service->name }}</h4>
                                <p class="text-xs text-slate-500">₹{{ number_format($service->price) }}</p>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button type="button" @click="if(qty > 0) { qty--; total_extras -= price; }" class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-200 font-bold">-</button>
                                <span class="w-6 text-center font-bold text-slate-800" x-text="qty"></span>
                                <input type="hidden" name="services[{{ $service->id }}]" x-model="qty">
                                <button type="button" @click="qty++; total_extras += price;" class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 hover:bg-blue-200 font-bold">+</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- 3. SUBMIT --}}
            <div class="mt-auto border-t border-slate-200 pt-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-slate-500 font-medium">Extras Total</span>
                    <span class="text-xl font-black text-slate-800">₹<span x-text="total_extras">0</span></span>
                </div>
                <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-black text-lg shadow-xl shadow-blue-500/20 transform hover:scale-[1.01] transition-all">
                    {{ $isUpdate ? 'Update Stay Details' : 'Confirm Check-in' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

