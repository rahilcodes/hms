@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.laundry.batches.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1 mb-2">
                &larr; Back to History
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Dispatch Laundry</h1>
            <p class="text-sm text-gray-500">Send dirty linen to an external vendor</p>
        </div>

        <form action="{{ route('admin.laundry.batches.store') }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Select Vendor</label>
                    <select name="vendor_id" required
                        class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Sent Date</label>
                        <input type="date" name="sent_date" value="{{ date('Y-m-d') }}" required
                            class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Expected Return</label>
                        <input type="date" name="expected_return_date" value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                            class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                Enter Quantities
            </h3>

            <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                @foreach($linens as $index => $linen)
                    <div
                        class="flex items-center justify-between p-4 bg-slate-50 border border-slate-100 rounded-xl hover:bg-white hover:border-blue-200 hover:shadow-sm transition group">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-lg font-black text-slate-300 group-hover:text-blue-500 group-hover:border-blue-200 transition">
                                {{ substr($linen->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">{{ $linen->name }}</p>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">{{ $linen->category }} •
                                    Stock: {{ $linen->total_stock }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <input type="hidden" name="items[{{ $index }}][linen_type_id]" value="{{ $linen->id }}">
                            <div
                                class="flex items-center gap-2 bg-white rounded-lg border border-slate-300 px-3 py-2 focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500">
                                <span class="text-xs font-bold text-slate-400 uppercase">Qty</span>
                                <input type="number" name="items[{{ $index }}][quantity]" value="0" min="0"
                                    class="w-16 text-right font-bold text-gray-800 outline-none border-none p-0 focus:ring-0">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-8 border-t border-gray-100 mt-8 flex justify-end">
                <button type="submit"
                    class="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                    Dispatch Batch
                </button>
            </div>
        </form>
    </div>
@endsection