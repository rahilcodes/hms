<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedDate;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BlockedDateController extends Controller
{
    public function index()
    {
        $blocks = BlockedDate::with('roomType')
            ->orderByDesc('date')
            ->get();

        return view('admin.blocked-dates.index', compact('blocks'));
    }

    public function create()
    {
        $roomTypes = RoomType::orderBy('name')->get();

        return view('admin.blocked-dates.create', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'blocked_rooms' => 'required|integer|min:1',
        ]);

        $start = Carbon::parse($request->from_date);
        $end = Carbon::parse($request->to_date);

        while ($start->lte($end)) {
            BlockedDate::updateOrCreate(
                [
                    'room_type_id' => $request->room_type_id,
                    'date' => $start->toDateString(),
                ],
                [
                    'blocked_rooms' => $request->blocked_rooms,
                ]
            );

            $start->addDay();
        }

        return redirect()
            ->route('admin.blocked-dates.index')
            ->with('success', 'Rooms blocked successfully');
    }
    public function destroy(BlockedDate $blockedDate)
    {
        $blockedDate->delete();
        return back()->with('success', 'Block removed successfully');
    }
}
