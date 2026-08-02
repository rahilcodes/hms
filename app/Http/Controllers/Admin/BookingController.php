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

                // 🔍 Search (Name or ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('guests', function($sq) use ($search) {
                    $sq->where('name', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%" . (str_replace('#', '', $search)) . "%");
            });
        }

        // 🔘 Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 🏨 Room Type Filter
        if ($request->filled('room_type_id')) {
            $query->where('room_type_id', $request->room_type_id);
        }

        // 📅 Date Filter (Occupancy: Arriving, staying, or leaving on this date)
        if ($request->filled('date')) {
            $date = $request->date;
            $query->where(function($q) use ($date) {
                $q->whereDate('check_in', '<=', $date)
                  ->whereDate('check_out', '>=', $date);
            });
        }

        // 🔘 Sorting
        $sort = request()->get('sort_by', 'id');
        $order = request()->get('sort_order', 'desc');
        $allowedSorts = ['id', 'guest_name', 'check_in', 'total_amount', 'status'];

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $order === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('id');
        }

        $bookings = $query->with(['guests', 'roomType', 'company'])
            ->paginate(20)
            ->withQueryString();

        $roomTypes = \App\Models\RoomType::all();

        return view('admin.bookings.index', compact('bookings', 'sort', 'order', 'roomTypes'));
    }

    public function create()
    {
        $roomTypes = \App\Models\RoomType::all();
        $services = \App\Models\Service::where('is_active', true)->get();
        $companies = \App\Models\Company::where('is_active', true)->orderBy('name')->get();
        $agents = \App\Models\TravelAgent::where('is_active', true)->orderBy('name')->get();
        return view('admin.bookings.create', compact('roomTypes', 'services', 'companies', 'agents'));
    }

    public function store(Request $request)
    {
        $isDayUse = $request->input('booking_type') === 'day_use';

        $request->validate([
            'guest_name' => 'required|string',
            'guest_email' => 'required|email',
            'guest_phone' => 'nullable|string',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => ($isDayUse ? 'nullable' : 'required') . '|date|after:check_in',
            'booking_type' => 'nullable|in:overnight,day_use',
            'day_use_hours' => 'nullable|in:4,8',
            'items' => 'required|array|min:1',
            'items.*.room_type_id' => 'required|exists:room_types,id',
            'items.*.rooms' => 'required|integer|min:1',
            'items.*.extra_persons' => 'nullable|integer|min:0',
            'items.*.children' => 'nullable|integer|min:0',
            'services' => 'nullable|array',
            'total_amount' => 'nullable|numeric|min:0',
            'company_id' => 'nullable|exists:companies,id',
            'agent_id' => 'nullable|exists:travel_agents,id',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        // A day-use booking blocks the room for that calendar day only.
        $checkOut = $isDayUse ? $checkIn->copy()->addDay() : Carbon::parse($request->check_out);
        $dayUseHours = $isDayUse ? (int) $request->input('day_use_hours', 4) : null;
        $nights = $checkIn->diffInDays($checkOut);
        $groupId = count($request->items) > 1 ? (string) \Illuminate\Support\Str::uuid() : null;

        // Rate override / discount permission gate
        if ($request->total_amount !== null && !auth('admin')->user()->hasPermission('can_override_rate')) {
            return back()->withInput()->withErrors([
                'total_amount' => 'You are not allowed to override the rate. Ask a manager, or clear the manual total.',
            ]);
        }

        $agent = $request->agent_id ? \App\Models\TravelAgent::find($request->agent_id) : null;

        $createdBookings = [];

        foreach ($request->items as $index => $itemData) {
            $roomType = \App\Models\RoomType::find($itemData['room_type_id']);
            $extraPersonTotal = ($itemData['extra_persons'] ?? 0) * $roomType->extra_person_price * $nights;
            $childrenCount = (int) ($itemData['children'] ?? 0);
            $childTotal = $childrenCount * ($roomType->child_price ?? 0) * $nights;

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

            if ($isDayUse) {
                $slabPrice = $dayUseHours >= 8
                    ? ($roomType->day_use_price_8h ?? $roomType->base_price)
                    : ($roomType->day_use_price_4h ?? round($roomType->base_price * 0.6, 2));
                $roomCharges = $slabPrice * $itemData['rooms'];
            } else {
                // Seasonal / weekend / festival rules — same engine the website uses
                $roomCharges = (new \App\Services\PricingService())->calculate(
                    $roomType,
                    $checkIn->toDateString(),
                    $checkOut->toDateString(),
                    (int) $itemData['rooms']
                );
            }

            $bookingPrice = $roomCharges + $extraPersonTotal + $childTotal + $itemServicesTotal;

            // Manual total override only for single-item bookings
            if ($request->total_amount !== null && count($request->items) === 1) {
                $bookingPrice = $request->total_amount;
            }

            $booking = Booking::create([
                'hotel_id' => auth('admin')->user()->hotel_id ?? 1,
                'group_id' => $groupId,
                'company_id' => $request->company_id,
                'agent_id' => $agent?->id,
                'agent_commission' => $agent ? round($bookingPrice * $agent->commission_percent / 100, 2) : 0,
                'room_type_id' => $itemData['room_type_id'],
                'check_in' => $checkIn->toDateString(),
                'check_out' => $checkOut->toDateString(),
                'booking_type' => $isDayUse ? 'day_use' : 'overnight',
                'day_use_hours' => $dayUseHours,
                'rooms' => (int) $itemData['rooms'],
                'total_amount' => $bookingPrice,
                'status' => 'confirmed',
                'services_json' => $itemServicesJson,
                'meta' => [
                    'guest_email' => $request->guest_email,
                    'source' => 'admin_manual',
                    'extra_persons' => (int) ($itemData['extra_persons'] ?? 0),
                    'children' => $childrenCount,
                    'is_group' => $groupId ? true : false,
                    'rate_overridden_by' => ($request->total_amount !== null && count($request->items) === 1)
                        ? auth('admin')->user()->name
                        : null,
                ],
            ]);

            $booking->guests()->create([
                'name' => $request->guest_name,
                'phone' => $request->guest_phone,
            ]);

            $createdBookings[] = $booking;
            ActivityLog::log('Booking Created', "New reservation #{$booking->id} for {$request->guest_name}" . ($groupId ? " (Group: {$groupId})" : ""), $booking);
        }

        // Send confirmation email + WhatsApp for the first one (usually enough for a group)
        if (!empty($createdBookings)) {
            $this->emailService->sendConfirmation($createdBookings[0]);
            event(new \App\Events\BookingConfirmed($createdBookings[0]));
        }

        $msg = count($createdBookings) > 1
            ? 'Bulk booking created successfully (' . count($createdBookings) . ' room types) and confirmation sent.'
            : 'Booking created successfully and confirmation email sent.';

        return redirect()->route('admin.bookings.index')->with('success', $msg);
    }

    public function show(Booking $booking)
    {
        $booking->load(['roomType', 'guests', 'company', 'assignedRooms', 'roomServiceOrders', 'activityLogs.admin']);

        // Keep the folio complete so the Folio tab is the single source of truth
        \App\Services\FolioService::sync($booking);
        $booking->load('folioItems');

        $loyalty = \App\Services\LoyaltyService::profile($booking->guests->first()->phone ?? null);
        view()->share('loyalty', $loyalty);
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
            
        // If in-house, also include current room in availability list (for moves)
         if ($booking->status === 'checked_in') {
             $currentRooms = $booking->assignedRooms;
             $availableRooms = $availableRooms->merge($currentRooms)->unique('id');
        }

        $services = \App\Models\Service::where('is_active', true)->get();

        return view('admin.bookings.show', compact('booking', 'groupMembers', 'availableRooms', 'services'));
    }


    
    // ... skipping directly to processCheckIn ...


    public function cancel(Booking $booking)
    {
        if (!auth('admin')->user()->hasPermission('can_void')) {
            return back()->with('error', 'You are not allowed to cancel bookings. Please ask a manager.');
        }

        $booking->update(['status' => 'cancelled']);

        ActivityLog::log('Booking Cancelled', "Reservation #{$booking->id} was cancelled by " . auth('admin')->user()->name, $booking);

        return back()->with('success', 'Booking cancelled');
    }

    public function markPaid(Booking $booking, Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,partial',
            'amount' => $request->type === 'partial' ? 'required|numeric|min:0.01' : 'nullable',
            'method' => 'required|string', // Allow custom strings
            'notes' => 'nullable|string'
        ]);

        if (!in_array($booking->status, ['confirmed', 'pending', 'checked_in', 'checked_out'])) {
            abort(400, 'Only confirmed, pending, or active bookings can be marked as paid');
        }

        // Night-audit day-close lock: no back-dated collections on closed days
        if ($booking->status === 'checked_out'
            && $booking->checked_out_at
            && day_is_closed($booking->checked_out_at)
            && !auth('admin')->user()->isSuperAdmin()) {
            return back()->with('error', 'That business day is closed by night audit. Only the owner (super admin) can post back-dated payments.');
        }

        $meta = $booking->meta ?? [];
        $payments = $meta['payments'] ?? [];

        $paymentAmount = $request->type === 'full' ? $booking->balance_amount : (float) $request->amount;

        $payments[] = [
            'amount' => $paymentAmount,
            'method' => $request->input('method'),
            'notes' => $request->input('notes'),
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
        } else {
             // Calculate required rooms count handling array accessor
             $requiredRooms = is_array($booking->rooms) ? array_sum($booking->rooms) : (int)$booking->rooms;
             
             if ($currentRooms->count() >= $requiredRooms) {
                // Use existing assigned rooms
                $rooms = $currentRooms;
             } else {
                return back()->with('error', 'Please assign rooms before checking in.');
             }
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

        // WhatsApp: thank-you + Google review nudge (reputation flow)
        $guest = $booking->guests->first();
        if ($guest && $guest->phone) {
            $reviewLink = trim((string) site('google_review_link', ''));
            if ($reviewLink !== '') {
                \App\Jobs\SendWhatsAppMessageJob::dispatch($guest->phone, 'review_request', [
                    $guest->name,
                    site('hotel_name', 'our hotel'),
                    $reviewLink,
                ]);
            }
            \App\Jobs\SendWhatsAppMessageJob::dispatch($guest->phone, 'checkout_thanks', [
                $guest->name,
                site('hotel_name', 'our hotel'),
            ]);
        }

        return back()->with('success', 'Guest checked out. Rooms marked dirty.');
    }

    public function invoice(Booking $booking)
    {
        $booking->load(['roomType', 'assignedRooms', 'company', 'guests', 'agent']);

        // Folio is the single source of truth — make sure it is complete first.
        \App\Services\FolioService::sync($booking);
        $booking->load('folioItems');

        $gst = \App\Services\GstService::forBooking($booking);

        // Invoice number keyed to the Indian financial year (Apr-Mar)
        $invDate = $booking->checked_out_at ?? now();
        $fyStart = $invDate->month >= 4 ? $invDate->year : $invDate->year - 1;
        $invoiceNumber = sprintf('INV/%02d%02d/%05d', $fyStart % 100, ($fyStart + 1) % 100, $booking->id);

        return view('admin.bookings.invoice', compact('booking', 'gst', 'invoiceNumber'));
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

    public function autoAssign(Booking $booking, \App\Services\RoomAssignmentService $assignmentService)
    {
        if ($booking->status !== 'confirmed') {
             return back()->with('error', 'Only confirmed bookings can be assigned.');
        }

        $room = $assignmentService->findBestRoom($booking);

        if (!$room) {
            return back()->with('error', 'No clean available rooms found for this date range.');
        }

        // Assign
        $booking->assignedRooms()->sync([$room->id]);

        return back()->with('success', "Auto-assigned Room {$room->room_number} to Booking #{$booking->id}");
    }

    public function smartCheckIn(Booking $booking, \App\Services\RoomAssignmentService $assignmentService)
    {
        $booking->load(['roomType', 'guests', 'assignedRooms', 'company', 'roomServiceOrders']);
        
        // For standard check-in OR modification of in-house guests
        if (!in_array($booking->status, ['confirmed', 'checked_in'])) {
             return back()->with('error', 'Booking cannot be modified (Status: ' . $booking->status . ')');
        }

        $availableRooms = $assignmentService->getAvailableRooms($booking);
        
        // If updating, include current room in the list even if occupied (by self)
        if ($booking->status === 'checked_in') {
             $currentRooms = $booking->assignedRooms;
             $availableRooms = $availableRooms->merge($currentRooms)->unique('id');
        }

        $services = \App\Models\Service::where('is_active', true)->get();
        $isUpdate = $booking->status === 'checked_in';

        return view('admin.bookings.smart-checkin', compact('booking', 'availableRooms', 'services', 'isUpdate'));
    }

    public function processCheckIn(Booking $booking, Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'services' => 'nullable|array' // Array of service IDs to add
        ]);

        $isCheckIn = $booking->status !== 'checked_in';
        $oldRoomIds = $booking->assignedRooms->pluck('id')->toArray();
        $newRoomId = (int) $request->room_id;

        // 1. Assign Room
        if (!in_array($newRoomId, $oldRoomIds)) {
            // Room Change Detected
            if (!$isCheckIn) {
                // Moving In-House Guest: Mark OLD room dirty
                \App\Models\Room::whereIn('id', $oldRoomIds)->update([
                    'status' => 'available', 
                    'housekeeping_status' => 'dirty'
                ]);
            }
            
            // Sync New Room
            $booking->assignedRooms()->sync([$newRoomId]);
        }
        
        $room = \App\Models\Room::find($newRoomId);

        // 2. Add Services / Upsells
        if ($request->has('services')) {
            foreach ($request->services as $serviceId => $qty) {
                 if ($qty > 0) {
                     $service = \App\Models\Service::find($serviceId);
                     if ($service) {
                         $booking->roomServiceOrders()->create([
                             'items' => [[
                                 'id' => $service->id,
                                 'name' => $service->name,
                                 'price' => $service->price,
                                 'qty' => $qty
                             ]],
                             'total_amount' => $service->price * $qty,
                             'status' => 'delivered',
                             'type' => 'service',
                         ]);
                     }
                 }
            }
        }

        // 3. Mark New Room Occupied
        $room->update(['status' => 'occupied']);

        // 4. Update Booking Status & Logs
        if ($isCheckIn) {
            $booking->update([
                'checked_in_at' => now(),
                'rechecked_by' => auth('admin')->id(),
                'status' => 'checked_in'
            ]);

            ActivityLog::log('Guest Checked-In (Smart)', "Smart Check-in for #{$booking->id} to Room {$room->room_number}", $booking);
            $this->emailService->sendWelcome($booking); // Only send welcome email on first check-in
            $msg = 'Guest checked in successfully!';
            return redirect()->route('admin.bookings.show', $booking)->with('success', $msg);
        } else {
            // Just update modified by?
            if (!in_array($newRoomId, $oldRoomIds)) {
                 ActivityLog::log('Room Moved', "Guest moved from previous room to Room {$room->room_number}", $booking);
                 $msg = 'Room moved and stay details updated!';
            } else {
                 ActivityLog::log('Booking Updated', "Stay details updated for #{$booking->id}", $booking);
                 $msg = 'Stay details updated successfully!';
            }
            return redirect()->route('admin.bookings.show', $booking)->with('success', $msg);
        }
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
                    'status' => $booking->operational_status,
                    'rooms' => $booking->rooms,
                ]
            ];
        });

        return response()->json($events);
    }

    public function addService(Booking $booking, Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $service = \App\Models\Service::find($request->service_id);
        $qty = (int) $request->quantity;

        // --- Constraint Validation ---
        if ($service->constraints && isset($service->constraints['max_quantity_rule'])) {
            $rule = $service->constraints['max_quantity_rule'];

            if ($rule === 'room_extra_capacity') {
                // Calculate total allowed extra capacity across all assigned rooms
                $maxAllowed = 0;
                foreach ($booking->assignedRooms as $room) {
                    $maxAllowed += $room->roomType->max_extra_persons ?? 0;
                }

                // Calculate CURRENTLY used quantity for this service
                $currentQty = 0;
                foreach ($booking->roomServiceOrders as $order) {
                    // $order->items is cast to array in model? No, it's JSON column. 
                    // Let's assume it's accessible as array or needs decoding.
                    // Improving robustness by decoding if string, though strict casting in model is better.
                    $items = is_string($order->items) ? json_decode($order->items, true) : $order->items;
                    
                    if (is_array($items)) {
                        foreach ($items as $item) {
                            if (isset($item['id']) && $item['id'] == $service->id) {
                                $currentQty += intval($item['qty'] ?? 0);
                            }
                        }
                    }
                }

                $newTotal = $currentQty + $qty;

                if ($newTotal > $maxAllowed) {
                    return back()->with('error', "Limit Reached: You already have {$currentQty} {$service->name}(s). Adding {$qty} more would exceed the limit of {$maxAllowed}.");
                }
            }
        }
        // -----------------------------

        $booking->roomServiceOrders()->create([
            'items' => [[
                'id' => $service->id,
                'name' => $service->name,
                'price' => $service->price,
                'qty' => $qty,
                'unit' => $service->price_unit ?? 'item'
            ]],
            'total_amount' => $service->price * $qty,
            'status' => 'delivered', // Auto-deliver key services for now
            'type' => 'service',
        ]);

        // Auto-charge to bill is handled by accessor or we need to ensure it's calculated.
        // Booking->total_bill accessor sums roomServiceOrders.

        ActivityLog::log('Service Added', "Added {$qty} x {$service->name} to bill", $booking);

        return back()->with('success', "Added {$service->name} to folio.");
    }
}


// Audit Trace
