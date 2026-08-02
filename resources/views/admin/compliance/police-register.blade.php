@extends('layouts.admin')

@section('header_title', 'Police Register (Form-A)')

@section('content')
    <div class="max-w-6xl mx-auto space-y-5">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-800">Daily Guest Register</h2>
                <p class="text-xs text-slate-500 mt-0.5">Form-A style arrival register for local police verification.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" class="flex items-center gap-2">
                    <input type="date" name="date" value="{{ $date->toDateString() }}"
                        class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                    <button class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold">View</button>
                </form>
                <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-bold">Print</button>
            </div>
        </div>

        <div class="print-area bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
            <div class="hidden print:block mb-4 text-center">
                <h1 class="text-lg font-black">{{ site('hotel_name', 'Hotel') }} — Guest Arrival Register</h1>
                <p class="text-xs">Date: {{ $date->format('d M Y') }} &nbsp;&bull;&nbsp; {{ site('hotel_phone', '') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-xs min-w-[900px]">
                    <thead>
                        <tr class="text-left text-[10px] uppercase tracking-widest text-slate-400 border-b border-slate-200">
                            <th class="py-2 pr-2 font-black">#</th>
                            <th class="py-2 pr-2 font-black">Guest Name</th>
                            <th class="py-2 pr-2 font-black">Address</th>
                            <th class="py-2 pr-2 font-black">Nationality</th>
                            <th class="py-2 pr-2 font-black">ID Type</th>
                            <th class="py-2 pr-2 font-black">ID Number</th>
                            <th class="py-2 pr-2 font-black">Arrived From</th>
                            <th class="py-2 pr-2 font-black">Purpose</th>
                            <th class="py-2 pr-2 font-black">Room</th>
                            <th class="py-2 pr-2 font-black">Check-in</th>
                            <th class="py-2 font-black">Exp. Departure</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guests as $i => $g)
                            <tr class="border-b border-slate-100">
                                <td class="py-2.5 pr-2 text-slate-400">{{ $i + 1 }}</td>
                                <td class="py-2.5 pr-2 font-bold text-slate-700">{{ $g->name }}<div class="text-[10px] text-slate-400 font-normal">{{ $g->phone }}</div></td>
                                <td class="py-2.5 pr-2 max-w-[180px]">{{ $g->address ?: '—' }}</td>
                                <td class="py-2.5 pr-2">{{ $g->nationality ?: '—' }}</td>
                                <td class="py-2.5 pr-2">{{ $g->id_type ? ucwords(str_replace('_', ' ', $g->id_type)) : '—' }}</td>
                                <td class="py-2.5 pr-2 font-mono">{{ $g->id_number ?: '—' }}</td>
                                <td class="py-2.5 pr-2">{{ $g->arrived_from ?: '—' }}</td>
                                <td class="py-2.5 pr-2">{{ $g->purpose_of_visit ?: '—' }}</td>
                                <td class="py-2.5 pr-2 font-bold">{{ $g->booking->assignedRooms->pluck('room_number')->join(', ') ?: ($g->booking->roomType->name ?? '—') }}</td>
                                <td class="py-2.5 pr-2">{{ optional($g->booking->checked_in_at)->format('h:i A') }}</td>
                                <td class="py-2.5">{{ optional($g->booking->check_out)->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="py-10 text-center text-slate-400">No check-ins recorded on {{ $date->format('d M Y') }}.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="hidden print:flex justify-between mt-10 pt-6 text-[10px]">
                <p>Prepared by: ______________________</p>
                <p>Manager Signature: ______________________</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            .print-area, .print-area * { visibility: visible; }
            .print-area { position: absolute; inset: 0; width: 100%; border: none !important; box-shadow: none !important; }
        }
    </style>
@endsection
