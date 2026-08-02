@extends('layouts.admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Record New Expense</h1>
        <p class="text-slate-500 font-medium">Capture hotel expenditures for the ledger.</p>
    </div>

    <form action="{{ route('admin.expenses.store') }}" method="POST" class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Category</label>
                <select name="category" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-medium focus:ring-blue-500">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Amount (₹)</label>
                <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-bold focus:ring-blue-500">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Expense Date</label>
                <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-medium focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Payment Method</label>
                <select name="payment_method" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-medium focus:ring-blue-500">
                    <option value="cash">Petty Cash</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="credit_card">Business Credit Card</option>
                    <option value="cheque">Cheque</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Description / Vendor</label>
            <textarea name="description" rows="3" required placeholder="e.g. Monthly Electricity Bill, Staff Lunch, Plumbing Repair..." class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-medium focus:ring-blue-500"></textarea>
        </div>

        <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Reference / Invoice #</label>
            <input type="text" name="reference_number" placeholder="Optional" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-medium focus:ring-blue-500">
        </div>

        <div class="pt-4 flex items-center justify-between gap-4">
            <a href="{{ route('admin.expenses.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600">Cancel</a>
            <button type="submit" class="px-8 py-3 bg-slate-900 text-white font-black rounded-xl hover:bg-slate-800 shadow-lg shadow-slate-200 transition transform hover:-translate-y-0.5 active:translate-y-0">
                Post Expense
            </button>
        </div>
    </form>
</div>
@endsection
