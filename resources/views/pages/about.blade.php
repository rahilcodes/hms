@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white pt-24 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Hero Section -->
            <div class="text-center mb-20">
                <h1 class="text-5xl md:text-7xl font-bold font-heading mb-6 tracking-tight">
                    Our Story
                </h1>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto font-light leading-relaxed">
                    Founded in 1998, we embarked on a journey to redefine luxury hospitality. Not just a place to sleep, but
                    a sanctuary for the modern traveler.
                </p>
            </div>

            <!-- Content Grid -->
            <div class="grid md:grid-cols-2 gap-16 items-center mb-32">
                <div class="relative">
                    <div class="absolute inset-0 bg-blue-500 blur-[100px] opacity-20 rounded-full"></div>
                    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                        alt="Hotel Exterior"
                        class="relative rounded-2xl shadow-2xl border border-gray-800 rotate-2 hover:rotate-0 transition duration-700 ease-out">
                </div>
                <div>
                    <h2 class="text-3xl font-bold mb-6 text-blue-400">The Philosophy</h2>
                    <div class="space-y-6 text-gray-300 font-light">
                        <p>
                            We believe that true luxury lies in the details. From the thread count of our linens to the
                            curvature of our furniture, every element is curated to evoke a sense of calm and
                            sophistication.
                        </p>
                        <p>
                            Our architecture seamlessly blends with the natural landscape, creating an environment where the
                            outside world melts away, leaving only peace and tranquility.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Values Section -->
            <div class="grid md:grid-cols-3 gap-8 text-center">
                <div class="p-8 rounded-2xl bg-gray-800/50 border border-gray-700 hover:border-gray-600 transition group">
                    <div
                        class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Timelessness</h3>
                    <p class="text-gray-400 text-sm">Design that transcends trends and eras.</p>
                </div>
                <div class="p-8 rounded-2xl bg-gray-800/50 border border-gray-700 hover:border-gray-600 transition group">
                    <div
                        class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Passion</h3>
                    <p class="text-gray-400 text-sm">Service delivered with genuine care.</p>
                </div>
                <div class="p-8 rounded-2xl bg-gray-800/50 border border-gray-700 hover:border-gray-600 transition group">
                    <div
                        class="w-12 h-12 bg-gray-700 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Innovation</h3>
                    <p class="text-gray-400 text-sm">Always pushing the boundaries of comfort.</p>
                </div>
            </div>

        </div>
    </div>
@endsection