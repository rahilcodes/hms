<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;

class RoomAssignmentService
{
    /**
     * Find the optimal room for a booking based on Gap Filler logic.
     *
     * @param Booking $booking
     * @return Room|null
     */
    public function findBestRoom(Booking $booking)
    {
        $checkIn = Carbon::parse($booking->check_in);
        $checkOut = Carbon::parse($booking->check_out);

        // 1. Get all potential rooms of the requested type
        // Use strict room type matching first (Yield Save)
        $rooms = Room::where('room_type_id', $booking->room_type_id)
            ->where('status', 'available')
            ->where('housekeeping_status', 'clean') // Prefer clean rooms
            ->get();

        // If no clean rooms, try dirty rooms (can be cleaned)
        if ($rooms->isEmpty()) {
            $rooms = Room::where('room_type_id', $booking->room_type_id)
                ->where('status', 'available')
                ->get();
        }

        // If still no rooms, return null (Yield Save: Don't auto-upgrade yet)
        if ($rooms->isEmpty()) {
            return null;
        }

        $candidates = [];

        foreach ($rooms as $room) {
            // 2. Check for conflicts (Inventory Logic)
            if ($this->hasConflicts($room, $checkIn, $checkOut, $booking->id)) {
                continue;
            }

            // 3. Calculate Gap Score
            $score = $this->calculateGapScore($room, $checkIn, $checkOut);
            
            $candidates[] = [
                'room' => $room,
                'score' => $score
            ];
        }

        // 4. Sort by Gap Score ASC (Lower is better)
        usort($candidates, function ($a, $b) {
            return $a['score'] <=> $b['score'];
        });

        return $candidates[0]['room'] ?? null;
    }

    /**
     * Check if room has conflicting bookings
     */
    protected function hasConflicts(Room $room, Carbon $start, Carbon $end, $ignoreBookingId = null)
    {
        return $room->bookings()
            ->where('bookings.id', '!=', $ignoreBookingId)
            ->where(function ($query) use ($start, $end) {
                // Check if any booking overlaps with requested range
                $query->where(function ($q) use ($start, $end) {
                    $q->where('check_in', '<', $end)
                      ->where('check_out', '>', $start);
                });
            })
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    /**
     * Calculate Gap Score: minimize dead days.
     * Score = Days to Previous Booking + Days to Next Booking
     */
    protected function calculateGapScore(Room $room, Carbon $start, Carbon $end)
    {
        // Find previous booking
        $prevBooking = $room->bookings()
            ->where('check_out', '<=', $start)
            ->where('status', '!=', 'cancelled')
            ->orderBy('check_out', 'desc')
            ->first();

        // Find next booking
        $nextBooking = $room->bookings()
            ->where('check_in', '>=', $end)
            ->where('status', '!=', 'cancelled')
            ->orderBy('check_in', 'asc')
            ->first();

        $prevGap = $prevBooking ? $start->diffInDays(Carbon::parse($prevBooking->check_out)) : 999; // 999 if no previous booking (huge gap)
        $nextGap = $nextBooking ? Carbon::parse($nextBooking->check_in)->diffInDays($end) : 999;

        // We want to minimize small gaps (1-2 days). 
        // Large gaps (>= 3 days) are fine, they are sellable.
        // Small gaps (1 day) are bad.
        
        // Weighting: 
        // 0 days gap = 0 score (Perfect)
        // 1 day gap = 100 score (Bad)
        // 2 days gap = 50 score (Manageable)
        // >2 days gap = 10 score (Sellable)

        return $this->scoreGap($prevGap) + $this->scoreGap($nextGap);
    }

    protected function scoreGap($days)
    {
        if ($days == 0) return 0;       // Perfect continuity
        if ($days == 1) return 100;     // Terrible: 1 dead night
        if ($days == 2) return 50;      // Hard to sell
        return 10;                      // Sellable
    }
}
