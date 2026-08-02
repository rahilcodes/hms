@extends('layouts.app')

@section('content')
    <div class="bg-gray-900 min-h-screen text-white pt-24 pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="text-center mb-16">
                <h1 class="text-5xl md:text-7xl font-bold font-heading mb-6 tracking-tight">Gallery</h1>
                <p class="text-lg text-gray-400 max-w-2xl mx-auto font-light leading-relaxed">
                    A visual journey through our property, rooms, and lifestyle.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[300px]">
                <!-- Hero Landscape -->
                <div class="md:col-span-2 group relative overflow-hidden rounded-2xl">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition duration-500 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Exterior">
                    <div
                        class="absolute bottom-0 left-0 p-6 z-20 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition duration-500">
                        <p class="text-white font-bold text-lg">The Facade at Dusk</p>
                    </div>
                </div>

                <!-- Vertical Shot -->
                <div class="md:row-span-2 group relative overflow-hidden rounded-2xl">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition duration-500 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                        alt="Interior Detail">
                    <div
                        class="absolute bottom-0 left-0 p-6 z-20 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition duration-500">
                        <p class="text-white font-bold text-lg">Lobby Details</p>
                    </div>
                </div>

                <!-- Standard Shot -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition duration-500 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?ixlib=rb-4.0.3&auto=format&fit=crop&w=1474&q=80"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Dining">
                    <div
                        class="absolute bottom-0 left-0 p-6 z-20 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition duration-500">
                        <p class="text-white font-bold text-lg">Private Dining</p>
                    </div>
                </div>

                <!-- Standard Shot -->
                <div class="group relative overflow-hidden rounded-2xl">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition duration-500 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1578683010236-d716f9a3f461?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Suite">
                    <div
                        class="absolute bottom-0 left-0 p-6 z-20 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition duration-500">
                        <p class="text-white font-bold text-lg">Presidential Suite</p>
                    </div>
                </div>

                <!-- Wide Shot -->
                <div class="md:col-span-2 group relative overflow-hidden rounded-2xl">
                    <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition duration-500 z-10"></div>
                    <img src="https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1498&q=80"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Pool">
                    <div
                        class="absolute bottom-0 left-0 p-6 z-20 translate-y-4 group-hover:translate-y-0 opacity-0 group-hover:opacity-100 transition duration-500">
                        <p class="text-white font-bold text-lg">Infinity Pool</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection