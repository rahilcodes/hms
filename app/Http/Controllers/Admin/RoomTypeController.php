<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    public function index()
    {
        $roomTypes = RoomType::where('hotel_id', 1)->orderBy('id')->get();

        return view('admin.room-types.index', compact('roomTypes'));
    }

    public function create()
    {
        return view('admin.room-types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_rooms' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'base_occupancy' => 'required|integer|min:1',
            'max_extra_persons' => 'required|integer|min:0',
            'extra_person_price' => 'required|numeric|min:0',
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('rooms', 'public');
        }

        $gallery = [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('rooms', 'public');
            }
        }

        $roomType = RoomType::create([
            'hotel_id' => 1,
            'name' => $request->name,
            'total_rooms' => $request->total_rooms,
            'base_price' => $request->base_price,
            'image' => $path,
            'gallery_json' => $gallery,
            'description' => $request->description,
            'amenities' => $request->amenities,
            'base_occupancy' => $request->base_occupancy,
            'max_extra_persons' => $request->max_extra_persons,
            'extra_person_price' => $request->extra_person_price,
        ]);

        ActivityLog::log('Room Type Created', "New room type '{$request->name}' added", $roomType);

        return redirect()
            ->route('admin.room-types.index')
            ->with('success', 'Room type added successfully');
    }

    public function edit(RoomType $roomType)
    {
        return view('admin.room-types.edit', compact('roomType'));
    }

    public function update(Request $request, RoomType $roomType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'total_rooms' => 'required|integer|min:1',
            'base_price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
            'gallery.*' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'base_occupancy' => 'required|integer|min:1',
            'max_extra_persons' => 'required|integer|min:0',
            'extra_person_price' => 'required|numeric|min:0',
        ]);

        $data = [
            'name' => $request->name,
            'total_rooms' => $request->total_rooms,
            'base_price' => $request->base_price,
            'description' => $request->description,
            'amenities' => $request->amenities,
            'base_occupancy' => $request->base_occupancy,
            'max_extra_persons' => $request->max_extra_persons,
            'extra_person_price' => $request->extra_person_price,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('rooms', 'public');
        }

        $gallery = $roomType->gallery_json ?? [];
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $file) {
                $gallery[] = $file->store('rooms', 'public');
            }
            $data['gallery_json'] = $gallery;
        }

        $roomType->update($data);

        // Sync Rooms
        if ($request->has('room_numbers')) {
            $submittedRooms = $request->room_numbers;
            // Get existing rooms to update/delete
            $existingRooms = $roomType->rooms()->orderBy('id')->get();

            foreach ($submittedRooms as $index => $number) {
                if (empty($number))
                    continue; // Skip empty? Or auto-generate? JS should have handled it.

                if (isset($existingRooms[$index])) {
                    // Update existing
                    $existingRooms[$index]->update(['room_number' => $number]);
                } else {
                    // Create new
                    $roomType->rooms()->create([
                        'room_number' => $number,
                        'status' => 'available', // Default status
                        'housekeeping_status' => 'clean', // Default
                    ]);
                }
            }

            // Delete extra rooms if count reduced
            if ($existingRooms->count() > count($submittedRooms)) {
                // Determine which to delete (the ones at the end)
                $toDelete = $existingRooms->slice(count($submittedRooms));
                foreach ($toDelete as $room) {
                    // Check for bookings? For now soft delete or strict delete
                    // If bookings exist maybe prevent? But user reduced count. 
                    // Let's assume user knows what they are doing for now, or just leave them orphaned?
                    // BelongsTo relationship, deleting room might be okay if cascade.
                    // But strictly, let's just delete the record.
                    $room->delete();
                }
            }
        }

        ActivityLog::log('Room Type Updated', "Room type '{$roomType->name}' details modified", $roomType);

        return redirect()
            ->route('admin.room-types.edit', $roomType)
            ->with('success', 'Room type updated successfully');
    }

    public function removeImage(RoomType $roomType, Request $request)
    {
        $type = $request->type; // 'main' or 'gallery'
        $path = $request->path;

        if ($type === 'main') {
            if ($roomType->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($roomType->image);
                $roomType->update(['image' => null]);
            }
        } else {
            $gallery = $roomType->gallery_json ?? [];
            if (($key = array_search($path, $gallery)) !== false) {
                unset($gallery[$key]);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
                $roomType->update(['gallery_json' => array_values($gallery)]);
            }
        }

        return redirect()
            ->route('admin.room-types.edit', $roomType)
            ->with('success', 'Image removed successfully');
    }

    public function reorderImages(RoomType $roomType, Request $request)
    {
        $roomType->update(['gallery_json' => $request->images]);
        return response()->json(['success' => true]);
    }
}
