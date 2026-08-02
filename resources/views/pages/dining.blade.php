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

            <div class="space-y-32">
                @foreach($categories as $index => $category)
                    <div class="grid md:grid-cols-2 gap-16 items-center {{ $index % 2 === 1 ? 'flex-row-reverse' : '' }} mb-20 section-fade-in">
                        <div class="{{ $index % 2 === 1 ? 'md:order-2' : '' }} relative group">
                            <div class="absolute inset-0 {{ $index % 2 === 0 ? 'bg-yellow-500' : 'bg-blue-500' }} blur-[100px] opacity-10 rounded-full group-hover:opacity-20 transition duration-700"></div>
                            
                            {{-- Category Image (Using first item's image if available, or fallback) --}}
                            @php
                                $featuredItem = $category->items->whereNotNull('image')->first();
                                $fallbackImage = $index % 2 === 0 
                                    ? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80'
                                    : 'https://images.unsplash.com/photo-1533038590840-1cde6e668a91?ixlib=rb-4.0.3&auto=format&fit=crop&w=1470&q=80';
                                $imageSrc = $featuredItem && $featuredItem->image ? asset('storage/' . $featuredItem->image) : $fallbackImage;
                            @endphp

                            <img src="{{ $imageSrc }}"
                                alt="{{ $category->name }}"
                                class="relative rounded-2xl shadow-2xl border border-gray-800 hover:scale-105 transition duration-700 w-full object-cover h-[400px]">
                        </div>
                        
                        <div class="{{ $index % 2 === 1 ? 'md:order-1' : '' }}">
                            <h2 class="text-4xl font-bold mb-6 {{ $index % 2 === 0 ? 'text-yellow-500' : 'text-blue-400' }}">{{ $category->name }}</h2>
                            <p class="text-gray-300 font-light mb-8">
                                {{ $category->description ?? 'Explore our selection of ' . strtolower($category->name) . ', crafted with the finest ingredients.' }}
                            </p>
                            
                            <div class="space-y-6">
                                @foreach($category->items as $item)
                                    <div class="flex justify-between items-start group">
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-100 group-hover:text-white transition">{{ $item->name }}</h3>
                                            <p class="text-sm text-gray-500 font-light max-w-sm">{{ $item->description }}</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-lg font-display text-gray-200">₹{{ number_format($item->price) }}</span>
                                        </div>
                                    </div>
                                    @if(!$loop->last) <div class="border-b border-gray-800/50"></div> @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
@endsection