<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BanquetBooking;
use App\Models\BanquetHall;
use App\Models\Company;
use App\Services\GstService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BanquetController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month')
            ? Carbon::createFromFormat('Y-m', $request->input('month'))->startOfMonth()
            : now()->startOfMonth();

        $events = BanquetBooking::with('hall')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->whereBetween('event_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
            ->orderBy('event_date')
            ->get();

        $halls = BanquetHall::withCount([
            'bookings as month_events' => fn ($q) => $q
                ->whereBetween('event_date', [$month->copy()->startOfMonth(), $month->copy()->endOfMonth()])
                ->where('status', '!=', 'cancelled'),
        ])->get();

        $stats = [
            'month_revenue' => $events->where('status', '!=', 'cancelled')->sum(fn ($e) => $e->total_amount),
            'month_advance' => $events->where('status', '!=', 'cancelled')->sum('advance_paid'),
            'confirmed' => $events->where('status', 'confirmed')->count(),
            'enquiries' => $events->where('status', 'enquiry')->count(),
        ];

        // Day-wise map for the mini calendar strip
        $eventsByDay = $events->groupBy(fn ($e) => $e->event_date->format('Y-m-d'));

        return view('admin.banquets.index', compact('events', 'halls', 'stats', 'month', 'eventsByDay'));
    }

    public function create()
    {
        $halls = BanquetHall::where('is_active', true)->get();
        $companies = Company::where('is_active', true)->get();

        return view('admin.banquets.create', ['halls' => $halls, 'companies' => $companies, 'event' => null]);
    }

    public function edit(BanquetBooking $banquet)
    {
        $halls = BanquetHall::where('is_active', true)->get();
        $companies = Company::where('is_active', true)->get();

        return view('admin.banquets.create', ['halls' => $halls, 'companies' => $companies, 'event' => $banquet]);
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'banquet_hall_id' => 'required|exists:banquet_halls,id',
            'company_id' => 'nullable|exists:companies,id',
            'customer_name' => 'required|string|max:150',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'customer_gstin' => 'nullable|string|max:20',
            'event_type' => 'required|in:' . implode(',', BanquetBooking::EVENT_TYPES),
            'event_date' => 'required|date',
            'start_time' => 'nullable|string|max:10',
            'end_time' => 'nullable|string|max:10',
            'guests_expected' => 'nullable|integer|min:0',
            'per_plate_rate' => 'nullable|numeric|min:0',
            'food_plates' => 'nullable|integer|min:0',
            'hall_rent' => 'nullable|numeric|min:0',
            'decoration_charge' => 'nullable|numeric|min:0',
            'other_charges' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'advance_paid' => 'nullable|numeric|min:0',
            'status' => 'required|in:' . implode(',', BanquetBooking::STATUSES),
            'notes' => 'nullable|string|max:2000',
        ]);
    }

    protected function assertHallFree(array $data, ?int $ignoreId = null): void
    {
        if (($data['status'] ?? '') === 'cancelled') {
            return;
        }
        $clash = BanquetBooking::where('banquet_hall_id', $data['banquet_hall_id'])
            ->whereDate('event_date', $data['event_date'])
            ->where('status', '!=', 'cancelled')
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists();

        if ($clash) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'event_date' => 'This hall already has a non-cancelled event on that date.',
            ]);
        }
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $this->assertHallFree($data);

        $data['hotel_id'] = auth('admin')->user()->hotel_id ?? 1;
        $data['created_by'] = auth('admin')->id();
        $event = BanquetBooking::create($data);

        ActivityLog::log('Banquet Created', "Banquet #{$event->id} ({$event->customer_name}, {$event->event_type}) on {$event->event_date->format('d M Y')}");

        return redirect()->route('admin.banquets.index', ['month' => $event->event_date->format('Y-m')])
            ->with('success', 'Banquet booking saved.');
    }

    public function update(Request $request, BanquetBooking $banquet)
    {
        $data = $this->validated($request);
        $this->assertHallFree($data, $banquet->id);

        $banquet->update($data);
        ActivityLog::log('Banquet Updated', "Banquet #{$banquet->id} ({$banquet->customer_name}) updated");

        return redirect()->route('admin.banquets.index', ['month' => $banquet->event_date->format('Y-m')])
            ->with('success', 'Banquet booking updated.');
    }

    public function invoice(BanquetBooking $banquet)
    {
        $banquet->load(['hall', 'company']);
        $gst = GstService::forBanquet($banquet);

        $fyStart = $banquet->event_date->month >= 4 ? $banquet->event_date->year : $banquet->event_date->year - 1;
        $invoiceNumber = sprintf('BQT/%02d%02d/%05d', $fyStart % 100, ($fyStart + 1) % 100, $banquet->id);

        return view('admin.banquets.invoice', ['event' => $banquet, 'gst' => $gst, 'invoiceNumber' => $invoiceNumber]);
    }

    public function storeHall(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'capacity' => 'required|integer|min:1',
            'base_rent' => 'required|numeric|min:0',
        ]);
        $data['hotel_id'] = auth('admin')->user()->hotel_id ?? 1;
        BanquetHall::create($data);

        return back()->with('success', 'Hall added.');
    }
}
