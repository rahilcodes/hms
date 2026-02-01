@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.laundry.batches.index') }}"
                class="text-sm text-gray-500 hover:text-gray-800 flex items-center gap-1 mb-2">
                &larr; Back to History
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-800">Receive Batch #{{ $batch->batch_number }}</h1>
                <span
                    class="px-2 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-bold uppercase tracking-widest border border-amber-100">Processing</span>
            </div>
            <p class="text-sm text-gray-500 mt-1">Sent to <strong>{{ $batch->vendor->name }}</strong> on
                {{ $batch->sent_date->format('d M, Y') }}</p>
        </div>

        <form action="{{ route('admin.laundry.batches.update', $batch) }}" method="POST"
            class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:p-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="receive_items" value="1">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Received Date</label>
                    <input type="date" name="received_date" value="{{ date('Y-m-d') }}" required
                        class="w-full p-2.5 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Total Cost (Bill Amount)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-slate-400 font-bold">₹</span>
                        <input type="number" name="total_cost" step="0.01" min="0" placeholder="0.00"
                            class="w-full pl-8 p-2.5 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto border border-slate-200 rounded-xl mb-8">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Item</th>
                            <th class="px-6 py-4 text-center w-32">Qty Sent</th>
                            <th class="px-6 py-4 w-40">Received</th>
                            <th class="px-6 py-4 w-40">Rejected/Lost</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($batch->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800">{{ $item->linenType->name }}</p>
                                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                                </td>
                                <td class="px-6 py-4 text-center font-mono font-bold">{{ $item->quantity_sent }}</td>
                                <td class="px-6 py-4">
                                    <input type="number" name="items[{{ $index }}][quantity_received]"
                                        value="{{ $item->quantity_sent }}" max="{{ $item->quantity_sent }}" min="0"
                                        class="w-full p-2 border border-slate-300 rounded text-center font-bold text-gray-800 focus:ring-blue-500 focus:border-blue-500 js-received">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="items[{{ $index }}][quantity_rejected]" value="0" min="0"
                                        class="w-full p-2 border border-slate-300 rounded text-center text-rose-600 font-bold focus:ring-rose-500 focus:border-rose-500 js-rejected bg-rose-50">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit"
                    class="px-8 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-200 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Confirm Receipt & Audit
                </button>
            </div>
        </form>
    </div>

    <script>
        // Simple auto-calc: if user changes rejected, reduce received automatically? 
        // Or just validate? Let's keep it simple for now and trust user input, 
        // but maybe warn if Received + Rejected != Sent.

        document.querySelectorAll('.js-rejected').forEach(input => {
            input.addEventListener('input', function () {
                const row = this.closest('tr');
                const sent = parseInt(row.querySelector('td:nth-child(2)').innerText);
                const receivedInput = row.querySelector('.js-received');
                const rejected = parseInt(this.value) || 0;

                // Auto adjust received if possible
                if (sent - rejected >= 0) {
                    receivedInput.value = sent - rejected;
                }
            });
        });
    </script>
@endsection