<x-mail::message>
    # Welcome to {{ config('app.name') }}!

    Hello {{ $booking->guest_name }},

    It's a pleasure to have you with us. We hope you have a wonderful and relaxing stay.

    <x-mail::panel>
        **Quick Guide**
        - **Wi-Fi Network:** {{ config('app.name') }}_Guest
        - **Wi-Fi Password:** Welcome2026
        - **Breakfast:** 7:30 AM - 10:30 AM at The Glass House
        - **Front Desk:** Available 24/7 (Press 0 from room phone)
    </x-mail::panel>

    If there is anything we can do to make your stay more comfortable, please do not hesitate to ask.

    Enjoy your stay!

    Warm regards,<br>
    **{{ config('app.name') }}**
</x-mail::message>