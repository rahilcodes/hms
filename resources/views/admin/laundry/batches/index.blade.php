@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Laundry Batches</h1>
                <p class="text-sm text-gray-500 mt-1">Manage dispatch and return cycles</p>
            </div>

            <a href="{{ route('admin.laundry.batches.create') }}"
                class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Dispatch New Batch
            </a>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-slate-50 text-xs uppercase font-bold text-gray-500">
                    <tr>
                        <th class="px-6 py-4">Batch #</th>
                        <th class="px-6 py-4">Vendor</th>
                        <th class="px-6 py-4">Sent Date</th>
                        <th class="px-6 py-4">Items Sent</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($batches as $batch)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-mono font-bold text-blue-600">{{ $batch->batch_number }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $batch->vendor->name }}</td>
                            <td class="px-6 py-4">{{ $batch->sent_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="font-bold">{{ $batch->items->sum('quantity_sent') }}</span> items
                                <span class="text-xs text-slate-400">({{ $batch->items->count() }} types)</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($batch->status == 'out')
                                    <span
                                        class="px-2 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-bold uppercase tracking-widest border border-amber-100">Processing</span>
                                @elseif($batch->status == 'returned')
                                    <span
                                        class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-bold uppercase tracking-widest border border-emerald-100">
                                        returned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($batch->status == 'out')
                                    <a href="{{ route('admin.laundry.batches.edit', $batch) }}"
                                        class="text-xs font-bold uppercase tracking-widest text-blue-600 hover:text-blue-800 border border-blue-200 hover:bg-blue-50 px-3 py-1.5 rounded-lg transition">
                                        Receive
                                    </a>
                                @else
                                    <a href="{{ route('admin.laundry.batches.edit', $batch) }}"
                                        class="text-xs font-bold text-slate-400 hover:text-slate-600">View</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-400 italic">No batches found. Disptach your
                                first laundry batch.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @forelse($batches as $batch)
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <span
                                class="text-xs font-mono font-black text-blue-500 bg-blue-50 px-2 py-1 rounded">{{ $batch->batch_number }}</span>
                            <h3 class="font-bold text-slate-800 mt-2">{{ $batch->vendor->name }}</h3>
                            <p class="text-xs text-slate-400">Sent: {{ $batch->sent_date->format('d M Y') }}</p>
                        </div>
                        @if($batch->status == 'out')
                            <span
                                class="px-2 py-1 bg-amber-50 text-amber-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-amber-100">Processing</span>
                        @elseif($batch->status == 'returned')
                            <span
                                class="px-2 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-100">Returned</span>
                        @endif
                    </div>

                    <div class="flex items-center justify-between border-t border-slate-50 pt-4">
                        <div class="text-xs font-medium text-slate-600">
                            Items: <span class="font-black text-slate-900">{{ $batch->items->sum('quantity_sent') }}</span>
                        </div>

                        @if($batch->status == 'out')
                            <a href="{{ route('admin.laundry.batches.edit', $batch) }}"
                                class="w-full max-w-[120px] text-center text-xs font-black uppercase tracking-widest text-white bg-blue-600 hover:bg-blue-700 px-4 py-2.5 rounded-xl shadow-lg shadow-blue-200 transition">
                                Receive
                            </a>
                        @else
                            <a href="{{ route('admin.laundry.batches.edit', $batch) }}"
                                class="text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest">View
                                Details</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-slate-400 text-sm font-bold">No batches found.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection