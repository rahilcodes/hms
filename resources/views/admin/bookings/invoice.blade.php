<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $booking->id + 1000 }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-white text-slate-900 font-sans p-8 max-w-4xl mx-auto">

    <!-- Header -->
    <div class="flex justify-between items-start mb-12">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">INVOICE</h1>
            <p class="text-slate-500 font-medium mt-1">#INV-{{ $booking->id + 1000 }}</p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-slate-800">Hotel CRM Ultra</h2>
            <p class="text-sm text-slate-500">123 Luxury Avenue, Metropolis</p>
            <p class="text-sm text-slate-500">contact@hotelultra.com | +1 234 567 890</p>
        </div>
    </div>

    <!-- Bill To / details -->
    <div class="flex justify-between mb-8 pb-8 border-b border-slate-100">
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Bill To</p>
            <h3 class="text-lg font-bold text-slate-900">{{ $booking->guest_name }}</h3>
            @if($booking->company)
                <p class="text-sm font-medium text-slate-600">{{ $booking->company->name }} (Corporate)</p>
                <p class="text-xs text-slate-400 mt-1">GST: {{ $booking->company->gst_number }}</p>
            @endif
            @if($booking->guests->first()->phone)
                <p class="text-sm text-slate-500 mt-1">{{ $booking->guests->first()->phone }}</p>
            @endif
        </div>
        <div class="text-right">
            <div class="mb-2">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Check-in</span>
                <span
                    class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($booking->check_in)->format('d M, Y') }}</span>
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest block">Check-out</span>
                <span
                    class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($booking->check_out)->format('d M, Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Line Items -->
    <table class="w-full mb-12">
        <thead>
            <tr class="text-left text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-200">
                <th class="pb-3">Description</th>
                <th class="pb-3 text-right">Qty / Nights</th>
                <th class="pb-3 text-right">Rate</th>
                <th class="pb-3 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="text-sm">
            <!-- Room Charges -->
            <tr class="border-b border-slate-50">
                <td class="py-4">
                    <p class="font-bold text-slate-800">{{ $booking->roomType->name }} Accommodation</p>
                    <p class="text-xs text-slate-500">
                        {{ $booking->assignedRooms->pluck('room_number')->implode(', ') ?: 'Unassigned Room' }}
                    </p>
                </td>
                <td class="py-4 text-right">
                    {{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} Nights
                    x {{ array_sum($booking->rooms ?? []) }} Rooms
                </td>
                <td class="py-4 text-right">₹{{ number_format($booking->roomType->base_price) }}</td>
                <td class="py-4 text-right font-bold">
                    @php
                        $nights = \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out);
                        $rooms = array_sum($booking->rooms ?? []);
                        $roomTotal = $booking->roomType->base_price * $rooms * $nights;
                    @endphp
                    ₹{{ number_format($roomTotal) }}
                </td>
            </tr>

            <!-- Extra Person Charges -->
            @if(($booking->meta['extra_persons'] ?? 0) > 0)
                <tr class="border-b border-slate-50">
                    <td class="py-4">
                        <p class="font-bold text-slate-800">Extra Person Charges</p>
                    </td>
                    <td class="py-4 text-right">
                        {{ $booking->meta['extra_persons'] }} Person(s) x {{ $nights }} Nights
                    </td>
                    <td class="py-4 text-right">₹{{ number_format($booking->roomType->extra_person_price) }}</td>
                    <td class="py-4 text-right font-bold">
                        ₹{{ number_format($booking->meta['extra_persons'] * $booking->roomType->extra_person_price * $nights) }}
                    </td>
                </tr>
            @endif

            <!-- Services -->
            @if(!empty($booking->services_json))
                @foreach($booking->services_json as $service)
                    <tr class="border-b border-slate-50">
                        <td class="py-4">
                            <p class="font-bold text-slate-800">{{ $service['name'] }}</p>
                            <p class="text-xs text-slate-500">Add-on Service</p>
                        </td>
                        <td class="py-4 text-right">
                            {{ $service['qty'] ?? 1 }}
                            {{ isset($service['price_unit']) && $service['price_unit'] == 'per_night' ? ' x ' . $nights . ' Nights' : '' }}
                        </td>
                        <td class="py-4 text-right">₹{{ number_format($service['price']) }}</td>
                        <td class="py-4 text-right font-bold">
                            @php
                                $sTotal = $service['price'] * ($service['qty'] ?? 1);
                                if (isset($service['price_unit']) && $service['price_unit'] == 'per_night')
                                    $sTotal *= $nights;
                            @endphp
                            ₹{{ number_format($sTotal) }}
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Totals -->
    <div class="flex justify-end mb-12">
        <div class="w-1/2 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="font-bold text-slate-500">Subtotal</span>
                <span class="font-bold text-slate-800">₹{{ number_format($booking->total_amount) }}</span>
            </div>
            <!-- Tax placeholder if we sort that out later -->

            <div class="flex justify-between items-center pt-4 border-t border-slate-200">
                <span class="text-lg font-black text-slate-900">Total Due</span>
                <span class="text-lg font-black text-blue-600">₹{{ number_format($booking->total_amount) }}</span>
            </div>

            <div class="flex justify-between text-sm pt-4">
                <span class="font-bold text-emerald-600">Paid</span>
                <span class="font-bold text-emerald-600">₹{{ number_format($booking->paid_amount) }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="font-bold {{ $booking->balance_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">Balance
                    Pending</span>
                <span
                    class="font-bold {{ $booking->balance_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">₹{{ number_format($booking->balance_amount) }}</span>
            </div>
        </div>
    </div>

    <!-- Footer / Payments Info -->
    <div class="bg-slate-50 rounded-xl p-6 border border-slate-100">
        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Payment History</h4>
        <div class="space-y-2">
            @if(($booking->meta['advance_paid'] ?? 0) > 0)
                <div class="flex justify-between text-xs">
                    <span class="text-slate-600">Advance Payment (Online/Initial)</span>
                    <span class="font-bold text-slate-900">₹{{ number_format($booking->meta['advance_paid']) }}</span>
                </div>
            @endif
            @foreach($booking->meta['payments'] ?? [] as $payment)
                <div class="flex justify-between text-xs">
                    <span class="text-slate-600">
                        {{ \Carbon\Carbon::parse($payment['timestamp'])->format('d M Y') }} -
                        {{ strtoupper($payment['method'] ?? 'CASH') }}
                    </span>
                    <span class="font-bold text-slate-900">₹{{ number_format($payment['amount']) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Print Button (No Print) -->
    <div class="fixed bottom-8 right-8 no-print">
        <button onclick="window.print()"
            class="px-6 py-3 bg-blue-600 text-white font-bold rounded-full shadow-xl hover:bg-blue-700 transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print Invoice
        </button>
    </div>

</body>

</html>