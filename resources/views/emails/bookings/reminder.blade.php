<x-mail::message>
    # See You Tomorrow!

    Hello {{ $booking->guest_name }},

    We are looking forward to welcoming you at **{{ config('app.name') }}** tomorrow!

    <x-mail::panel>
        **Arrival Details**
        - **Reservation:** #{{ $booking->id + 1000 }}
        - **Check-in:** {{ $booking->check_in->format('D, d M Y') }} (12:00 PM onwards)
        - **Room Type:** {{ $booking->roomType->name ?? 'Suite' }}
    </x-mail::panel>

    @if($booking->balance_amount > 0)
        Just a reminder that the balance of **₹{{ number_format($booking->balance_amount) }}** is due upon arrival.
    @endif

    <x-mail::button :url="url('/')">
        Get Directions
    </x-mail::button>

    Safe travels! If you need airport pickup or special assistance, please let us know.

    Warm regards,<br>
    **{{ config('app.name') }}**
</x-mail::message>