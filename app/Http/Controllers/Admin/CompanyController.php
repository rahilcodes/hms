<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::orderBy('name')->paginate(20);
        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        Company::create($request->all());

        return redirect()->route('admin.companies.index')->with('success', 'Corporate profile created successfully.');
    }

    public function edit(Company $company)
    {
        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $company->update($request->all());

        return redirect()->route('admin.companies.index')->with('success', 'Corporate profile updated successfully.');
    }

    public function destroy(Company $company)
    {
        $company->delete();
        return redirect()->route('admin.companies.index')->with('success', 'Corporate profile deleted successfully.');
    }
}
