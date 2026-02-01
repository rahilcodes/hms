@extends('layouts.admin')

@section('header_title', 'Reservations')

@section('content')

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
            <div>
                <h3 class="text-lg font-bold text-slate-900">All Bookings</h3>
                <p class="text-xs text-slate-400 font-medium">Detailed history of all property reservations</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.bookings.create') }}"
                    class="px-5 py-2.5 text-xs font-bold text-white bg-blue-600 rounded-xl hover:shadow-lg hover:shadow-blue-200 transition-all transform hover:-translate-y-0.5">
                    + New Booking
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-5 text-left">Reference</th>
                        <th class="px-8 py-5 text-left">Guest Context</th>
                        <th class="px-8 py-5 text-left">Stay Period</th>
                        <th class="px-8 py-5 text-left">Room Matrix</th>
                        <th class="px-8 py-5 text-left">Financials</th>
                        <th class="px-8 py-5 text-left">Status</th>
                        <th class="px-8 py-5 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-8 py-5">
                                <span class="text-xs font-bold text-slate-900">#{{ $booking->id + 1000 }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($booking->guest_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">{{ $booking->guest_name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium tracking-tight truncate max-w-[120px]">
                                            {{ $booking->guests->first()->phone ?? 'No Phone' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2 mb-1">
                                    <span
                                        class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M') }}</span>
                                    <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                    <span
                                        class="text-xs font-bold text-slate-700">{{ \Carbon\Carbon::parse($booking->check_out)->format('d M, Y') }}</span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium tracking-tight">
                                    {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} Nights Stay
                                </p>
                            </td>
                            <td class="px-8 py-5">
                                <span
                                    class="inline-flex items-center px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-bold">
                                    {{ $booking->roomType->name ?? 'None' }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="text-xs font-extrabold text-slate-900">
                                    ₹{{ number_format($booking->total_bill) }}
                                </div>
                                @if($booking->company_id)
                                    <div class="flex items-center gap-1 mt-1">
                                        <span
                                            class="text-[9px] font-black text-blue-500 uppercase tracking-widest">{{ $booking->company->name }}</span>
                                    </div>
                                @endif
                                @if($booking->meta['paid_at_hotel'] ?? false)
                                    <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Paid</span>
                                @else
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Balance Owed</span>
                                @endif
                                @if($booking->group_id)
                                    <div class="mt-1">
                                        <span
                                            class="px-1.5 py-0.5 bg-purple-50 text-purple-600 rounded text-[8px] font-black uppercase tracking-tighter">Group</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                @php
                                    $statusStyle = match ($booking->status) {
                                        'confirmed' => 'bg-emerald-50 text-emerald-600',
                                        'pending' => 'bg-amber-50 text-amber-600',
                                        'cancelled' => 'bg-rose-50 text-rose-600',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                @endphp
                                <span
                                    class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $statusStyle }}">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                    class="relative z-10 inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-200 shadow-sm">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-20 text-center opacity-30">
                                <p class="text-sm font-bold">No reservations found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection