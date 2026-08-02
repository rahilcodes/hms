<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LaundryBatch;
use App\Models\LaundryVendor;
use App\Models\LinenType;
use App\Models\LaundryItem;

class LaundryBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batches = LaundryBatch::with(['vendor', 'items.linenType'])->orderByDesc('created_at')->get();
        return view('admin.laundry.batches.index', compact('batches'));
    }

    public function create()
    {
        $vendors = LaundryVendor::where('is_active', true)->get();
        $linens = LinenType::all();
        return view('admin.laundry.batches.create', compact('vendors', 'linens'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:laundry_vendors,id',
            'sent_date' => 'required|date',
            'expected_return_date' => 'nullable|date|after_or_equal:sent_date',
            'items' => 'required|array',
            'items.*.linen_type_id' => 'required|exists:linen_types,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $batch = LaundryBatch::create([
            'batch_number' => 'LND-' . date('Ymd') . '-' . rand(100, 999),
            'vendor_id' => $validated['vendor_id'],
            'sent_date' => $validated['sent_date'],
            'expected_return_date' => $validated['expected_return_date'],
            'status' => 'out',
        ]);

        foreach ($validated['items'] as $item) {
            if ($item['quantity'] > 0) {
                LaundryItem::create([
                    'batch_id' => $batch->id,
                    'linen_type_id' => $item['linen_type_id'],
                    'quantity_sent' => $item['quantity'],
                ]);

                // Deduct from stock (conceptually, "total_stock" is owned, so maybe we don't deduct total, 
                // but we might want to track "available stock". For now, total_stock remains same.)
            }
        }

        return redirect()->route('admin.laundry.batches.index')->with('success', 'Laundry batch dispatched.');
    }

    public function edit(LaundryBatch $batch)
    {
        $batch->load(['items.linenType', 'vendor']);
        return view('admin.laundry.batches.edit', compact('batch'));
    }

    public function update(Request $request, LaundryBatch $batch)
    {
        // Handle Receiving
        if ($request->has('receive_items')) {
            $validated = $request->validate([
                'received_date' => 'required|date',
                'items' => 'required|array',
                'items.*.id' => 'required|exists:laundry_items,id',
                'items.*.quantity_received' => 'required|integer|min:0',
                'items.*.quantity_rejected' => 'required|integer|min:0',
                'total_cost' => 'nullable|numeric|min:0',
            ]);

            foreach ($validated['items'] as $itemData) {
                $item = LaundryItem::find($itemData['id']);
                $item->update([
                    'quantity_received' => $itemData['quantity_received'],
                    'quantity_rejected' => $itemData['quantity_rejected'],
                ]);

                // Update Total Stock if rejected (Lost/Damaged)
                if ($itemData['quantity_rejected'] > 0) {
                    $item->linenType->decrement('total_stock', $itemData['quantity_rejected']);
                }
            }

            $batch->update([
                'status' => 'returned',
                'received_date' => $validated['received_date'],
                'total_cost' => $request->total_cost,
            ]);

            return redirect()->route('admin.laundry.batches.index')->with('success', 'Laundry received and stock updated.');
        }

        return redirect()->back();
    }
}
