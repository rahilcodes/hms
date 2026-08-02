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
            body { background: #fff; }
            .no-print { display: none !important; }
            .invoice-sheet { box-shadow: none !important; margin: 0 !important; border: none !important; border-radius: 0 !important; }
        }
    </style>
</head>

<body class="text-slate-800 font-sans">
    <div class="no-print max-w-3xl mx-auto px-4 pt-4 flex justify-between">
        <a href="{{ route('admin.banquets.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-sm font-bold text-slate-600">&larr; Back</a>
        <button onclick="window.print()" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold">Print / Save PDF</button>
    </div>

    <div class="invoice-sheet max-w-3xl mx-auto my-4 bg-white rounded-2xl shadow-lg border border-slate-200 p-6 sm:p-10">
        <div class="flex flex-col sm:flex-row justify-between gap-6 pb-6 border-b-2 border-slate-800">
            <div>
                <h1 class="text-2xl font-black tracking-tight">{{ site('hotel_name', 'Hotel') }}</h1>
                <p class="text-xs text-slate-500 mt-1">Phone: {{ site('hotel_phone', '') }}</p>
                @if(trim((string) site('hotel_gstin', '')) !== '')
                    <p class="text-xs font-bold mt-2">GSTIN: {{ site('hotel_gstin') }}</p>
                @endif
            </div>
            <div class="sm:text-right">
                <p class="text-lg font-black uppercase tracking-widest text-slate-700">Banquet Tax Invoice</p>
                <p class="text-sm font-bold mt-1">{{ $invoiceNumber }}</p>
                <p class="text-xs text-slate-500 mt-1">Event: {{ $event->event_date->format('d M Y') }} &bull; {{ ucfirst($event->event_type) }}</p>
            </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-6 py-6 border-b border-slate-200">
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">Billed To</p>
                <p class="font-bold">{{ $event->company->name ?? $event->customer_name }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $event->customer_phone }} @if($event->customer_email) &bull; {{ $event->customer_email }} @endif</p>
                @php $gstin = $event->customer_gstin ?: ($event->company->gst_number ?? null); @endphp
                @if($gstin)
                    <p class="text-xs font-bold mt-1">GSTIN: {{ $gstin }}</p>
                @endif
            </div>
            <div class="sm:text-right text-xs text-slate-600 space-y-1">
                <p><span class="font-bold">Hall:</span> {{ $event->hall->name ?? '—' }}</p>
                <p><span class="font-bold">Guests:</span> {{ $event->guests_expected }} pax</p>
                @if($event->start_time)<p><span class="font-bold">Time:</span> {{ $event->start_time }} – {{ $event->end_time }}</p>@endif
            </div>
        </div>

        <div class="overflow-x-auto py-6">
            <table class="w-full text-sm min-w-[520px]">
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
                    @foreach($gst['lines'] as $line)
                        <tr class="border-b border-slate-100">
                            <td class="py-2.5 pr-2">{{ $line['description'] }}</td>
                            <td class="py-2.5 text-right text-xs text-slate-400">{{ $line['sac'] }}</td>
                            <td class="py-2.5 text-right">{{ number_format($line['taxable'], 2) }}</td>
                            <td class="py-2.5 text-right">{{ rtrim(rtrim(number_format($line['rate'], 1), '0'), '.') }}%</td>
                            <td class="py-2.5 text-right">{{ number_format($line['cgst'], 2) }}</td>
                            <td class="py-2.5 text-right">{{ number_format($line['sgst'], 2) }}</td>
                            <td class="py-2.5 text-right font-bold">{{ number_format($line['gross'], 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid sm:grid-cols-2 gap-8 pb-6 border-b border-slate-200">
            <p class="text-[11px] text-slate-500 italic self-end leading-relaxed">{{ amount_in_words($gst['gross_total']) }}</p>
            <div class="text-sm space-y-2 sm:text-right">
                <div class="flex justify-between sm:justify-end sm:gap-10"><span class="text-slate-500">Taxable Value</span><span class="font-bold">₹{{ number_format($gst['taxable_total'], 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10"><span class="text-slate-500">CGST</span><span class="font-bold">₹{{ number_format($gst['cgst_total'], 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10"><span class="text-slate-500">SGST</span><span class="font-bold">₹{{ number_format($gst['sgst_total'], 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10 text-base border-t border-slate-200 pt-2"><span class="font-black">Grand Total</span><span class="font-black">₹{{ number_format($gst['gross_total'], 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10 text-emerald-600"><span>Advance Paid</span><span class="font-bold">₹{{ number_format($event->advance_paid, 2) }}</span></div>
                <div class="flex justify-between sm:justify-end sm:gap-10 {{ $event->balance_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                    <span class="font-black">Balance Due</span><span class="font-black">₹{{ number_format(max(0, $event->balance_amount), 2) }}</span>
                </div>
            </div>
        </div>

        <div class="pt-6">
            @php $upi = $event->balance_amount > 0 ? upi_uri((float) $event->balance_amount, 'Banquet #' . $event->id) : null; @endphp
            @if($upi)
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Scan &amp; Pay Balance via UPI</p>
                <div data-upi-qr="{{ $upi }}" data-qr-size="150" class="inline-block p-2 bg-white border border-slate-200 rounded-xl"></div>
                <p class="text-xs text-slate-500 mt-1.5">{{ site('hotel_upi_vpa') }}</p>
            @endif
        </div>

        <div class="mt-10 pt-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between gap-2 text-[10px] text-slate-400">
            <p>Prices are inclusive of GST. E. &amp; O.E.</p>
            <p>Authorised Signatory — {{ site('hotel_name', 'Hotel') }}</p>
        </div>
    </div>
</body>

</html>
