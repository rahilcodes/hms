<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        return view('admin.services.index', compact('services'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'required|string|in:fixed,per_hour,per_person,per_night',
            'icon_class' => 'nullable|string',
            'max_quantity_rule' => 'nullable|in:room_extra_capacity,none',
        ]);

        if (!empty($validated['max_quantity_rule']) && $validated['max_quantity_rule'] !== 'none') {
            $validated['constraints'] = ['max_quantity_rule' => $validated['max_quantity_rule']];
        } else {
            $validated['constraints'] = null;
        }
        unset($validated['max_quantity_rule']);

        Service::create($validated);

        return back()->with('success', 'Service added successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_unit' => 'required|string|in:fixed,per_hour,per_person,per_night',
            'icon_class' => 'nullable|string',
            'is_active' => 'required|boolean',
            'max_quantity_rule' => 'nullable|in:room_extra_capacity,none',
        ]);

        if (!empty($validated['max_quantity_rule']) && $validated['max_quantity_rule'] !== 'none') {
            $validated['constraints'] = ['max_quantity_rule' => $validated['max_quantity_rule']];
        } else {
            $validated['constraints'] = null;
        }
        unset($validated['max_quantity_rule']);

        $service->update($validated);

        return back()->with('success', 'Service updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success', 'Service deleted successfully');
    }
}
