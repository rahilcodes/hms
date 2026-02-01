@extends('layouts.admin')

@section('header_title', 'Room Service Orders')

@section('content')
    <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h3 class="font-black text-slate-900 text-lg">Active Orders</h3>
                <span
                    class="px-3 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest">Live
                    Updates</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50">
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Order ID
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Guest /
                                Room</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Items</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status
                            </th>
                            <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($orders as $order)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-8 py-6">
                                    <p class="font-black text-slate-900">#{{ $order->id }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">
                                        {{ $order->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="font-bold text-slate-900">{{ $order->booking->guest_name }}</p>
                                    <p class="text-[10px] text-blue-600 font-black uppercase tracking-widest">
                                        {{ $order->booking->roomType->name }} • Suite #{{ $order->booking->id + 100 }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="space-y-1">
                                        @foreach($order->items as $item)
                                            <p class="text-xs font-medium text-slate-600">
                                                <span class="font-black text-slate-900">{{ $item['qty'] }}x</span>
                                                {{ $item['name'] }}
                                            </p>
                                        @endforeach
                                        @if($order->notes)
                                            <p class="text-[10px] text-rose-500 font-bold italic mt-2">Note: {{ $order->notes }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="font-black text-slate-900">₹{{ number_format($order->total_amount) }}</p>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest 
                                            @if($order->status === 'pending') bg-slate-100 text-slate-500
                                            @elseif($order->status === 'confirmed') bg-blue-100 text-blue-600
                                            @elseif($order->status === 'preparing') bg-amber-100 text-amber-600
                                            @elseif($order->status === 'delivered') bg-emerald-100 text-emerald-600
                                            @else bg-rose-100 text-rose-600
                                            @endif">
                                        {{ $order->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <form action="{{ route('admin.dining.orders.updateStatus', $order) }}" method="POST"
                                        class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                            class="text-[10px] font-black uppercase tracking-widest border-slate-200 rounded-lg bg-slate-50 focus:ring-blue-500 focus:border-blue-500">
                                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Mark
                                                Pending</option>
                                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Mark
                                                Confirmed</option>
                                            <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Mark
                                                Preparing</option>
                                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Mark
                                                Delivered</option>
                                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Mark
                                                Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-8 bg-slate-50/50">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection