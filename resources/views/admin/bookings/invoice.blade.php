<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoiceNumber }} | {{ site('hotel_name', 'Hotel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/qr.js'])
    <style>
        body { background: #f1f5f9; }
        @media print {
            body { background: #fff; -webkit-print-color-adjust: exact; }
            .no-print { display: none !important; }
            .invoice-sheet { box-shadow: none !important; margin: 0 !important; border: none !important; border-radius: 0 !important; }
        }
    </style>
</head>

<body class="text-slate-800 font-sans">
    <div class="no-print max-w-4xl mx-auto px-4 pt-4 flex items-center justify-between gap-3">
        <a href="{{ url()->previous() }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-600">&larr; Back</a>
        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold shadow">Print / Save PDF</button>
    </div>

    <div class="invoice-sheet max-w-4xl mx-auto my-4 bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sm:p-10">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between gap-6 pb-6 border-b-2 border-slate-800">
            <div>
                <h1 class="text-2xl font-black tracking-tight">{{ site('hotel_name', $booking->hotel->name ?? 'Hotel') }}</h1>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    {{ $booking->hotel->address ?? '' }}<br>
                    Phone: {{ site('hotel_phone', $booking->hotel->phone ?? '') }}
                    @if($booking->hotel->email) &bull; {{ $booking->hotel->email }} @endif
                </p>
                @if(trim((string) site('hotel_gstin', '')) !== '')
                    <p class="text-xs font-bold mt-2">GSTIN: {{ site('hotel_gstin') }}</p>
                @endif
                @if(trim((string) site('hotel_fssai', '')) !== '')
                    <p class="text-xs text-slate-500">FSSAI: {{ site('hotel_fssai') }}</p>
                @endif
            </div>
            <div class="sm:text-right">
                <p class="text-lg font-black uppercase tracking-widest text-slate-700">Tax Invoice</p>
                <p class="text-sm font-bold mt-1">{{ $invoiceNumber }}</p>
                <p class="text-xs text-slate-500 mt-1">Date: {{ ($booking->checked_out_at ?? now())->format('d M Y') }}</p>
                <p class="text-xs text-slate-500">Booking Ref: #{{ $booking->id + 1000 }}</p>
            </div>
        </div>

        {{-- BILL TO / STAY --}}
        <div class="grid sm:grid-cols-2 gap-6 py-6 border-b border-slate-200">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Billed To</p>
                <p class="font-bold">{{ $booking->company->name ?? $booking->guests->first()->name ?? 'Guest' }}</p>
                @if($booking->company)
                    <p class="text-xs text-slate-500 mt-0.5">{{ $booking->company->address }}</p>
                    @if($booking->company->gst_number)
                        <p class="text-xs font-bold mt-1">GSTIN: {{ $booking->company->gst_number }}</p>
                    @endif
                    <p class="text-xs text-slate-400 mt-1">Guest: {{ $booking->guests->first()->name ?? '—' }}</p>
                @else
                    <p class="text-xs text-slate-500 mt-0.5">{{ $booking->guests->first()->phone ?? '' }}</p>
                    <p class="text-xs text-slate-500">{{ $booking->guests->first()->email ?? ($booking->meta['guest_email'] ?? '') }}</p>
                @endif
            </div>
            <div class="sm:text-right text-xs text-slate-600 space-y-1">
                <p><span class="font-bold">Room:</span> {{ $booking->roomType->name ?? '—' }}
                    @if($booking->assignedRooms->isNotEmpty())
                        ({{ $booking->assignedRooms->pluck('room_number')->join(', ') }})
                    @endif
                </p>
                @if($booking->isDayUse())
                    <p><span class="font-bold">Day Use:</span> {{ $booking->check_in->format('d M Y') }} ({{ $booking->day_use_hours }} hrs)</p>
                @else
                    <p><span class="font-bold">Check-in:</span> {{ $booking->check_in->format('d M Y') }} &nbsp; <span class="font-bold">Check-out:</span> {{ $booking->check_out->format('d M Y') }}</p>
                    <p><span class="font-bold">Nights:</span> {{ $booking->nights }}</p>
                @endif
                @if($booking->agent)
                    <p><span class="font-bold">Booked via:</span> {{ $booking->agent->agency_name ?? $booking->agent->name }}</p>
                @endif
            </div>
        </div>

        {{-- LINE ITEMS --}}
        <div class="overflow-x-auto py-6">
            <table class="w-full text-sm min-w-[540px]">
                <thead>
                    <tr class="text-[10px] uppercase tracking-widest text-slate-400 border-b border-slate-200">
                        <th class="text-left py-2 font-black">Description</th>
                        <th class="text-right py-2 font-black">SAC</th>
                        <th class="text-right py-2 font-black">Taxable ₹</th>
                        <th class="text-right py-2 font-black">GST %</th>
                        <th class="text-right py-2 font-black">CGST ₹</th>
                        <th class="text-right py-2 font-black">SGST ₹</th>
                        <th class="text-right py-2 font-black">Total ₹</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gst['lines'] as $line)
                        <tr class="border-b border-slate-100">
                            <td class="py-2.5 pr-2">{{ $line['description'] }}</td>
                            <td class="py-2.5 text-right text-xs text-slate-400">{{ $line['sac'] }}</td>
                            <td class="py-2.5 text-right">{{ number_format($line['taxable'], 2) }}</td>
                            <td class="py-2.5 text-right">{{ rtrim(rtrim(number_format($line['rate'], 1), '0'), '.') }}%</td>
                            <td class="py-2.5 text-right">{{ number_format($line['cgst'], 2) }}</td>
                            <td class="py-2.5 text-right">{{ number_format($line['sgst'], 2) }}</td>
                            <td class="py-2.5 text-right font-bold">{{ number_format($line['gross'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="py-6 text-center text-slate-400">No charges posted.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- GST SUMMARY + TOTALS --}}
        <div class="grid sm:grid-cols-2 gap-8 pb-6 border-b border-slate-200">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">GST Summary</p>
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-slate-400 border-b border-slate-100">
                            <th class="text-left py-1 font-bold">Rate</th>
                            <th class="text-right py-1 font-bold">Taxable ₹</th>
                            <th class="text-right py-1 font-bold">CGST ₹</th>
                            <th class="text-right py-1 font-bold">SGST ₹</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gst['by_rate'] as $r)
                            <tr class="border-b border-slate-50">
                                <td class="py-1">{{ rtrim(rtrim(number_format($r['rate'], 1), '0'), '.') }}%</td>
                                <td class="py-1 text-right">{{ number_format($r['taxable'], 2) }}</td>
                                <td class="py-1 text-right">{{ number_format($r['cgst'], 2) }}</td>
                                <td class="py-1 text-right">{{ number_format($r['sgst'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p class="text-[11px] text-slate-500 mt-3 italic leading-relaxed">
                    {{ amount_in_words($gst['gross_total']) }}
                </p>
            </div>
            <div class="text-sm space-y-2 sm:text-right">
                <div class="flex justify-between sm:justify-end sm:gap-10"><span class="text-slate-500">Taxable Value</span><span class="font-bold">₹{{ number_format($gst['taxable_total'], 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10"><span class="text-slate-500">CGST</span><span class="font-bold">₹{{ number_format($gst['cgst_total'], 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10"><span class="text-slate-500">SGST</span><span class="font-bold">₹{{ number_format($gst['sgst_total'], 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10 text-base border-t border-slate-200 pt-2"><span class="font-black">Grand Total</span><span class="font-black">₹{{ number_format($gst['gross_total'], 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10 text-emerald-600"><span>Paid</span><span class="font-bold">₹{{ number_format($booking->paid_amount, 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10 {{ $booking->balance_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                    <span class="font-black">Balance Due</span><span class="font-black">₹{{ number_format(max(0, $booking->balance_amount), 2) }}</span>
                </div>
            </div>
        </div>

        {{-- UPI + PAYMENTS --}}
        <div class="grid sm:grid-cols-2 gap-8 pt-6">
            <div>
                @php $upi = $booking->balance_amount > 0 ? upi_uri((float) $booking->balance_amount, 'Booking #' . ($booking->id + 1000)) : null; @endphp
                @if($upi)
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Scan &amp; Pay Balance via UPI</p>
                    <div data-upi-qr="{{ $upi }}" data-qr-size="160" class="inline-block p-2 bg-white border border-slate-200 rounded-xl"></div>
                    <p class="text-xs text-slate-500 mt-1.5">{{ site('hotel_upi_vpa') }}</p>
                @endif
            </div>
            <div class="sm:text-right">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Payment History</p>
                @php $payLog = collect($booking->meta['payments'] ?? []); @endphp
                @if($booking->meta['advance_paid'] ?? 0)
                    <p class="text-xs text-slate-600">Advance: ₹{{ number_format($booking->meta['advance_paid'], 2) }}</p>
                @endif
                @forelse($payLog as $p)
                    <p class="text-xs text-slate-600">
                        ₹{{ number_format($p['amount'] ?? 0, 2) }} — {{ ucfirst($p['method'] ?? 'cash') }}
                        <span class="text-slate-400">{{ isset($p['timestamp']) ? \Carbon\Carbon::parse($p['timestamp'])->format('d M Y') : '' }}</span>
                    </p>
                @empty
                    @if(!($booking->meta['advance_paid'] ?? 0))
                        <p class="text-xs text-slate-400">No payments recorded.</p>
                    @endif
                @endforelse
            </div>
        </div>

        <div class="mt-10 pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between gap-2 text-[10px] text-slate-400">
            <p>Prices are inclusive of GST. E. &amp; O.E. Subject to local jurisdiction.</p>
            <p>Authorised Signatory — {{ site('hotel_name', 'Hotel') }}</p>
        </div>
    </div>
</body>

</html>
