<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RoomType;
use App\Models\ActivityLog;
use App\Services\BookingService;
use App\Services\BookingEmailService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $emailService;

    public function __construct(BookingEmailService $emailService)
    {
        $this->emailService = $emailService;
    }
    public function index(Request $request)
    {
        $query = Booking::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderByDesc('id')
            ->with(['guests', 'roomType', 'company'])
            ->paginate(20)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $roomTypes = \App\Models\RoomType::all();
        $services = \App\Models\Service::where('is_active', true)->get();
        $companies = \App\Models\Company::where('is_active', true)->orderBy('name')->get();
        return view('admin.bookings.create', compact('roomTypes', 'services', 'companies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_name' => 'required|string',
            'guest_email' => 'required|email',
            'guest_phone' => 'nullable|string',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'items' => 'required|array|min:1',
            'items.*.room_type_id' => 'required|exists:room_types,id',
            'items.*.rooms' => 'required|integer|min:1',
            'items.*.extra_persons' => 'nullable|integer|min:0',
            'services' => 'nullable|array',
            'total_amount' => 'nullable|numeric|min:0',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);
        $groupId = count($request->items) > 1 ? (string) \Illuminate\Support\Str::uuid() : null;

        $createdBookings = [];

        foreach ($request->items as $index => $itemData) {
            $roomType = \App\Models\RoomType::find($itemData['room_type_id']);
            $extraPersonTotal = ($itemData['extra_persons'] ?? 0) * $roomType->extra_person_price * $nights;

            // 🔘 Calculate Services for this specific item
            $itemServicesJson = [];
            $itemServicesTotal = 0;

            if (isset($itemData['services']) && is_array($itemData['services'])) {
                foreach ($itemData['services'] as $serviceId => $qty) {
                    if ($qty < 1)
                        continue;
                    $service = \App\Models\Service::find($serviceId);
                    if ($service) {
                        $sTotal = $service->price * $qty;
                        if ($service->price_unit === 'per_night')
                            $sTotal *= $nights;
                        $itemServicesTotal += $sTotal;
                        $itemServicesJson[] = [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->price,
                            'qty' => $qty,
                            'price_unit' => $service->price_unit
                        ];
                    }
                }
            }

            $bookingPrice = ($roomType->base_price * $itemData['rooms'] * $nights) + $extraPersonTotal + $itemServicesTotal;

            // Manual total override only for single-item bookings
            if ($request->total_amount !== null && count($request->items) === 1) {
                $bookingPrice = $request->total_amount;
            }

            $booking = Booking::create([
                'hotel_id' => 1,
                'group_id' => $groupId,
                'company_id' => $request->company_id,
                'room_type_id' => $itemData['room_type_id'],
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'rooms' => (int) $itemData['rooms'],
                'total_amount' => $bookingPrice,
                'status' => 'confirmed',
                'services_json' => $itemServicesJson,
                'meta' => [
                    'guest_email' => $request->guest_email,
                    'source' => 'admin_manual',
                    'extra_persons' => (int) ($itemData['extra_persons'] ?? 0),
                    'is_group' => $groupId ? true : false
                ],
            ]);

            $booking->guests()->create([
                'name' => $request->guest_name,
                'phone' => $request->guest_phone,
            ]);

            $createdBookings[] = $booking;
            ActivityLog::log('Booking Created', "New reservation #{$booking->id} for {$request->guest_name}" . ($groupId ? " (Group: {$groupId})" : ""), $booking);
        }

        // Send confirmation email for the first one (usually enough for a group)
        if (!empty($createdBookings)) {
            $this->emailService->sendConfirmation($createdBookings[0]);
        }

        $msg = count($createdBookings) > 1
            ? 'Bulk booking created successfully (' . count($createdBookings) . ' room types) and confirmation sent.'
            : 'Booking created successfully and confirmation email sent.';

        return redirect()->route('admin.bookings.index')->with('success', $msg);
    }

    public function show(Booking $booking)
    {
        $booking->load(['roomType', 'guests', 'company']);
        $groupMembers = [];
        if ($booking->group_id) {
            $groupMembers = Booking::where('group_id', $booking->group_id)
                ->where('id', '!=', $booking->id)
                ->with('roomType')
                ->get();
        }

        // Fetch available rooms for this booking's type
        $availableRooms = \App\Models\Room::where('room_type_id', $booking->room_type_id)
            ->where('status', 'available')
            ->orderBy('room_number')
            ->get();

        return view('admin.bookings.show', compact('booking', 'groupMembers', 'availableRooms'));
    }

    public function cancel(Booking $booking)
    {
        $booking->update(['status' => 'cancelled']);

        ActivityLog::log('Booking Cancelled', "Reservation #{$booking->id} was cancelled", $booking);

        return back()->with('success', 'Booking cancelled');
    }

    public function markPaid(Booking $booking, Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,partial',
            'amount' => 'required_if:type,partial|numeric|min:0.01',
            'method' => 'required|in:cash,upi,card,bank_transfer',
        ]);

        if (!in_array($booking->status, ['confirmed', 'pending', 'checked_in', 'checked_out'])) {
            abort(400, 'Only confirmed, pending, or active bookings can be marked as paid');
        }

        $meta = $booking->meta ?? [];
        $payments = $meta['payments'] ?? [];

        $paymentAmount = $request->type === 'full' ? $booking->balance_amount : (float) $request->amount;

        $payments[] = [
            'amount' => $paymentAmount,
            'method' => $request->input('method'),
            'timestamp' => now()->toDateTimeString(),
            'recorded_by' => auth('admin')->user()->name ?? 'System',
            'type' => $request->type
        ];

        $meta['payments'] = $payments;

        $updateData = ['meta' => $meta];

        // Status update logic
        if ($booking->status === 'pending') {
            $updateData['status'] = 'confirmed';
        }

        $booking->update($updateData);

        $logMsg = $request->type === 'full'
            ? "Full balance of ₹" . number_format($paymentAmount) . " settled via " . strtoupper($request->input('method'))
            : "Partial payment of ₹" . number_format($paymentAmount) . " received via " . strtoupper($request->input('method'));

        ActivityLog::log('Payment Received', $logMsg, $booking);

        return back()->with('success', 'Payment recorded successfully');
    }

    public function checkIn(Booking $booking, Request $request)
    {
        if ($booking->status !== 'confirmed') {
            return back()->with('error', 'Only confirmed bookings can check in.');
        }

        // Check if rooms are already assigned
        $currentRooms = $booking->assignedRooms;

        if ($request->has('room_ids')) {
            // Validate provided room IDs
            $request->validate([
                'room_ids' => 'required|array',
                'room_ids.*' => 'exists:rooms,id'
            ]);
            $rooms = \App\Models\Room::whereIn('id', $request->room_ids)->get();

            // Sync new rooms
            $booking->assignedRooms()->sync($request->room_ids);
        } elseif ($currentRooms->count() >= $booking->rooms) {
            // Use existing assigned rooms
            $rooms = $currentRooms;
        } else {
            return back()->with('error', 'Please assign rooms before checking in.');
        }

        // 1. Check availability (Double check even for pre-assigned)
        foreach ($rooms as $room) {
            if ($room->status !== 'available' && !$currentRooms->contains($room->id)) {
                // Only block if it's a NEW assignment and room is busy. 
                // If it was already assigned to THIS booking, it might be 'booked' status, which is fine to transition to 'occupied'.
                // But typically 'booked' rooms are 'available' for check-in? 
                // Let's assume 'available' means 'vacant'.
                return back()->with('error', "Room {$room->room_number} is not available (Status: {$room->status}).");
            }
        }

        // 2. Update Status to Occupied
        foreach ($rooms as $room) {
            $room->update(['status' => 'occupied']);
        }

        $booking->update([
            'checked_in_at' => now(),
            'rechecked_by' => auth('admin')->id(),
            'status' => 'checked_in' // Optional: explicit status
        ]);

        ActivityLog::log('Guest Checked-In', "Guest for #{$booking->id} checked in to rooms: " . $rooms->pluck('room_number')->implode(', '), $booking);

        // Send Welcome Email
        $this->emailService->sendWelcome($booking);

        return back()->with('success', 'Guest checked in successfully.');
    }

    public function checkOut(Booking $booking)
    {
        if (!$booking->checked_in_at) {
            return back()->with('error', 'Guest must be checked in before checking out.');
        }

        if ($booking->balance_amount > 0) {
            return back()->with('error', 'Cannot perform checkout. Outstanding balance of ₹' . number_format($booking->balance_amount) . ' must be cleared first.');
        }

        // Release Rooms
        $rooms = $booking->assignedRooms;
        foreach ($rooms as $room) {
            $room->update([
                'status' => 'available',
                'housekeeping_status' => 'dirty' // Auto-mark dirty
            ]);
        }

        $booking->update([
            'checked_out_at' => now(),
            'status' => 'checked_out'
        ]);

        ActivityLog::log('Guest Checked-Out', "Guest for #{$booking->id} checked out. Rooms marked dirty.", $booking);

        // Send Post-stay survey
        $this->emailService->sendSurvey($booking);

        return back()->with('success', 'Guest checked out. Rooms marked dirty.');
    }

    public function invoice(Booking $booking)
    {
        $booking->load(['roomType', 'assignedRooms', 'company', 'guests']);
        return view('admin.bookings.invoice', compact('booking'));
    }

    public function reschedule(Booking $booking, Request $request)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $oldCheckIn = \Carbon\Carbon::parse($booking->check_in)->format('Y-m-d');
        $oldCheckOut = \Carbon\Carbon::parse($booking->check_out)->format('Y-m-d');

        $booking->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
        ]);

        ActivityLog::log('Booking Rescheduled', "Reservation #{$booking->id} moved from {$oldCheckIn}/{$oldCheckOut} to {$request->check_in}/{$request->check_out}", $booking);

        return back()->with('success', 'Booking rescheduled successfully');
    }

    public function calendar()
    {
        return view('admin.bookings.calendar');
    }

    public function calendarData(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $query = Booking::with(['roomType', 'guests']);

        if ($start && $end) {
            $query->whereBetween('check_in', [$start, $end]);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($booking) {
            $guestName = $booking->guest_name ?? 'Guest';
            $roomName = $booking->roomType ? $booking->roomType->name : 'N/A';

            $status = $booking->operational_status;
            $color = match ($status) {
                'pending_checkin' => '#f59e0b',  // Amber
                'in_house' => '#3b82f6',         // Blue
                'pending_checkout' => '#ec4899', // Pink
                'overdue_checkout' => '#ef4444', // Red
                'checked_out' => '#64748b',      // Slate (Archived)
                'cancelled' => '#94a3b8',        // Gray
                default => '#10b981'             // Emerald (Upcoming Confirmed)
            };

            return [
                'id' => $booking->id,
                'title' => "{$guestName} - {$roomName}",
                'start' => $booking->check_in,
                'end' => $booking->check_out,
                'url' => route('admin.bookings.show', $booking),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'status' => $status,
                    'rooms' => $booking->rooms,
                ]
            ];
        });

        return response()->json($events);
    }
}
