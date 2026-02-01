<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LinenType;

class LinenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $linens = LinenType::orderBy('category')->get();
        return view('admin.linen.index', compact('linens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:bedding,bath,fb,staff,other',
            'par_level' => 'required|integer|min:0',
            'total_stock' => 'required|integer|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
        ]);

        LinenType::create($validated);

        return redirect()->route('admin.linen.index')->with('success', 'Linen type added successfully.');
    }

    public function update(Request $request, LinenType $linen)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:bedding,bath,fb,staff,other',
            'par_level' => 'required|integer|min:0',
            'total_stock' => 'required|integer|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
        ]);

        $linen->update($validated);

        return redirect()->route('admin.linen.index')->with('success', 'Linen type updated.');
    }

    public function destroy(LinenType $linen)
    {
        $linen->delete();
        return redirect()->route('admin.linen.index')->with('success', 'Linen type deleted.');
    }
}
