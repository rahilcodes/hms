@extends('layouts.admin')

@section('header_title', 'Financial Intelligence')

@section('content')

    <div class="mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tighter">Financial Ledger</h2>
            <p class="text-slate-500 font-medium">Real-time revenue tracking and multi-mode collection auditing.</p>
        </div>

        <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-4 w-full lg:w-auto">
            <form action="{{ route('admin.financials.index') }}" method="GET"
                x-data="{ 
                    range: '{{ request('range', 'today') }}',
                    corporateOnly: {{ request('corporate_only') === '1' ? 'true' : 'false' }},
                    showMenu: false
                }"
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full sm:w-auto">

                <input type="hidden" name="corporate_only" :value="corporateOnly ? '1' : '0'">
                <input type="hidden" name="range" x-model="range">

                <button type="button" 
                    @click="corporateOnly = !corporateOnly; $nextTick(() => $el.closest('form').submit())"
                    :class="corporateOnly ? 'bg-blue-600 text-white shadow-lg shadow-blue-200 border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'"
                    class="px-6 py-4 rounded-2xl border text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 flex items-center gap-2 whitespace-nowrap">
                    <div class="w-2 h-2 rounded-full" :class="corporateOnly ? 'bg-white animate-pulse' : 'bg-slate-300'"></div>
                    Corporate Only
                </button>

                <div class="relative group">
                    <button type="button" @click="showMenu = !showMenu" @click.outside="showMenu = false"
                        class="flex items-center gap-4 px-6 py-4 bg-white border border-slate-200 rounded-2xl shadow-sm hover:border-blue-200 hover:bg-slate-50 transition-all duration-300 group">
                        <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" /></svg>
                        </div>
                        <div class="flex flex-col items-start leading-tight">
                            <span class="text-[9px] font-black text-blue-600 uppercase tracking-[0.2em] mb-0.5">Filter by Period</span>
                            <span class="text-sm font-black text-slate-900 flex items-center gap-2">
                                <span x-text="{
                                    'today': 'Today',
                                    'yesterday': 'Yesterday',
                                    'last_7_days': 'Last 7 Days',
                                    'this_month': 'This Month',
                                    'last_month': 'Last Month',
                                    'last_3_months': 'Last 3 Months',
                                    'custom': 'Custom Range'
                                }[range] || 'Select Date'">Today</span>
                                <svg class="w-3.5 h-3.5 text-slate-300 group-hover:text-blue-600 transition-transform duration-300" 
                                     :class="showMenu ? 'rotate-180' : ''"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </span>
                        </div>
                    </button>

                    {{-- CUSTOM DROPDOWN MENU --}}
                    <div x-show="showMenu" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2 scale-95" 
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-100" 
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        style="display: none;"
                        class="absolute left-0 mt-3 w-72 bg-white/80 backdrop-blur-xl rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-white p-3 z-[100] titanium-dropdown-menu">

                        <div class="grid grid-cols-1 gap-1">
                            @foreach ([
                                'today' => [
                                    'label' => 'Today', 
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />', 
                                    'desc' => 'Instant realization'
                                ],
                                'yesterday' => [
                                    'label' => 'Yesterday', 
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />', 
                                    'desc' => 'Previous collections'
                                ],
                                'last_7_days' => [
                                    'label' => 'Last 7 Days', 
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />', 
                                    'desc' => 'Weekly summary'
                                ],
                                'this_month' => [
                                    'label' => 'This Month', 
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />', 
                                    'desc' => 'Current cycle'
                                ],
                                'last_month' => [
                                    'label' => 'Last Month', 
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />', 
                                    'desc' => 'Archived history'
                                ],
                                'last_3_months' => [
                                    'label' => 'Last 3 Months', 
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.625-1.219c.337-2.538 2.452-4.474 4.941-4.474.13 0 .258.004.386.012a6.76 6.76 0 0 1 3.515 2.18 6.726 6.726 0 0 1 1.058 3.5c0 .012 0 .025-.001.037a6.736 6.736 0 0 1-1.06 3.514 6.76 6.76 0 0 1-3.515 2.18c-.128.008-.256.012-.387.012-2.49 0-4.605-1.936-4.942-4.474" />', 
                                    'desc' => 'Quarterly audit'
                                ],
                                'custom' => [
                                    'label' => 'Custom Range', 
                                    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0m-3.75 0H3.75" />', 
                                    'desc' => 'Pick your dates'
                                ]
                            ] as $val => $data)
                                <div @click="range = '{{ $val }}'; showMenu = false; if('{{ $val }}' !== 'custom') $nextTick(() => $el.closest('form').submit())"
                                    :class="range === '{{ $val }}' ? 'bg-blue-600 text-white shadow-lg shadow-blue-200' : 'hover:bg-slate-50 text-slate-600'"
                                    class="group/item flex items-center gap-4 p-3 rounded-[1.25rem] transition-all duration-200 cursor-pointer">
                                    <div :class="range === '{{ $val }}' ? 'bg-white/20' : 'bg-blue-50 text-blue-600'" 
                                         class="w-10 h-10 rounded-xl flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $data['icon'] !!}</svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-black" :class="range === '{{ $val }}' ? 'text-white' : 'text-slate-900'">{{ $data['label'] }}</span>
                                        <span class="text-[9px] font-bold" :class="range === '{{ $val }}' ? 'text-blue-100' : 'text-slate-400'">{{ $data['desc'] }}</span>
                                    </div>
                                    <template x-if="range === '{{ $val }}'">
                                        <svg class="w-4 h-4 ml-auto text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </template>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div x-show="range === 'custom'" x-transition class="flex items-center gap-3 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-2 px-3 border-r border-slate-100">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">From</span>
                        <input type="date" name="start_date" value="{{ $start->format('Y-m-d') }}"
                            class="bg-transparent text-xs font-bold text-slate-900 outline-none p-1">
                    </div>
                    <div class="flex items-center gap-2 px-3">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">To</span>
                        <input type="date" name="end_date" value="{{ $end->format('Y-m-d') }}"
                            class="bg-transparent text-xs font-bold text-slate-900 outline-none p-1">
                    </div>
                    <button type="submit" class="p-2 bg-slate-900 text-white rounded-xl hover:bg-black transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </div>
            </form>

            <a href="{{ route('admin.financials.export', ['range' => request('range', 'today'), 'start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d')]) }}"
                class="flex items-center gap-3 px-6 py-4 bg-blue-600 text-white rounded-2xl group transition-all duration-300 shadow-xl shadow-blue-500/20 hover:bg-blue-700 hover:shadow-blue-500/40 transform active:scale-95">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </div>
                <div class="flex flex-col items-start leading-tight">
                    <span class="text-[9px] font-black text-blue-100 uppercase tracking-[0.2em] mb-0.5">Intelligence</span>
                    <span class="text-xs font-black tracking-tight">Export CSV</span>
                </div>
            </a>
        </div>
    </div>

    {{-- KPI GRID: PROFITABILITY --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- INCOME --}}
        <div class="bg-white rounded-[2rem] p-8 border border-slate-200 shadow-sm relative overflow-hidden">
             <div class="absolute right-0 top-0 w-32 h-32 bg-emerald-50 rounded-full blur-3xl -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600 mb-4">Total Income</p>
                <h3 class="text-4xl font-black tracking-tighter text-slate-900 mb-2">₹{{ number_format($stats['total_income']) }}</h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-xs font-bold text-slate-400">Cash: ₹{{ number_format($stats['cash']) }}</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400">Digital: ₹{{ number_format($stats['upi'] + $stats['card'] + $stats['bank_transfer']) }}</span>
                </div>
            </div>
        </div>

        {{-- EXPENSES --}}
        <div class="bg-white rounded-[2rem] p-8 border border-slate-200 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 w-32 h-32 bg-rose-50 rounded-full blur-3xl -mr-16 -mt-16"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-600 mb-4">Total Expenses</p>
                <h3 class="text-4xl font-black tracking-tighter text-slate-900 mb-2">₹{{ number_format($stats['total_expense']) }}</h3>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-xs font-bold text-slate-400">Laundry: ₹{{ number_format($stats['laundry_cost']) }}</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400">Maint: ₹{{ number_format($stats['maintenance_cost']) }}</span>
                </div>
            </div>
        </div>

        {{-- NET PROFIT --}}
        <div class="bg-slate-900 rounded-[2rem] p-8 text-white relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-40 h-40 bg-blue-500/20 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-blue-400 mb-4">Net Profit / Loss</p>
                <h3 class="text-4xl font-black tracking-tighter mb-2 {{ $stats['net_profit'] >= 0 ? 'text-white' : 'text-rose-400' }}">
                    {{ $stats['net_profit'] >= 0 ? '+' : '' }}₹{{ number_format($stats['net_profit']) }}
                </h3>
                <p class="text-xs text-slate-400 font-medium italic">Realized gains for selected period</p>
            </div>
        </div>
    </div>

    {{-- TRANSACTIONS LEDGER --}}
    <div class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-10 py-8 border-b border-slate-100 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-black text-slate-900 tracking-tight">Financial Ledger</h3>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Consolidated Stream</p>
            </div>
             <div class="flex items-center gap-2">
                <span class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-full">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div> Income
                </span>
                <span class="flex items-center gap-1 text-[10px] font-bold text-rose-600 bg-rose-50 px-3 py-1.5 rounded-full">
                    <div class="w-1.5 h-1.5 rounded-full bg-rose-500"></div> Expense
                </span>
            </div>
        </div>

        {{-- MOBILE LEDGER (CARDS) --}}
        <div class="md:hidden space-y-4 px-4 pb-4">
            @forelse($ledger as $row)
                <div class="bg-white rounded-[1.5rem] p-5 border border-slate-100 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 {{ $row['type'] === 'income' ? 'bg-emerald-50' : 'bg-rose-50' }} rounded-bl-[2rem] -mr-4 -mt-4"></div>
                    
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <p class="text-[10px] font-black {{ $row['type'] === 'income' ? 'text-emerald-500' : 'text-rose-500' }} uppercase tracking-widest mb-1">{{ $row['type'] === 'income' ? 'Income' : 'Expense' }}</p>
                                <h4 class="text-sm font-black text-slate-900 leading-tight">{{ $row['entity'] }}</h4>
                            </div>
                            <span class="text-lg font-black tracking-tight {{ $row['type'] === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $row['type'] === 'income' ? '+' : '-' }}₹{{ number_format($row['amount']) }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2 mb-4">
                            <span class="text-[10px] font-bold font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded">
                                {{ $row['reference'] }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400">
                                {{ $row['date']->format('d M, h:i A') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pt-3 border-t border-slate-50">
                            <span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                {{ $row['category'] }}
                            </span>
                            <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1">
                                {{ $row['method'] }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 opacity-50">
                    <p class="text-xs font-bold uppercase tracking-widest">No Transactions</p>
                </div>
            @endforelse
        </div>

        {{-- DESKTOP LEDGER (TABLE) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Timestamp</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Reference</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Entity / Guest</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Category</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Credit (In)</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Debit (Out)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($ledger as $row)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-8 py-5">
                                <p class="text-xs font-black text-slate-900">{{ $row['date']->format('d M, Y') }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $row['date']->format('H:i A') }}</p>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-xs font-bold font-mono text-slate-500 bg-slate-100 px-2 py-1 rounded">
                                    {{ $row['reference'] }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <p class="text-xs font-bold text-slate-900">{{ $row['entity'] }}</p>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-[10px] font-bold uppercase tracking-wide
                                    {{ $row['type'] === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $row['category'] }}
                                </span>
                                <p class="text-[9px] text-slate-400 font-bold mt-0.5">{{ $row['method'] }}</p>
                            </td>
                            
                            {{-- CREDIT / INCOME --}}
                            <td class="px-8 py-5 text-right">
                                @if($row['type'] === 'income')
                                    <span class="text-sm font-black text-emerald-600">+₹{{ number_format($row['amount']) }}</span>
                                @else
                                    <span class="text-slate-200 text-xs">-</span>
                                @endif
                            </td>

                            {{-- DEBIT / EXPENSE --}}
                            <td class="px-8 py-5 text-right">
                                @if($row['type'] === 'expense')
                                    <span class="text-sm font-black text-rose-600">-₹{{ number_format($row['amount']) }}</span>
                                @else
                                    <span class="text-slate-200 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-10 py-32 text-center opacity-20">
                                <p class="text-2xl font-black uppercase tracking-[0.2em] italic">No Financial Records Found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection