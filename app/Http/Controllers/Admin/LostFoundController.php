<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LostFoundItem;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class LostFoundController extends Controller
{
    public function index(Request $request)
    {
        $query = LostFoundItem::with(['room', 'foundBy', 'guest'])
            ->orderByRaw("FIELD(status, 'found', 'claimed', 'disposed', 'donated')")
            ->orderByDesc('found_date');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('found_location', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(20)->withQueryString();

        return view('admin.lost-found.index', compact('items'));
    }

    public function create()
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('admin.lost-found.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:electronics,clothing,documents,valuables,others',
            'found_date' => 'required|date',
            'found_location' => 'required|string',
            'room_id' => 'nullable|exists:rooms,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120', // 5MB
        ]);

        $admin = Auth::guard('admin')->user();

        $data = $validated;
        $data['hotel_id'] = $admin ? $admin->hotel_id : 1;
        $data['found_by_user_id'] = $admin ? $admin->id : null;
        $data['status'] = 'found';

        // Auto-link guest if room provided and occupied at found_date
        if ($request->room_id) {
            $date = $request->found_date;
            // Simplified logic: find booking active on that date in that room
            // In a real scenario, we'd query booking_room and check dates.
            // For now, let's leave guest_id null unless explicitly linked later.
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('lost-found', 'public');
            $data['image_path'] = $path;
        }

        LostFoundItem::create($data);

        return redirect()->route('admin.lost-found.index')
            ->with('success', 'Item reported successfully.');
    }

    public function show(LostFoundItem $lost_found_item)
    {
        return view('admin.lost-found.show', compact('lost_found_item'));
    }

    public function edit(LostFoundItem $lost_found_item)
    {
        $rooms = Room::orderBy('room_number')->get();
        return view('admin.lost-found.edit', compact('lost_found_item', 'rooms'));
    }

    public function update(Request $request, LostFoundItem $lost_found_item)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'category' => 'required|in:electronics,clothing,documents,valuables,others',
            'found_location' => 'required|string',
            'status' => 'required|in:found,claimed,disposed,donated',

            // Allow claiming
            'claimed_by_name' => 'required_if:status,claimed',
            'claimed_date' => 'required_if:status,claimed',
        ]);

        // Handle Image Update
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('lost-found', 'public');
            $validated['image_path'] = $path;
        }

        $lost_found_item->update($validated);

        return redirect()->route('admin.lost-found.index')
            ->with('success', 'Item updated successfully.');
    }

    public function destroy(LostFoundItem $lost_found_item)
    {
        $lost_found_item->delete();
        return back()->with('success', 'Item deleted.');
    }
}
