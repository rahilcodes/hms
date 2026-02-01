@extends('layouts.admin')

@section('header_title', 'One-Click Night Audit')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- LEFT COLUMN: AUDIT COMMAND CENTER --}}
        <div class="lg:col-span-8 space-y-6">
            
            {{-- STATUS CARD --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-8 flex flex-col items-center text-center">
                <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center mb-6 shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 mb-2">Ready for Night Audit</h2>
                <p class="text-slate-500 text-sm max-w-md mx-auto mb-8 font-medium">Closing the operations for <span class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($businessDate)->format('l, d M Y') }}</span>. This will rollover the business date and post daily revenue.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 w-full mb-8">
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-left">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Check-in Status</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-black text-slate-900">{{ $pendingCheckins }} Pending</span>
                            @if($pendingCheckins > 0)
                                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 rounded text-[10px] font-bold">Auto No-Show</span>
                            @else
                                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            @endif
                        </div>
                    </div>
                    <div class="p-6 bg-slate-50 rounded-2xl border border-slate-100 text-left">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Check-out Status</p>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-black text-slate-900">{{ $pendingCheckouts }} Pending</span>
                            @if($pendingCheckouts > 0)
                                <span class="px-2 py-0.5 bg-rose-100 text-rose-700 rounded text-[10px] font-bold uppercase">Overdue</span>
                            @else
                                <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            @endif
                        </div>
                    </div>
                </div>

                <form action="{{ route('admin.night-audit.run') }}" method="POST" class="w-full max-w-sm">
                    @csrf
                    <button type="submit" onclick="return confirm('WARNING: This will close the business day and advance the calendar. This action cannot be undone. Proceed?')" 
                        class="w-full bg-slate-900 text-white rounded-2xl py-5 font-black text-lg shadow-2xl shadow-slate-300 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Perform One-Click Audit
                    </button>
                    <p class="text-[10px] font-bold text-slate-400 mt-4 uppercase tracking-tighter italic">Last Audit: {{ $history->first()?->created_at->diffForHumans() ?? 'Never' }}</p>
                </form>
            </div>

            {{-- HISTORY TABLE --}}
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="font-black text-slate-900 text-sm italic">Audit Ledger History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="px-8 py-4 text-left">Audit Date</th>
                                <th class="px-8 py-4 text-center">Revenue</th>
                                <th class="px-8 py-4 text-center">Occupancy</th>
                                <th class="px-8 py-4 text-center">No-Shows</th>
                                <th class="px-8 py-4 text-left">Performed By</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($history as $audit)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-8 py-5 font-bold text-slate-900">{{ $audit->audit_date->format('d M, Y') }}</td>
                                    <td class="px-8 py-5 text-center font-black text-slate-900">₹{{ number_format($audit->revenue_total) }}</td>
                                    <td class="px-8 py-5 text-center">
                                        <div class="flex flex-col items-center">
                                            <span class="text-xs font-bold text-slate-700">{{ $audit->occupancy_rate }}%</span>
                                            <div class="w-12 h-1 bg-slate-100 rounded-full mt-1 overflow-hidden">
                                                <div class="h-full bg-blue-500" style="width: {{ $audit->occupancy_rate }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        <span class="px-2 py-0.5 {{ $audit->no_shows_count > 0 ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-400' }} rounded text-[10px] font-bold">
                                            {{ $audit->no_shows_count }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 bg-slate-900 text-white rounded-full flex items-center justify-center text-[8px] font-bold">
                                                {{ substr($audit->admin->name ?? 'A', 0, 1) }}
                                            </div>
                                            <span class="text-xs font-medium text-slate-600">{{ $audit->admin->name ?? 'System' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-12 text-center text-slate-400 text-xs italic">No audit records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($history->hasPages())
                    <div class="px-8 py-4 border-t border-slate-100">
                        {{ $history->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: CHECKLIST --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
                <h4 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 pb-2 border-b border-slate-100 italic">Pre-Audit Checklist</h4>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <div class="w-6 h-6 rounded-full {{ $pendingCheckins == 0 ? 'bg-emerald-50 text-emerald-500' : 'bg-amber-50 text-amber-500' }} flex items-center justify-center shrink-0">
                            @if($pendingCheckins == 0)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800">Clear Pending Check-ins</p>
                            <p class="text-[10px] text-slate-400 font-medium leading-relaxed">System will auto-mark all pending arrivals as No-Show if not handled.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-6 h-6 rounded-full {{ $pendingCheckouts == 0 ? 'bg-emerald-50 text-emerald-500' : 'bg-rose-50 text-rose-500' }} flex items-center justify-center shrink-0">
                             @if($pendingCheckouts == 0)
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            @else
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800">Clear Overdue Check-outs</p>
                            <p class="text-[10px] text-slate-400 font-medium leading-relaxed">Ensure all guests physical departures are recorded to avoid inventory sync issues.</p>
                        </div>
                    </div>

                    <div class="flex gap-4 opacity-50">
                        <div class="w-6 h-6 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </div>
                        <div>
                            <p class="text-xs font-black text-slate-800">Post Daily Revenue</p>
                            <p class="text-[10px] text-slate-400 font-medium leading-relaxed">System posts all transaction data into the official ledger.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-8 bg-slate-900 rounded-3xl text-white shadow-2xl shadow-slate-400">
                <h4 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-4 italic">Operational Tip</h4>
                <p class="text-xs font-medium leading-relaxed text-slate-300">Run the audit after midnight but before the first check-in of the new day starts. This ensures financial precision for the daily ledger.</p>
            </div>
        </div>
    </div>

@endsection
