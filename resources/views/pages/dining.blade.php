@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white pt-24 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-16">
                <h1 class="text-5xl md:text-7xl font-bold font-heading mb-6 tracking-tight">Culinary Excellence</h1>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto font-light leading-relaxed">
                    A symphony of flavors sourced from local artisans and global inspirations.
                </p>
            </div>

            <!-- Restaurant Feature -->
            <div class="grid md:grid-cols-2 gap-16 items-center mb-32">
                <div>
                    <h2 class="text-4xl font-bold mb-6 text-yellow-500">The Obsidian Grill</h2>
                    <p class="text-gray-300 font-light mb-6">
                        Our signature steakhouse offering prime cuts and an extensive wine list. The ambiance is dark,
                        intimate, and perfect for romantic evenings or business dinners.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 text-sm text-gray-400">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Open: 6:00 PM - 11:00 PM</span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-400">
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span>Dress Code: Smart Casual</span>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="absolute inset-0 bg-yellow-500 blur-[100px] opacity-10 rounded-full"></div>
                    <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                        alt="Restaurant"
                        class="relative rounded-2xl shadow-2xl border border-gray-800 hover:scale-105 transition duration-700">
                </div>
            </div>

            <!-- Breakfast Section -->
            <div class="grid md:grid-cols-2 gap-16 items-center flex-row-reverse mb-20">
                <div class="relative md:order-2">
                    <div class="absolute inset-0 bg-blue-500 blur-[100px] opacity-10 rounded-full"></div>
                    <img src="https://images.unsplash.com/photo-1533038590840-1cde6e668a91?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                        alt="Breakfast"
                        class="relative rounded-2xl shadow-2xl border border-gray-800 hover:scale-105 transition duration-700">
                </div>
                <div class="md:order-1">
                    <h2 class="text-4xl font-bold mb-6 text-blue-400">Sunrise Terrace</h2>
                    <p class="text-gray-300 font-light mb-6">
                        Start your day with our gourmet breakfast buffet featuring fresh pastries, tropical fruits, and
                        made-to-order omelets, all while overlooking the city skyline.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-center gap-4 text-sm text-gray-400">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Open: 7:00 AM - 10:30 AM</span>
                        </div>
                        <div class="flex items-center gap-4 text-sm text-gray-400">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                            <span>Inclusive for Guests</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection