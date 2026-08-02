@extends('layouts.admin')

@section('header_title', 'Reservations')

@section('content')

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/30">
            <div class="flex items-center justify-between gap-3">
                <div class="hidden xl:block">
                    <h3 class="text-sm font-bold text-slate-900">All Bookings</h3>
                </div>
                <div class="flex-1 flex items-center justify-end gap-2">
                    {{-- Filter Form --}}
                    <form action="{{ route('admin.bookings.index') }}" method="GET" class="flex flex-wrap items-center gap-2">
                        {{-- Search Input --}}
                        <div class="relative group">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." 
                                class="pl-8 pr-3 py-1.5 text-[11px] font-medium bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all w-32 lg:w-48 placeholder:text-slate-400 group-hover:border-slate-300">
                            <svg class="w-3.5 h-3.5 absolute left-2.5 top-2.5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        {{-- Status Select --}}
                        <div class="relative group">
                            <select name="status" onchange="this.form.submit()" 
                                class="appearance-none pl-3 pr-8 py-1.5 text-[11px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer group-hover:border-slate-300">
                                <option value="">Status</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                <option value="checked_in" {{ request('status') === 'checked_in' ? 'selected' : '' }}>In</option>
                                <option value="checked_out" {{ request('status') === 'checked_out' ? 'selected' : '' }}>Out</option>
                            </select>
                            <svg class="w-3 h-3 absolute right-2.5 top-2.5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>

                        {{-- Room Type Select --}}
                        <div class="relative group">
                            <select name="room_type_id" onchange="this.form.submit()" 
                                class="appearance-none pl-3 pr-8 py-1.5 text-[11px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer group-hover:border-slate-300">
                                <option value="">Room Type</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            <svg class="w-3 h-3 absolute right-2.5 top-2.5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </div>

                        {{-- Date Filter --}}
                        <div class="relative group">
                            <input type="date" name="date" value="{{ request('date') }}" onchange="this.form.submit()" 
                                class="pl-3 pr-3 py-1.5 text-[11px] font-bold text-slate-700 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all cursor-pointer group-hover:border-slate-300">
                        </div>

                        {{-- Reset Button --}}
                        @if(request()->anyFilled(['search', 'status', 'date', 'room_type_id']))
                            <a href="{{ route('admin.bookings.index') }}" 
                                class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all" title="Clear Filters">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </form>

                    <div class="h-6 w-px bg-slate-200 hidden sm:block mx-1"></div>

                    <a href="{{ route('admin.bookings.create') }}"
                        class="px-4 py-2 text-[11px] font-bold text-white bg-blue-600 rounded-lg hover:shadow-lg hover:shadow-blue-200 transition-all transform hover:-translate-y-0.5 whitespace-nowrap">
                        + New
                    </a>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <th class="px-5 py-3 text-left group/sort">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'id', 'sort_order' => ($sort === 'id' && $order === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-600 transition-colors">
                                Reference
                                @if($sort === 'id')
                                    <svg class="w-2.5 h-2.5 {{ $order === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left group/sort">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'guest_name', 'sort_order' => ($sort === 'guest_name' && $order === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-600 transition-colors">
                                Guest
                                @if($sort === 'guest_name')
                                    <svg class="w-2.5 h-2.5 {{ $order === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left group/sort">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'check_in', 'sort_order' => ($sort === 'check_in' && $order === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-600 transition-colors">
                                Stay
                                @if($sort === 'check_in')
                                    <svg class="w-2.5 h-2.5 {{ $order === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left group/sort">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'room_type_id', 'sort_order' => ($sort === 'room_type_id' && $order === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-600 transition-colors">
                                Type
                                @if($sort === 'room_type_id')
                                    <svg class="w-2.5 h-2.5 {{ $order === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left group/sort">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'total_amount', 'sort_order' => ($sort === 'total_amount' && $order === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-600 transition-colors">
                                Bill
                                @if($sort === 'total_amount')
                                    <svg class="w-2.5 h-2.5 {{ $order === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-left group/sort">
                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => ($sort === 'status' && $order === 'asc') ? 'desc' : 'asc']) }}" class="flex items-center gap-1 hover:text-slate-600 transition-colors">
                                Status
                                @if($sort === 'status')
                                    <svg class="w-2.5 h-2.5 {{ $order === 'asc' ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                @endif
                            </a>
                        </th>
                        <th class="px-5 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-5 py-2.5">
                                <span class="text-xs font-bold text-slate-900">#{{ $booking->id + 1000 }}</span>
                            </td>
                            <td class="px-5 py-2.5">
                                <div>
                                    <p class="text-xs font-bold text-slate-900 mb-0.5">
                                        {{ $booking->guests->first()->name ?? 'Guest' }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-medium tracking-tight">
                                        {{ $booking->guests->first()->phone ?? 'No Phone' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-5 py-2.5">
                                <div class="flex items-center gap-2 mb-0.5">
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
                            <td class="px-5 py-2.5">
                                <span
                                    class="inline-flex items-center px-2 py-1 bg-slate-100 text-slate-600 rounded-lg text-[9px] font-bold">
                                    {{ $booking->roomType->name ?? 'None' }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5">
                                <div class="text-xs font-extrabold text-slate-900 leading-tight">
                                    ₹{{ number_format($booking->total_bill) }}
                                </div>
                                @if($booking->company_id)
                                    <div class="flex items-center gap-1 mt-0.5">
                                        <span
                                            class="text-[9px] font-black text-blue-500 uppercase tracking-widest">{{ $booking->company->name }}</span>
                                    </div>
                                @endif
                                @if($booking->meta['paid_at_hotel'] ?? false)
                                    <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Paid</span>
                                @else
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Owed</span>
                                @endif
                            </td>
                            <td class="px-5 py-2.5">
                                @php
                                    $statusStyle = match ($booking->status) {
                                        'confirmed' => 'bg-emerald-50 text-emerald-600',
                                        'pending' => 'bg-amber-50 text-amber-600',
                                        'cancelled' => 'bg-rose-50 text-rose-600',
                                        default => 'bg-slate-100 text-slate-600'
                                    };
                                @endphp
                                <span
                                    class="px-2 py-1 rounded-full text-[9px] font-bold uppercase tracking-wide {{ $statusStyle }}">
                                    {{ $booking->status }}
                                </span>
                            </td>
                            <td class="px-5 py-2.5 text-right">
                                <a href="{{ route('admin.bookings.show', $booking) }}"
                                    class="relative z-10 inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-slate-600 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-200 shadow-sm">
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