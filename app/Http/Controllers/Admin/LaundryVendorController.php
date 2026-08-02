<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaundryVendor;

class LaundryVendorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendors = LaundryVendor::all();
        return view('admin.laundry.vendors.index', compact('vendors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        LaundryVendor::create($validated);

        return redirect()->back()->with('success', 'Vendor added successfully.');
    }

    public function update(Request $request, LaundryVendor $vendor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
        ]);

        $vendor->update($validated);

        return redirect()->back()->with('success', 'Vendor updated.');
    }

    public function destroy(LaundryVendor $vendor)
    {
        $vendor->delete();
        return redirect()->back()->with('success', 'Vendor deleted.');
    }
}
