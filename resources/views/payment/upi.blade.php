<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Advance | {{ site('hotel_name', 'Hotel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/qr.js'])
</head>

<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 font-sans">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 p-6 sm:p-8 text-center">
        <h1 class="text-lg font-black text-slate-800">{{ site('hotel_name', 'Hotel') }}</h1>
        <p class="text-xs text-slate-500 mt-1">Booking #{{ $booking->id + 1000 }} &bull; {{ $booking->check_in->format('d M') }} → {{ $booking->check_out->format('d M Y') }}</p>

        <div class="my-6">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Advance to Confirm</p>
            <p class="text-4xl font-black text-slate-800 mt-1">₹{{ number_format($amountDue, 2) }}</p>
        </div>

        @if($upi)
            <div class="flex justify-center">
                <div data-upi-qr="{{ $upi }}" data-qr-size="200" class="p-3 bg-white border-2 border-slate-200 rounded-2xl"></div>
            </div>
            <p class="text-xs text-slate-500 mt-3">Scan with any UPI app (GPay, PhonePe, Paytm)<br>or pay to <span class="font-bold">{{ site('hotel_upi_vpa') }}</span></p>
            <a href="{{ $upi }}" class="mt-4 inline-block w-full py-3.5 rounded-xl bg-emerald-600 text-white text-sm font-black shadow">Open UPI App &rarr;</a>
            <div class="mt-5 rounded-xl bg-amber-50 border border-amber-100 p-3.5 text-left">
                <p class="text-xs text-amber-700 leading-relaxed">
                    <span class="font-bold">After paying:</span> your booking will be confirmed by our front desk as soon as the payment reflects — usually within a few minutes. You'll receive a confirmation on WhatsApp/email. Unpaid bookings are released after 30 minutes.
                </p>
            </div>
        @else
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-sm text-slate-600">Online payment is not available right now. Your booking is held as <span class="font-bold">pending</span> — please call us on
                    <a class="font-bold text-blue-600" href="tel:{{ site('hotel_phone', '') }}">{{ site('hotel_phone', 'the hotel') }}</a> to confirm.
                </p>
            </div>
        @endif

        <a href="{{ url('/') }}" class="mt-5 inline-block text-xs font-bold text-slate-400">&larr; Back to website</a>
    </div>
</body>

</html>
