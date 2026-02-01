@extends('layouts.admin')

@section('content')

    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.pricing-rules.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1 mb-2">
                &larr; Back to list
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Add Pricing Rule</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8" x-data="{ ruleType: 'weekend' }">
            <form action="{{ route('admin.pricing-rules.store') }}" method="POST" class="space-y-6">
                @csrf

                {{-- ROOM TYPE --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Room Type</label>
                    <select name="room_type_id"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                        @foreach($roomTypes as $type)
                            <option value="{{ $type->id }}">
                                {{ $type->name }} (Base: ₹{{ number_format($type->base_price) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- RULE TYPE --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Rule Type</label>
                    <div class="flex gap-4">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="weekend" x-model="ruleType" class="peer sr-only">
                            <div
                                class="p-4 border rounded-lg text-center bg-gray-50 peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 transition h-full flex flex-col items-center justify-center">
                                <span class="font-bold block">Weekend</span>
                                <span class="text-xs text-gray-500">Every Fri & Sat</span>
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="type" value="season" x-model="ruleType" class="peer sr-only">
                            <div
                                class="p-4 border rounded-lg text-center bg-gray-50 peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 transition h-full flex flex-col items-center justify-center">
                                <span class="font-bold block">Seasonal</span>
                                <span class="text-xs text-gray-500">Custom Date Range</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- DATE RANGE (Only for Season) --}}
                <div x-show="ruleType === 'season'" x-transition class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                {{-- PRICE --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">New Price per Night (₹)</label>
                    <input type="number" name="price" placeholder="e.g. 5500" required min="0" step="0.01"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">This price will override the base price for the matching dates.
                    </p>
                </div>

                {{-- SUBMIT --}}
                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                        class="px-8 py-2.5 bg-blue-600 text-white font-semibold rounded-lg shadow-sm hover:bg-blue-700 transition">
                        Save Rule
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection