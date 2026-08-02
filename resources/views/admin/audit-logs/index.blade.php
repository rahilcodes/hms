@extends('layouts.admin')

@section('header_title', 'System Audit Logs')

@section('content')

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
            <div>
                <h3 class="text-lg font-bold text-slate-900">Activity History</h3>
                <p class="text-xs text-slate-400 font-medium">Traceable record of all administrative changes</p>
            </div>
            <div class="flex items-center gap-2">
                <span
                    class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold uppercase tracking-widest">Live
                    Monitoring</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-5 text-left">Timestamp</th>
                        <th class="px-8 py-5 text-left">Operator</th>
                        <th class="px-8 py-5 text-left">Action</th>
                        <th class="px-8 py-5 text-left">Entity Context</th>
                        <th class="px-8 py-5 text-left">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                                    <tr class="hover:bg-slate-50 transition group">
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <p class="text-xs font-bold text-slate-900 leading-tight">
                                                {{ $log->created_at->format('M d, H:i:s') }}</p>
                                            <p class="text-[10px] text-slate-400 font-medium tracking-tight">
                                                {{ $log->created_at->diffForHumans() }}</p>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div
                                                    class="w-7 h-7 bg-slate-100 rounded-lg flex items-center justify-center text-slate-500 font-bold text-[10px]">
                                                    {{ strtoupper(substr($log->admin->name ?? 'S', 0, 1)) }}
                                                </div>
                                                <p class="text-xs font-bold text-slate-700">{{ $log->admin->name ?? 'System' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-2 mb-1">
                                                @php
                                                    $actionColor = match (true) {
                                                        str_contains(strtolower($log->action), 'create') => 'text-emerald-500',
                                                        str_contains(strtolower($log->action), 'cancel') => 'text-rose-500',
                                                        str_contains(strtolower($log->action), 'update') => 'text-blue-500',
                                                        str_contains(strtolower($log->action), 'delete') => 'text-rose-600',
                                                        default => 'text-slate-600'
                                                    };
                                                @endphp
                         <span
                                                    class="text-xs font-extrabold {{ $actionColor }} uppercase tracking-widest text-[10px]">{{ $log->action }}</span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 font-medium leading-relaxed">{{ $log->description }}</p>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 bg-slate-100 text-slate-500 rounded-md text-[9px] font-bold uppercase tracking-tighter">
                                                {{ str_replace('App\\Models\\', '', $log->target_type ?? 'N/A') }} #{{ $log->target_id }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <p class="text-[10px] font-bold text-slate-400">{{ $log->ip_address ?: '127.0.0.1' }}</p>
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center opacity-30">
                                <p class="text-sm font-bold tracking-tight">System record is currently empty.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

@endsection