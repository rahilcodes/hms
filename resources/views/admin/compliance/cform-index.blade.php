@extends('layouts.admin')

@section('header_title', 'C-Form / FRRO — Foreign Guests')

@section('content')
    <div class="max-w-6xl mx-auto space-y-5">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-800">Foreign Guest Arrivals</h2>
                <p class="text-xs text-slate-500 mt-0.5">Foreign nationals must be reported to the FRRO within 24 hours of check-in (Form-C).</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="date" name="from" value="{{ $from->toDateString() }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                <input type="date" name="to" value="{{ $to->toDateString() }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                <button class="px-4 py-2.5 rounded-xl bg-slate-800 text-white text-xs font-bold">Filter</button>
                <a href="{{ route('admin.compliance.cform.export', ['from' => $from->toDateString(), 'to' => $to->toDateString()]) }}"
                    class="px-4 py-2.5 rounded-xl bg-emerald-600 text-white text-xs font-bold">Export CSV</a>
            </form>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs min-w-[760px]">
                    <thead>
                        <tr class="text-left text-[10px] uppercase tracking-widest text-slate-400 border-b border-slate-200 bg-slate-50">
                            <th class="py-3 px-4 font-black">Guest</th>
                            <th class="py-3 px-2 font-black">Nationality</th>
                            <th class="py-3 px-2 font-black">Passport / ID</th>
                            <th class="py-3 px-2 font-black">Visa</th>
                            <th class="py-3 px-2 font-black">Stay</th>
                            <th class="py-3 px-2 font-black">Status</th>
                            <th class="py-3 px-4 font-black text-right">C-Form</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($guests as $g)
                            @php
                                $complete = $g->id_number && $g->visa_number;
                            @endphp
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="py-3 px-4">
                                    <p class="font-bold text-slate-700">{{ $g->name }}</p>
                                    <p class="text-[10px] text-slate-400">{{ $g->phone }}</p>
                                </td>
                                <td class="py-3 px-2 font-bold">{{ $g->nationality }}</td>
                                <td class="py-3 px-2 font-mono">{{ $g->id_number ?: '—' }}</td>
                                <td class="py-3 px-2">
                                    {{ $g->visa_number ?: '—' }}
                                    @if($g->visa_expiry)
                                        <div class="text-[10px] text-slate-400">exp {{ $g->visa_expiry->format('d M Y') }}</div>
                                    @endif
                                </td>
                                <td class="py-3 px-2">
                                    {{ optional($g->booking->check_in)->format('d M') }} → {{ optional($g->booking->check_out)->format('d M Y') }}
                                </td>
                                <td class="py-3 px-2">
                                    @if($complete)
                                        <span class="px-2 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black">READY</span>
                                    @else
                                        <span class="px-2 py-1 rounded-full bg-amber-50 text-amber-600 text-[10px] font-black">DETAILS MISSING</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <a href="{{ route('admin.compliance.cform.show', $g) }}"
                                        class="px-3 py-1.5 rounded-lg bg-blue-50 text-blue-600 text-[11px] font-bold">Open</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-slate-400">No foreign guest arrivals in this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">{{ $guests->links() }}</div>
        </div>
    </div>
@endsection
