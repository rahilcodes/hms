<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Expense::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('start_date')) {
            $query->where('expense_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('expense_date', '<=', $request->end_date);
        }

        $expenses = $query->orderByDesc('expense_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $totalAmount = $query->sum('amount');
        $categories = \App\Models\Expense::distinct()->pluck('category');

        return view('admin.expenses.index', compact('expenses', 'totalAmount', 'categories'));
    }

    public function create()
    {
        $categories = ['Salary', 'Utilities', 'Maintenance', 'F&B', 'Marketing', 'Laundry', 'Supplies', 'Other'];
        return view('admin.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'required|string|max:500',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,cheque',
            'reference_number' => 'nullable|string|max:100',
        ]);

        \App\Models\Expense::create(array_merge($validated, [
            'hotel_id' => 1, // Will be handled by global scope trait but explicit for now
            'status' => 'paid'
        ]));

        return redirect()->route('admin.expenses.index')->with('success', 'Expense recorded successfully.');
    }

    public function destroy(\App\Models\Expense $expense)
    {
        $expense->delete();
        return back()->with('success', 'Expense deleted.');
    }
}
