<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingGuest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ComplianceController extends Controller
{
    /**
     * Daily guest register (Form-A style) for police verification:
     * every guest who checked in on the selected date, with ID details.
     */
    public function policeRegister(Request $request)
    {
        $date = Carbon::parse($request->input('date', now()->toDateString()));

        $guests = BookingGuest::whereHas('booking', function ($q) use ($date) {
            $q->whereDate('checked_in_at', $date->toDateString());
        })
            ->with(['booking.roomType', 'booking.assignedRooms'])
            ->orderBy('booking_id')
            ->get();

        return view('admin.compliance.police-register', compact('guests', 'date'));
    }

    /**
     * Foreign guests list (nationality other than Indian) for FRRO / C-Form filing.
     */
    public function cformIndex(Request $request)
    {
        $from = Carbon::parse($request->input('from', now()->subDays(30)->toDateString()));
        $to = Carbon::parse($request->input('to', now()->toDateString()));

        $guests = BookingGuest::query()
            ->whereNotNull('nationality')
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(TRIM(nationality))'), ['indian', 'india', 'in', ''])
            ->whereHas('booking', function ($q) use ($from, $to) {
                $q->whereBetween('check_in', [$from->toDateString(), $to->toDateString()]);
            })
            ->with(['booking.roomType', 'booking.assignedRooms'])
            ->orderByDesc('booking_id')
            ->paginate(25)
            ->withQueryString();

        return view('admin.compliance.cform-index', compact('guests', 'from', 'to'));
    }

    /**
     * Printable C-Form for one foreign guest.
     */
    public function cformShow(BookingGuest $guest)
    {
        $guest->load(['booking.roomType', 'booking.assignedRooms', 'booking.hotel']);

        return view('admin.compliance.cform-show', compact('guest'));
    }

    /**
     * CSV export of foreign guest arrivals for the FRRO portal.
     */
    public function cformExport(Request $request): StreamedResponse
    {
        $from = Carbon::parse($request->input('from', now()->subDays(30)->toDateString()));
        $to = Carbon::parse($request->input('to', now()->toDateString()));

        $guests = BookingGuest::query()
            ->whereNotNull('nationality')
            ->whereNotIn(\Illuminate\Support\Facades\DB::raw('LOWER(TRIM(nationality))'), ['indian', 'india', 'in', ''])
            ->whereHas('booking', function ($q) use ($from, $to) {
                $q->whereBetween('check_in', [$from->toDateString(), $to->toDateString()]);
            })
            ->with(['booking.roomType', 'booking.assignedRooms'])
            ->orderBy('booking_id')
            ->get();

        return response()->streamDownload(function () use ($guests) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Guest Name', 'Nationality', 'Passport/ID Type', 'ID Number', 'Visa Number', 'Visa Expiry',
                'Arrived From', 'Next Destination', 'Purpose of Visit', 'Check-in', 'Check-out',
                'Room', 'Phone', 'Email',
            ]);
            foreach ($guests as $g) {
                fputcsv($out, [
                    $g->name,
                    $g->nationality,
                    $g->id_type,
                    $g->id_number,
                    $g->visa_number,
                    optional($g->visa_expiry)->format('Y-m-d'),
                    $g->arrived_from,
                    $g->next_destination,
                    $g->purpose_of_visit,
                    optional($g->booking->check_in)->format('Y-m-d'),
                    optional($g->booking->check_out)->format('Y-m-d'),
                    $g->booking->assignedRooms->pluck('room_number')->join(' '),
                    $g->phone,
                    $g->email,
                ]);
            }
            fclose($out);
        }, 'cform_export_' . $from->format('Ymd') . '_' . $to->format('Ymd') . '.csv', ['Content-Type' => 'text/csv']);
    }
}
