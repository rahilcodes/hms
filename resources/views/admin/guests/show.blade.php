@extends('layouts.admin')

@section('header_title')
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.guests.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 rounded-lg transition">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <span>Guest Profile: {{ $guestInfo->name }}</span>
    </div>
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        {{-- LEFT COLUMN: PROFILE & CRM ACTIONS --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- PROFILE CARD --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 flex flex-col items-center text-center">
                <div
                    class="w-24 h-24 {{ $totalSpend > 5000 ? 'bg-amber-100 text-amber-600' : 'bg-blue-600 text-white' }} rounded-3xl flex items-center justify-center font-bold text-3xl shadow-xl shadow-blue-100 mb-6">
                    {{ strtoupper(substr($guestInfo->name, 0, 1)) }}
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $guestInfo->name }}</h3>
                <div class="flex flex-wrap justify-center gap-2 mb-6">
                    @if ($guestBookings->count() >= 3)
                        <span
                            class="px-2 py-0.5 bg-indigo-50 text-indigo-600 rounded-lg text-[10px] font-black uppercase tracking-widest">Frequent
                            Stayer</span>
                    @endif
                    @if ($totalSpend > 5000)
                        <span
                            class="px-2 py-0.5 bg-amber-50 text-amber-600 rounded-lg text-[10px] font-black uppercase tracking-widest">VIP
                            Guest</span>
                    @endif
                </div>

                <div class="w-full space-y-4 pt-6 border-t border-slate-100 text-left">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Phone Number</span>
                        <span class="text-sm font-bold text-slate-900">{{ $guestInfo->phone ?? 'Not provided' }}</span>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Email Address</span>
                        <span class="text-sm font-bold text-slate-900">{{ $guestInfo->email ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            {{-- ANALYTICS CARD --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
                <h4 class="text-sm font-bold text-slate-900 mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    CRM Analytics
                </h4>
                <div class="space-y-4">
                    <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">LTV (Spend)</p>
                            <p class="text-lg font-black text-slate-900">₹{{ number_format($totalSpend) }}</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">Visits</p>
                            <p class="text-lg font-black text-slate-900">{{ $guestBookings->count() }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-0.5">Avg/Stay</p>
                            <p class="text-lg font-black text-slate-900">₹{{ number_format($avgSpend, 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CRM ACTIONS: NOTES & PREFERENCES --}}
            <form action="{{ route('admin.guests.update', $guestInfo->phone ?: 'none') }}" method="POST"
                class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8 space-y-6">
                @csrf
                @method('PUT')
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    CRM Intelligence
                </h4>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Guest
                        Preferences</label>
                    <textarea name="preferences" rows="3"
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-100 focus:bg-white outline-none transition-all"
                        placeholder="e.g. Loves high floors, Vegan breakfast...">{{ $guestInfo->preferences }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Internal Staff
                        Notes</label>
                    <textarea name="internal_notes" rows="3"
                        class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-100 focus:bg-white outline-none transition-all"
                        placeholder="Private notes for staff only...">{{ $guestInfo->internal_notes }}</textarea>
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 text-white rounded-xl py-3 text-xs font-bold hover:bg-black transition-all shadow-lg shadow-slate-200">
                    Save Intelligence
                </button>
            </form>
        </div>

        {{-- RIGHT COLUMN: JOURNEY TIMELINE --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/30 flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Stay Timeline</h3>
                        <p class="text-xs text-slate-400 font-medium tracking-tight">Full history of guest interaction</p>
                    </div>
                </div>

                <div class="p-8">
                    <div
                        class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-slate-200 before:to-transparent">

                        @foreach($guestBookings->sortByDesc('booking.check_in') as $gb)
                            {{-- TIMELINE ITEM --}}
                            <div
                                class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                {{-- DOT --}}
                                <div
                                    class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-slate-300 group-[.is-active]:bg-blue-600 text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>

                                {{-- CONTENT --}}
                                <div
                                    class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between space-x-2 mb-1">
                                        <div class="font-black text-slate-900">
                                            {{ \Carbon\Carbon::parse($gb->booking->check_in)->format('M d') }} -
                                            {{ \Carbon\Carbon::parse($gb->booking->check_out)->format('M d, Y') }}
                                        </div>
                                        <time
                                            class="font-medium text-[10px] text-slate-400 uppercase tracking-widest">{{ \Carbon\Carbon::parse($gb->booking->check_in)->format('Y') }}</time>
                                    </div>
                                    <div class="text-slate-500 text-xs mb-3 flex items-center gap-2">
                                        <span
                                            class="font-bold text-blue-600">{{ $gb->booking->roomType->name ?? 'Accommodation' }}</span>
                                        <span>•</span>
                                        <span>{{ $gb->booking->assignedRooms->pluck('room_number')->join(', ') ?: 'Room Pending' }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-slate-50">
                                        <span
                                            class="text-xs font-black text-slate-900">₹{{ number_format($gb->booking->total_amount) }}</span>
                                        <a href="{{ route('admin.bookings.show', $gb->booking) }}"
                                            class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">View
                                            Details</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection