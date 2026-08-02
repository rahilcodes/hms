<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C-Form — {{ $guest->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body { background: #f1f5f9; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .sheet { box-shadow: none !important; border: none !important; margin: 0 !important; }
        }
    </style>
</head>

<body class="text-slate-800 font-sans">
    <div class="no-print max-w-3xl mx-auto px-4 pt-4 flex justify-between">
        <a href="{{ route('admin.compliance.cform.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-600">&larr; Back</a>
        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold">Print</button>
    </div>

    <div class="sheet max-w-3xl mx-auto my-4 bg-white rounded-2xl shadow-lg border border-slate-200 p-8 sm:p-10">
        <div class="text-center pb-5 border-b-2 border-slate-800">
            <h1 class="text-lg font-black uppercase tracking-widest">Form C</h1>
            <p class="text-xs text-slate-500 mt-1">[See Rule 14 of the Registration of Foreigners Rules, 1992]</p>
            <p class="text-sm font-bold mt-2">Arrival Report of Foreigner — {{ site('hotel_name', $guest->booking->hotel->name ?? 'Hotel') }}</p>
            <p class="text-xs text-slate-500">{{ $guest->booking->hotel->address ?? '' }}</p>
        </div>

        @php
            $rows = [
                'Name of the Foreigner' => $guest->name,
                'Nationality' => $guest->nationality,
                'Passport / ID Type' => $guest->id_type ? ucwords(str_replace('_', ' ', $guest->id_type)) : '—',
                'Passport / ID Number' => $guest->id_number ?: '—',
                'Visa Number' => $guest->visa_number ?: '—',
                'Visa Valid Till' => optional($guest->visa_expiry)->format('d M Y') ?: '—',
                'Address in India' => ($guest->booking->hotel->name ?? '') . ', ' . ($guest->booking->hotel->address ?? ''),
                'Permanent Address' => $guest->address ?: '—',
                'Arrived From' => $guest->arrived_from ?: '—',
                'Proceeding To (Next Destination)' => $guest->next_destination ?: '—',
                'Purpose of Visit' => $guest->purpose_of_visit ?: '—',
                'Date of Arrival at Hotel' => optional($guest->booking->checked_in_at ?? $guest->booking->check_in)->format('d M Y'),
                'Intended Date of Departure' => optional($guest->booking->check_out)->format('d M Y'),
                'Room Number' => $guest->booking->assignedRooms->pluck('room_number')->join(', ') ?: ($guest->booking->roomType->name ?? '—'),
                'Phone' => $guest->phone ?: '—',
                'Email' => $guest->email ?: '—',
            ];
        @endphp

        <table class="w-full text-sm mt-6">
            @foreach($rows as $label => $value)
                <tr class="border-b border-slate-100">
                    <td class="py-2.5 pr-4 text-slate-500 w-1/2 align-top">{{ $loop->iteration }}. {{ $label }}</td>
                    <td class="py-2.5 font-bold">{{ $value }}</td>
                </tr>
            @endforeach
        </table>

        <div class="grid grid-cols-2 gap-8 mt-14 text-xs">
            <div>
                <div class="border-t border-slate-300 pt-2">Signature of Foreigner</div>
            </div>
            <div class="text-right">
                <div class="border-t border-slate-300 pt-2">Signature of Hotel Manager<br>Date: {{ now()->format('d M Y') }}</div>
            </div>
        </div>

        <p class="mt-8 text-[10px] text-slate-400">Submit to the FRRO / local police station within 24 hours of arrival. Online filing: indianfrro.gov.in</p>
    </div>
</body>

</html>
