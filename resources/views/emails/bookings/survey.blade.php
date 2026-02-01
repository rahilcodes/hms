<x-mail::message>
    # Thank You for Staying With Us!

    Hello {{ $booking->guest_name }},

    We hope you enjoyed your recent stay at **{{ config('app.name') }}**. It was a pleasure hosting you!

    We are always striving to improve our guest experience. Could you take a moment to share your feedback?

    <x-mail::button :url="url('/')">
        Share Your Experience
    </x-mail::button>

    Your insights help us ensure every stay is exceptional. We look forward to welcoming you back soon!

    Warm regards,<br>
    **{{ config('app.name') }}**
</x-mail::message>