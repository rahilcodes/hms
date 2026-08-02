@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md mx-auto w-full space-y-8 text-center">

            {{-- CELEBRATION ANIMATION (CSS-only checkmark) --}}
            <div class="flex justify-center">
                <div class="rounded-full bg-green-100 p-6 animate-bounce-slow">
                    <svg class="w-16 h-16 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <div>
                <h2 class="mt-2 text-3xl font-extrabold text-gray-900">
                    You're all set!
                </h2>
                <p class="mt-2 text-lg text-gray-600">
                    Booking confirmed. You can pay at the hotel.
                </p>
            </div>

            {{-- ACTION CARDS --}}
            <div class="bg-white py-8 px-4 shadow-xl shadow-gray-200/50 rounded-2xl border border-gray-100 sm:px-10">
                <div class="space-y-6">

                    {{-- WHATSAPP SHARE --}}
                    <a href="https://wa.me/?text=I%20just%20booked%20a%20stay%20at%20{{ urlencode(site('hotel_name')) }}!"
                        target="_blank"
                        class="w-full flex items-center justify-center px-4 py-4 border border-transparent rounded-xl shadow-sm text-base font-medium text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transform transition hover:-translate-y-0.5">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                        </svg>
                        Share on WhatsApp
                    </a>

                    {{-- MAPS LINK --}}
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(site('hotel_address')) }}"
                        target="_blank"
                        class="w-full flex items-center justify-center px-4 py-4 border border-gray-200 rounded-xl shadow-sm text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-6 h-6 mr-3 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Navigate to Hotel
                    </a>

                    {{-- GUEST PORTAL BUTTON --}}
                    <a href="{{ route('guest.login') }}"
                        class="w-full flex items-center justify-center px-4 py-4 border-2 border-blue-600 rounded-xl shadow-lg shadow-blue-500/10 text-base font-black text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transform transition hover:scale-[1.02]">
                        <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Access My Stay Dashboard
                    </a>

                </div>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <p class="text-xs text-gray-400 uppercase tracking-widest mb-4">You can find your booking details in the
                        admin panel</p>
                    <a href="{{ route('home') }}" class="font-medium text-blue-600 hover:text-blue-500 transition">
                        &larr; Return directly to Home
                    </a>
                </div>
            </div>

        </div>
    </div>

    <style>
        @keyframes bounce-slow {

            0%,
            100% {
                transform: translateY(-5%);
            }

            50% {
                transform: translateY(0);
            }
        }

        .animate-bounce-slow {
            animation: bounce-slow 2s infinite;
        }
    </style>
@endsection