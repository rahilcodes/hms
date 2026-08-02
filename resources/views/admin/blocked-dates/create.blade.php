@extends('layouts.admin')

@section('content')

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.blocked-dates.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1 mb-2">
                &larr; Back to list
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Block Dates</h1>
            <p class="text-sm text-gray-500">Prevent bookings for specific rooms on specific dates.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            <form action="{{ route('admin.blocked-dates.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- ROOM TYPE --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Select Room Type</label>
                    <select name="room_type_id"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->name }} (Total: {{ $type->total_rooms }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- DATES --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">From Date</label>
                        <input type="date" name="from_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">To Date</label>
                        <input type="date" name="to_date" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- QUANTITY --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Number of Rooms to Block</label>
                    <input type="number" name="blocked_rooms" value="1" min="1"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">
                        If you block 5 rooms, booking availability will decrease by 5 for the selected dates.
                    </p>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-red-600 text-white font-semibold rounded-lg shadow-sm hover:bg-red-700 transition">
                        Block Dates
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection