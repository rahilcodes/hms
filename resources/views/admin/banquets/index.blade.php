@extends('layouts.admin')

@section('header_title', 'Banquets & Events')

@section('content')
    <div class="max-w-6xl mx-auto space-y-5">

        {{-- Header + month nav --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-800">Banquets &amp; Events</h2>
                <p class="text-xs text-slate-500 mt-0.5">Weddings, receptions and conferences — {{ $month->format('F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.banquets.index', ['month' => $month->copy()->subMonth()->format('Y-m')]) }}"
                    class="px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-600">&larr;</a>
                <a href="{{ route('admin.banquets.index', ['month' => $month->copy()->addMonth()->format('Y-m')]) }}"
                    class="px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-600">&rarr;</a>
                <a href="{{ route('admin.banquets.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-black shadow">+ New Event</a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Month Revenue</p>
                <p class="text-xl font-black text-slate-800 mt-1">₹{{ number_format($stats['month_revenue']) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Advance Collected</p>
                <p class="text-xl font-black text-emerald-600 mt-1">₹{{ number_format($stats['month_advance']) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Confirmed</p>
                <p class="text-xl font-black text-blue-600 mt-1">{{ $stats['confirmed'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200 p-4">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Enquiries</p>
                <p class="text-xl font-black text-amber-500 mt-1">{{ $stats['enquiries'] }}</p>
            </div>
        </div>

        {{-- Calendar strip --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-4 overflow-x-auto">
            <div class="flex gap-1.5 min-w-max">
                @for($d = $month->copy()->startOfMonth(); $d <= $month->copy()->endOfMonth(); $d = $d->copy()->addDay())
                    @php $dayEvents = $eventsByDay->get($d->format('Y-m-d'), collect()); @endphp
                    <div class="w-10 text-center rounded-xl py-2 {{ $dayEvents->isNotEmpty() ? 'bg-blue-600 text-white' : ($d->isToday() ? 'bg-slate-100 ring-1 ring-blue-300' : 'bg-slate-50') }}">
                        <p class="text-[9px] font-bold {{ $dayEvents->isNotEmpty() ? 'text-blue-100' : 'text-slate-400' }}">{{ $d->format('D') }}</p>
                        <p class="text-sm font-black">{{ $d->day }}</p>
                        @if($dayEvents->isNotEmpty())
                            <p class="text-[9px] font-black">{{ $dayEvents->count() }}</p>
                        @endif
                    </div>
                @endfor
            </div>
        </div>

        {{-- Event list --}}
        <div class="space-y-3">
            @forelse($events as $event)
                @php
                    $statusColors = [
                        'enquiry' => 'bg-amber-50 text-amber-600',
                        'confirmed' => 'bg-blue-50 text-blue-600',
                        'completed' => 'bg-emerald-50 text-emerald-600',
                        'cancelled' => 'bg-rose-50 text-rose-500',
                    ];
                @endphp
                <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex items-center gap-4 flex-1 min-w-0">
                        <div class="w-14 h-14 rounded-2xl bg-slate-800 text-white flex flex-col items-center justify-center shrink-0">
                            <span class="text-lg font-black leading-none">{{ $event->event_date->day }}</span>
                            <span class="text-[9px] font-bold uppercase">{{ $event->event_date->format('M') }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="font-black text-slate-800 truncate">{{ $event->customer_name }}
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded-full ml-1 {{ $statusColors[$event->status] ?? 'bg-slate-100' }}">{{ $event->status }}</span>
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5">
                                {{ ucfirst($event->event_type) }} &bull; {{ $event->hall->name ?? '—' }}
                                &bull; {{ $event->guests_expected }} pax
                                @if($event->start_time) &bull; {{ $event->start_time }}–{{ $event->end_time }} @endif
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-4 sm:gap-6">
                        <div class="text-right">
                            <p class="font-black text-slate-800">₹{{ number_format($event->total_amount) }}</p>
                            <p class="text-[10px] {{ $event->balance_amount > 0 ? 'text-rose-500' : 'text-emerald-600' }} font-bold">
                                {{ $event->balance_amount > 0 ? 'Due ₹' . number_format($event->balance_amount) : 'Fully paid' }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.banquets.edit', $event) }}" class="px-3 py-2 rounded-xl bg-slate-100 text-slate-600 text-[11px] font-bold">Edit</a>
                            <a href="{{ route('admin.banquets.invoice', $event) }}" class="px-3 py-2 rounded-xl bg-blue-50 text-blue-600 text-[11px] font-bold">Invoice</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-slate-300 p-10 text-center text-slate-400 text-sm">
                    No events in {{ $month->format('F Y') }}. Tap “+ New Event” to add one.
                </div>
            @endforelse
        </div>

        {{-- Halls --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-black text-slate-700 uppercase tracking-wide">Halls</h3>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($halls as $hall)
                    <div class="rounded-xl border border-slate-200 p-4">
                        <p class="font-black text-slate-800">{{ $hall->name }}</p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $hall->capacity }} pax &bull; base ₹{{ number_format($hall->base_rent) }} &bull; {{ $hall->month_events }} events this month</p>
                    </div>
                @endforeach

                <form method="POST" action="{{ route('admin.banquets.halls.store') }}" class="rounded-xl border border-dashed border-slate-300 p-4 space-y-2">
                    @csrf
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Add Hall</p>
                    <input name="name" required placeholder="Hall name" class="w-full px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    <div class="flex gap-2">
                        <input name="capacity" type="number" required placeholder="Capacity" class="w-1/2 px-3 py-2 rounded-lg border border-slate-200 text-sm">
                        <input name="base_rent" type="number" step="0.01" required placeholder="Base rent ₹" class="w-1/2 px-3 py-2 rounded-lg border border-slate-200 text-sm">
                    </div>
                    <button class="w-full py-2 rounded-lg bg-slate-800 text-white text-xs font-black">Add</button>
                </form>
            </div>
        </div>
    </div>
@endsection
