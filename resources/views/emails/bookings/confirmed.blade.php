<x-mail::message>
    # Booking Confirmed!

    Hello {{ $booking->guest_name }},

    Your reservation at **{{ config('app.name') }}** has been successfully confirmed. We are excited to host you!

    <x-mail::panel>
        **Reservation Summary**
        - **Reference:** #{{ $booking->id + 1000 }}
        - **Check-in:** {{ $booking->check_in->format('D, d M Y') }}
        - **Check-out:** {{ $booking->check_out->format('D, d M Y') }}
        - **Room Type:** {{ $booking->roomType->name ?? 'Special Assignment' }}
    </x-mail::panel>

    <x-mail::table>
        | Description | Amount |
        | :--- | :--- |
        | Total Amount | ₹{{ number_format($booking->total_amount) }} |
        | **Paid (Advance)** | **₹{{ number_format($booking->paid_amount) }}** |
        | **Balance Due** | **₹{{ number_format($booking->balance_amount) }}** |
    </x-mail::table>

    @if($booking->balance_amount > 0)
        The remaining balance of **₹{{ number_format($booking->balance_amount) }}** is payable at the front desk upon
        arrival.
    @endif

    <x-mail::button :url="url('/')">
        View My Booking
    </x-mail::button>

    If you have any questions, please reply to this email or call us at
    {{ config('mail.from.phone', 'our front desk') }}.

    Warm regards,<br>
    **{{ config('app.name') }}**
</x-mail::message>