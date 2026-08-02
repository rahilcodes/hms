@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Expense Ledger</h1>
            <p class="text-slate-500 font-medium">Manage hotel expenditures and petty cash.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.expenses.create') }}" class="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg shadow-sm hover:bg-blue-700 transition">
                Record Expense
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Total Period Spend</p>
            <h3 class="text-3xl font-black text-slate-900">₹{{ number_format($totalAmount) }}</h3>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1">Items Tracked</p>
            <h3 class="text-3xl font-black text-slate-900">{{ $expenses->total() }}</h3>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.expenses.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Category</label>
                <select name="category" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm font-medium focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-slate-50 border-slate-200 rounded-lg text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-slate-900 text-white font-bold rounded-lg hover:bg-slate-800 transition">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Expense Table --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Category</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Description</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Method</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Amount</th>
                    <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($expenses as $expense)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $expense->expense_date->format('d M, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-[10px] font-black uppercase">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-slate-700 leading-tight">{{ $expense->description }}</p>
                            @if($expense->reference_number)
                                <p class="text-[10px] text-slate-400 mt-1 font-mono">Ref: {{ $expense->reference_number }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">{{ str_replace('_', ' ', $expense->payment_method) }}</td>
                        <td class="px-6 py-4 text-right font-black text-slate-900">₹{{ number_format($expense->amount) }}</td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.expenses.destroy', $expense) }}" method="POST" onsubmit="return confirm('Delete this record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-rose-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium italic">No expenses recorded for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($expenses->hasPages())
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
