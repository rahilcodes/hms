<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('roomType')->latest()->paginate(10);
        return view('admin.assets.index', compact('assets'));
    }

    public function create()
    {
        $roomTypes = RoomType::all();
        $rooms = \App\Models\Room::orderBy('floor')->orderBy('room_number')->get();
        return view('admin.assets.create', compact('roomTypes', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:AC,TV,Furniture,Linen,Appliance,Other',
            'room_type_id' => 'nullable|exists:room_types,id',
            'room_id' => 'nullable|exists:rooms,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'status' => 'required|string|in:active,in-repair,retired',
        ]);

        // Generate unique QR Code string
        $validated['qr_code'] = strtoupper(Str::random(8));

        Asset::create($validated);

        return redirect()->route('admin.assets.index')->with('success', 'Asset registered successfully.');
    }

    public function show(Asset $asset)
    {
        return view('admin.assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $roomTypes = RoomType::all();
        $rooms = \App\Models\Room::orderBy('floor')->orderBy('room_number')->get();
        return view('admin.assets.edit', compact('asset', 'roomTypes', 'rooms'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:AC,TV,Furniture,Linen,Appliance,Other',
            'room_type_id' => 'nullable|exists:room_types,id',
            'room_id' => 'nullable|exists:rooms,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'warranty_expiry' => 'nullable|date',
            'status' => 'required|string|in:active,in-repair,retired',
        ]);

        $asset->update($validated);

        return redirect()->route('admin.assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('admin.assets.index')->with('success', 'Asset retired/deleted successfully.');
    }
}
