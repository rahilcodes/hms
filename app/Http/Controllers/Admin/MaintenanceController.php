<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceLog;
use App\Models\Asset;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $pendingLogs = MaintenanceLog::where('status', '!=', 'completed')
            ->with(['asset.roomType', 'asset.room'])
            ->latest()
            ->get();

        $completedLogs = MaintenanceLog::where('status', 'completed')
            ->with(['asset.roomType', 'asset.room'])
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.maintenance.index', compact('pendingLogs', 'completedLogs'));
    }

    public function create()
    {
        $assets = Asset::with(['room', 'roomType'])->where('status', '!=', 'retired')->get();
        $rooms = \App\Models\Room::with('roomType')->orderBy('room_number')->get();
        return view('admin.maintenance.create', compact('assets', 'rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'nullable|exists:assets,id',
            'room_id' => 'nullable|exists:rooms,id',
            'description' => 'required|string',
            'technician_name' => 'nullable|string',
            'priority' => 'nullable|string|in:low,medium,high,critical',
        ]);

        if (empty($validated['asset_id']) && empty($validated['room_id'])) {
            return back()->withErrors(['asset_id' => 'Please select either an Asset or a Room.'])->withInput();
        }

        MaintenanceLog::create([
            'asset_id' => $validated['asset_id'],
            'room_id' => $validated['room_id'],
            'description' => $validated['description'],
            'technician_name' => $validated['technician_name'] ?? 'Unassigned',
            'status' => 'pending',
            'cost' => 0,
        ]);

        // If asset is selected, update its status to in-repair if critical
        if (!empty($validated['asset_id']) && ($request->priority === 'critical' || $request->priority === 'high')) {
            $asset = Asset::find($validated['asset_id']);
            $asset->update(['status' => 'in-repair']);
        }

        // Similarly for room? Maybe set housekeeping status? For now, keep it simple.

        return redirect()->route('admin.maintenance.index')->with('success', 'Maintenance request logged successfully.');
    }

    public function updateStatus(Request $request, MaintenanceLog $maintenance)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in-progress,completed',
            'cost' => 'nullable|numeric|min:0',
            'technician_name' => 'nullable|string'
        ]);

        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'completed') {
            $updateData['completed_at'] = now();
            $updateData['cost'] = $validated['cost'] ?? $maintenance->cost;

            // If asset was in-repair, set back to active
            if ($maintenance->asset && $maintenance->asset->status === 'in-repair') {
                $maintenance->asset->update(['status' => 'active']);
            }
        }

        if (!empty($validated['technician_name'])) {
            $updateData['technician_name'] = $validated['technician_name'];
        }

        $maintenance->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'updated_at' => now()->format('M d, H:i')
        ]);
    }
}
