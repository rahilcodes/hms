@extends('layouts.app')

@section('content')

    {{-- 1. HERO SECTION --}}
    @if(site('home_hero_enabled', '1'))
        <section class="relative h-[85vh] flex items-center justify-center overflow-hidden bg-slate-900 group">
            {{-- BACKGROUND VIDEO/IMAGE --}}
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?q=80&w=3270&auto=format&fit=crop"
                    class="w-full h-full object-cover opacity-60 scale-105 group-hover:scale-100 transition-transform duration-[20s] ease-linear"
                    alt="Luxury Hotel">
                <div class="absolute inset-0 bg-gradient-to-b from-slate-900/50 via-transparent to-slate-900"></div>
            </div>

            {{-- CONTENT --}}
            <div
                class="relative z-10 w-full max-w-7xl mx-auto px-6 text-center flex flex-col items-center justify-center h-full pb-12">

                <div class="animate-fade-in-up">
                    <span
                        class="inline-block py-2 px-6 rounded-full bg-white/5 border border-white/10 text-white/90 text-[11px] font-bold uppercase tracking-[0.2em] mb-6 backdrop-blur-md shadow-2xl">
                        {{ site('home_hero_badge', 'The Pinnacle of Luxury') }}
                    </span>

                    {{-- WEATHER WIDGET --}}
                    <div id="weather-widget"
                        class="absolute top-8 right-6 hidden md:block animate-fade-in-up hover:scale-105 transition-transform duration-300 cursor-default z-50">
                    </div>
                    {{-- REFINED HEADING SIZE --}}
                    <h1
                        class="text-white text-5xl md:text-7xl lg:text-8xl font-bold mb-6 tracking-tighter drop-shadow-2xl font-display leading-[1.0]">
                        {!! nl2br(e(site('home_hero_heading', "Beyond\nExpectations."))) !!}
                    </h1>
                    <p
                        class="text-slate-200 text-lg md:text-xl font-light mb-12 max-w-2xl mx-auto drop-shadow-lg leading-relaxed mix-blend-screen opacity-90">
                        {{ site('home_hero_subheading', 'A sanctuary where timeless elegance meets modern mastery. Your extraordinary journey begins here.') }}
                    </p>
                </div>

                {{-- 2. SEARCH WIDGET (Premium Redesign) --}}
                @if(site('home_search_enabled', '1'))
                    <div id="hero-search-bar" class="w-full max-w-5xl mx-auto z-50 relative mt-8 md:mt-0">
                        <div class="bg-white/10 backdrop-blur-2xl border border-white/20 p-3 rounded-[2.5rem] shadow-[0_40px_100px_-20px_rgba(0,0,0,0.6)] transform transition duration-700 hover:shadow-blue-500/10 hover:scale-[1.01]">
                            <form action="{{ route('rooms') }}" method="GET" class="flex flex-col md:flex-row gap-3 bg-white rounded-[2rem] p-3">

                                {{-- INPUT FIELDS --}}
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-0 flex-1 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                                    {{-- CHECK IN --}}
                                    <div onclick="this.querySelector('input').showPicker()" class="px-8 py-5 hover:bg-slate-50 transition-colors group cursor-pointer relative flex flex-col justify-center">
                                        <div class="flex items-center gap-3 mb-1">
                                            <div class="p-1.5 bg-blue-50 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] group-hover:text-blue-600 transition-colors">
                                                {{ site('home_search_label_in', 'Check-in') }}
                                            </label>
                                        </div>
                                        <input type="date" id="check_in" name="check_in" value="{{ now()->toDateString() }}" min="{{ now()->toDateString() }}" required
                                            class="w-full bg-transparent border-none p-0 text-slate-900 font-bold text-lg focus:ring-0 cursor-pointer font-display ml-10">
                                    </div>

                                    {{-- CHECK OUT --}}
                                    <div onclick="this.querySelector('input').showPicker()" class="px-8 py-5 hover:bg-slate-50 transition-colors group cursor-pointer relative flex flex-col justify-center">
                                        <div class="flex items-center gap-3 mb-1">
                                            <div class="p-1.5 bg-blue-50 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] group-hover:text-blue-600 transition-colors">
                                                {{ site('home_search_label_out', 'Check-out') }}
                                            </label>
                                        </div>
                                        <input type="date" id="check_out" name="check_out" value="{{ now()->addDay()->toDateString() }}" required
                                            class="w-full bg-transparent border-none p-0 text-slate-900 font-bold text-lg focus:ring-0 cursor-pointer font-display ml-10">
                                    </div>

                                    {{-- GUESTS --}}
                                    <div onclick="this.querySelector('input').focus()" class="px-8 py-5 hover:bg-slate-50 transition-colors group cursor-pointer relative flex flex-col justify-center">
                                        <div class="flex items-center gap-3 mb-1">
                                            <div class="p-1.5 bg-blue-50 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </div>
                                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] group-hover:text-blue-600 transition-colors">
                                                {{ site('home_search_label_guests', 'Guests') }}
                                            </label>
                                        </div>
                                        <div class="flex items-center gap-3 ml-10">
                                            <input type="number" name="rooms" min="1" value="1" placeholder="1 Guest"
                                                class="w-full bg-transparent border-none p-0 text-slate-900 font-bold text-lg focus:ring-0 font-display">
                                        </div>
                                    </div>

                                </div>

                                {{-- SUBMIT --}}
                                <button type="submit"
                                    class="bg-slate-900 hover:bg-blue-600 text-white rounded-[1.8rem] px-12 font-extrabold text-xl shadow-2xl shadow-slate-900/30 transition-all duration-300 flex items-center justify-center gap-3 md:w-auto w-full py-5 md:py-0 font-display transform hover:-translate-y-1 active:translate-y-0 relative overflow-hidden group">
                                    <span class="relative z-10">{{ site('home_search_cta', 'Search') }}</span>
                                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                    </svg>
                                </button>

                            </form>
                        </div>
                    </div>
                @endif

            </div>
        </section>
    @endif

    {{-- 3. BRAND STORY (New Section) --}}
    @if(site('home_story_enabled', '0'))
        <section class="py-20 bg-white">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-4 block">{{ site('home_story_badge', 'About Us') }}</span>
                <h2 class="text-4xl font-bold text-slate-900 font-display mb-8">{{ site('home_story_title', 'Our Philosophy') }}
                </h2>
                <p class="text-slate-600 text-lg leading-loose font-light">
                    {{ site('home_story_text', 'We believe in crafting experiences, not just stays or holiday. Every detail is curated to provide you with the ultimate luxury and comfort.') }}
                </p>
                <div class="mt-10">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/Signature_sample.svg/1200px-Signature_sample.svg.png"
                        class="h-12 mx-auto opacity-50" alt="Signature">
                </div>
            </div>
        </section>
    @endif

    {{-- 4. FEATURED ROOMS --}}
    @if(site('home_rooms_enabled', '1') && count($featuredRooms) > 0)
        <section class="py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-6">

                {{-- SECTION HEADER --}}
                <div class="flex flex-col md:flex-row justify-between items-end mb-16 reveal">
                    <div class="max-w-2xl">
                        <span
                            class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">{{ site('home_rooms_badge', 'Accommodations') }}</span>
                        <h2 class="text-4xl md:text-5xl font-bold text-slate-900 tracking-tight font-display mb-4">
                            {{ site('home_rooms_title', 'Stay in Absolute Style.') }}
                        </h2>
                        <p class="text-slate-500 text-lg font-light leading-relaxed">
                            {{ site('home_rooms_desc', 'Choose from our exclusive collection of rooms and suites, each designed to be your personal sanctuary.') }}
                        </p>
                    </div>
                    <a href="{{ route('rooms') }}"
                        class="hidden md:flex items-center gap-2 text-sm font-bold text-slate-900 border-b-2 border-slate-200 pb-1 hover:border-blue-600 hover:text-blue-600 transition mt-6 md:mt-0 group">
                        {{ site('home_rooms_cta', 'View All Rooms') }}
                        <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($featuredRooms as $room)
                        {{-- CARD --}}
                        <div onclick="window.location='{{ route('rooms.show', $room->id) }}'"
                             class="titanium-card bg-white rounded-[2.5rem] overflow-hidden group flex flex-col reveal hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.15)] transition-all duration-700 cursor-pointer h-full border border-slate-100 relative">
                            
                            {{-- IMAGE WRAPPER --}}
                            <div class="relative h-[320px] overflow-hidden">
                                @if($room->image)
                                    <img src="{{ asset('storage/' . $room->image) }}"
                                         class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                                @else
                                    <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=2670&auto=format&fit=crop"
                                         class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                                @endif

                                {{-- LUXURY OVERLAY --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/20 to-transparent"></div>

                                {{-- PRICE BADGE --}}
                                <div class="absolute top-6 right-6 bg-white/10 backdrop-blur-xl px-4 py-2 rounded-2xl text-white border border-white/20 shadow-2xl flex flex-col items-center">
                                    <span class="text-[10px] font-black uppercase tracking-widest opacity-70">From</span>
                                    <span class="text-lg font-black tracking-tight">₹{{ number_format($room->base_price) }}</span>
                                </div>

                                {{-- CATEGORY BADGE --}}
                                <div class="absolute top-6 left-6">
                                    <span class="px-4 py-1.5 bg-blue-600 rounded-full text-[9px] font-black text-white uppercase tracking-widest shadow-lg shadow-blue-500/30">
                                        {{ $room->room_type_category ?? 'Signature Collection' }}
                                    </span>
                                </div>

                                <div class="absolute bottom-8 left-8 right-8 text-white">
                                    <h3 class="text-3xl font-black font-display tracking-tight leading-none group-hover:text-blue-400 transition-colors duration-500">
                                        {{ $room->name }}
                                    </h3>
                                </div>
                            </div>

                            {{-- CONTENT --}}
                            <div class="p-8 flex-1 flex flex-col">
                                <p class="text-slate-500 text-sm leading-relaxed mb-6 font-medium line-clamp-2">
                                    {{ $room->description }}
                                </p>

                                {{-- PILL AMENITIES --}}
                                <div class="flex flex-wrap gap-2 mb-8">
                                    <div class="flex items-center gap-2 px-3.5 py-2 bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 group-hover:bg-blue-50 group-hover:border-blue-100 group-hover:text-blue-600 transition-colors duration-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"></path></svg>
                                        <span class="text-[9px] font-black uppercase tracking-widest">WiFi</span>
                                    </div>
                                    <div class="flex items-center gap-2 px-3.5 py-2 bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 group-hover:bg-blue-50 group-hover:border-blue-100 group-hover:text-blue-600 transition-colors duration-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                                        <span class="text-[9px] font-black uppercase tracking-widest">Ocean View</span>
                                    </div>
                                    <div class="flex items-center gap-2 px-3.5 py-2 bg-slate-50 border border-slate-100 rounded-2xl text-slate-400 group-hover:bg-blue-50 group-hover:border-blue-100 group-hover:text-blue-600 transition-colors duration-500">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        <span class="text-[9px] font-black uppercase tracking-widest">A/C</span>
                                    </div>
                                </div>

                                <div class="mt-auto pt-6 border-t border-slate-50 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="relative flex h-2 w-2">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                        </div>
                                        <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Available</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-6">
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">Details</span>
                                        <div class="px-6 py-3 bg-blue-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-blue-500/20 group-hover:bg-slate-900 group-hover:shadow-slate-900/20 transition-all duration-500 group-hover:-translate-y-0.5">
                                            Book Now
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- HOVER GLOW --}}
                            <div class="absolute inset-0 border-2 border-transparent group-hover:border-blue-500/10 rounded-[2.5rem] pointer-events-none transition-all duration-500"></div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 text-center md:hidden">
                    <a href="{{ route('rooms') }}" class="btn-secondary inline-block w-full">View All Rooms</a>
                </div>
            </div>
        </section>
    @endif

    {{-- 5. EXCLUSIVE OFFERS --}}
    @if(site('home_offers_enabled', '0'))
        <section class="py-24 bg-blue-900 text-white overflow-hidden relative">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]"></div>
            </div>
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="text-center mb-16 reveal">
                    <span class="text-blue-300 font-bold tracking-widest uppercase text-xs mb-3 block">Limited Time</span>
                    <h2 class="text-4xl md:text-5xl font-bold font-display">
                        {{ site('home_offers_title', 'Exclusive Experiences') }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @for($i=1; $i<=2; $i++)
                    <div class="bg-white/5 backdrop-blur-lg border border-white/10 p-8 rounded-3xl hover:bg-white/10 transition duration-500 reveal reveal-delay-{{ ($i-1)*100 }}">
                        <h3 class="text-2xl font-bold mb-4">{{ site('home_offer_'.$i.'_title', $i==1?'Royal Romance':'Wellness sanctuary') }}</h3>
                        <p class="text-blue-100 mb-6 font-light">{{ site('home_offer_'.$i.'_desc', 'Experience the ultimate luxury with our curated packages.') }}</p>
                        <a href="#" class="inline-block py-3 px-8 bg-white text-blue-900 font-bold rounded-full hover:bg-blue-50 transition">Explore Offer</a>
                    </div>
                    @endfor
                </div>
            </div>
        </section>
    @endif

    {{-- 6. LIFESTYLE EXPERIENCE --}}
    @if(site('home_lifestyle_enabled', '1'))
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <div class="mb-16 reveal">
                    <span
                        class="text-blue-600 font-bold tracking-widest uppercase text-xs">{{ site('home_lifestyle_badge', 'Lifestyle') }}</span>
                    <h2 class="text-4xl md:text-5xl font-bold mt-2 text-slate-900 tracking-tight font-display">
                        {{ site('home_lifestyle_title', 'Unforgettable Moments') }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 h-[500px]">
                    {{-- CARD 1 --}}
                    <div class="md:col-span-2 relative rounded-[2rem] overflow-hidden group cursor-pointer reveal shadow-lg">
                        <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?q=80&w=1600&auto=format&fit=crop"
                            class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Experience">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-80"></div>
                        <div class="absolute bottom-10 left-10 p-4">
                            <h3 class="text-white text-3xl md:text-4xl font-bold font-display mb-2">{{ site('home_lifestyle_1_title', 'Signature Spa Rituals') }}</h3>
                            <p class="text-slate-300 max-w-sm text-sm">{{ site('home_lifestyle_1_desc', 'Ancient healing traditions meet modern luxury in our world-class wellness center.') }}</p>
                        </div>
                    </div>

                    {{-- STACKED CARDS (2 & 3) --}}
                    <div class="grid grid-rows-2 gap-6">
                        @for($i=2; $i<=3; $i++)
                        <div class="relative rounded-[2rem] overflow-hidden group cursor-pointer reveal reveal-delay-{{ ($i-1)*100 }} shadow-md">
                            <img src="{{ $i == 2 ? 'https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=800' : 'https://images.unsplash.com/photo-1513694203232-719a280e022f?q=80&w=800' }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" alt="Lifestyle">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-80"></div>
                            <div class="absolute bottom-6 left-6">
                                <h3 class="text-white text-xl font-bold font-display">{{ site('home_lifestyle_'.$i.'_title', $i==2?'Ocean Dining':'Yacht Charters') }}</h3>
                                @if(site('home_lifestyle_'.$i.'_desc'))
                                <p class="text-slate-300 text-[10px] mt-1 opacity-0 group-hover:opacity-100 transition duration-300">{{ site('home_lifestyle_'.$i.'_desc') }}</p>
                                @endif
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- 7. AMENITIES --}}
        @if(site('home_amenities_enabled', '1'))
            <section class="py-20 bg-slate-50 border-y border-slate-200/60">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center mb-12 reveal">
                         <h2 class="text-3xl font-bold text-slate-900 font-display">{{ site('home_amenities_title', 'World Class Services') }}</h2>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 text-center">
                        @php $amenIcons = ['M13 10V3L4 14h7v7l9-11h-7z', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 'M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z']; @endphp
                        @for($i=1; $i<=4; $i++)
                        <div class="group reveal reveal-delay-{{ ($i-1)*100 }} cursor-default">
                            <div class="w-14 h-14 mx-auto bg-white rounded-2xl shadow-sm flex items-center justify-center text-slate-900 mb-4 group-hover:scale-110 transition-transform duration-300 border border-slate-100">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $amenIcons[$i-1] }}"></path>
                                </svg>
                            </div>
                            <h3 class="font-bold text-slate-900 text-sm md:text-base">{{ site('home_amenity_'.$i.'_label', 'Service '.$i) }}</h3>
                        </div>
                        @endfor
                    </div>
                </div>
            </section>
        @endif

        {{-- 8. IMPACT STATS --}}
        @if(site('home_stats_enabled', '0'))
            <section class="py-24 bg-slate-900 text-white">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                        @for($i=1; $i<=4; $i++)
                        <div class="reveal reveal-delay-{{ ($i-1)*100 }}">
                            <div class="text-5xl font-bold font-display mb-2 text-blue-400">{{ site('home_stats_'.$i.'_num', $i==1?'10k+':($i==2?'15+':($i==3?'98%':'24h'))) }}</div>
                            <div class="text-slate-400 uppercase tracking-widest text-xs font-bold">{{ site('home_stats_'.$i.'_label', 'Stat '.$i) }}</div>
                        </div>
                        @endfor
                    </div>
                </div>
            </section>
        @endif

        {{-- 9. TESTIMONIALS --}}
        @if(site('home_reviews_enabled', '0'))
            <section class="py-24 bg-white">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center mb-16 reveal">
                        <span class="text-blue-600 font-bold tracking-widest uppercase text-xs mb-3 block">Guest Stories</span>
                        <h2 class="text-4xl font-bold text-slate-900 font-display">{{ site('home_reviews_title', 'What Our Guests Say') }}</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        @for($i = 1; $i <= 3; $i++)
                            <div class="bg-slate-50 p-8 rounded-3xl border border-slate-100 reveal reveal-delay-{{ $i * 100 }}">
                                <div class="flex text-amber-400 mb-4">
                                    @for($j = 0; $j < 5; $j++)
                                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                </div>
                                <p class="text-slate-600 italic mb-6 font-light">"{{ site('home_review_'.$i.'_text', 'An absolutely breathtaking experience. The attention to detail and personalized service made our stay truly unforgettable.') }}"</p>
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-slate-200 rounded-full"></div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ site('home_review_'.$i.'_name', 'Guest '.$i) }}</div>
                                        <div class="text-xs text-slate-400 uppercase tracking-widest font-bold">{{ site('home_review_'.$i.'_loc', 'New York, USA') }}</div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </section>
        @endif

        {{-- 10. IMMERSIVE GALLERY --}}
        @if(site('home_gallery_enabled', '0'))
            <section class="py-24 bg-slate-50">
                <div class="max-w-7xl mx-auto px-6 text-center mb-12">
                     <h2 class="text-4xl font-bold text-slate-900 font-display">{{ site('home_gallery_title', 'Through the Lens') }}</h2>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-4 h-[600px]">
                    <div class="h-full bg-slate-200 rounded-2xl overflow-hidden group reveal">
                        <img src="{{ site('home_gallery_img_1', 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?q=80&w=800') }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Gallery">
                    </div>
                    <div class="grid grid-rows-2 gap-4 h-full">
                        <div class="bg-slate-200 rounded-2xl overflow-hidden group reveal reveal-delay-100">
                            <img src="{{ site('home_gallery_img_2', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=800') }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Gallery">
                        </div>
                        <div class="bg-slate-200 rounded-2xl overflow-hidden group reveal reveal-delay-200">
                            <img src="{{ site('home_gallery_img_3', 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?q=80&w=800') }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Gallery">
                        </div>
                    </div>
                    <div class="h-full bg-slate-200 rounded-2xl overflow-hidden group reveal reveal-delay-300">
                        <img src="{{ site('home_gallery_img_4', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=800') }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Gallery">
                    </div>
                    <div class="grid grid-rows-2 gap-4 h-full">
                        <div class="bg-slate-200 rounded-2xl overflow-hidden group reveal reveal-delay-400">
                            <img src="{{ site('home_gallery_img_5', 'https://images.unsplash.com/photo-1445019980597-93fa8acb246c?q=80&w=800') }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700" alt="Gallery">
                        </div>
                        <div class="bg-slate-200 rounded-2xl overflow-hidden group reveal reveal-delay-500 text-slate-400 flex items-center justify-center font-bold text-lg bg-white border border-slate-100 uppercase tracking-widest text-[10px]">
                            {{ site('home_gallery_more_label', 'Explore More') }}
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- 11. FAQ ACCORDION --}}
        @if(site('home_faq_enabled', '0'))
            <section class="py-24 bg-white">
                <div class="max-w-3xl mx-auto px-6">
                    <h2 class="text-3xl font-bold mb-12 text-center font-display">{{ site('home_faq_title', 'Common Questions') }}</h2>
                    <div class="space-y-4" x-data="{ active: null }">
                        @for($i=1; $i<=3; $i++)
                        @if(site('home_faq_'.$i.'_q'))
                        <div class="border border-slate-100 rounded-2xl overflow-hidden reveal reveal-delay-{{ ($i-1)*100 }}">
                            <button @click="active = (active === {{ $i }} ? null : {{ $i }})" class="w-full p-6 text-left font-bold flex justify-between items-center group">
                                <span class="group-hover:text-blue-600 transition">{{ site('home_faq_'.$i.'_q') }}</span>
                                <svg class="w-5 h-5 transition transform" :class="active === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="active === {{ $i }}" x-collapse class="px-6 pb-6 text-slate-500 font-light">
                                {{ site('home_faq_'.$i.'_a') }}
                            </div>
                        </div>
                        @endif
                        @endfor
                    </div>
                </div>
            </section>
        @endif

    {{-- 12. MAP SECTION --}}
    @if(site('home_map_enabled', '1'))
    <section class="h-[500px] relative bg-slate-100">
        <div id="titanium-map" class="w-full h-full z-0"></div>
        <div
            class="absolute top-10 left-10 z-[400] bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-xl max-w-xs border border-white/50">
            <h3 class="text-xl font-bold text-slate-900 mb-2 font-display">{{ site('home_map_title', 'Our Location') }}</h3>
            <p class="text-slate-500 text-sm mb-4">
                {{ site('home_map_desc', 'Nestled in the heart of Kashmir, surrounded by breathtaking mountains and serene lakes.') }}
            </p>
            <a href="https://maps.google.com" target="_blank" class="text-blue-600 font-bold text-sm hover:underline font-bold">Get
                Directions &rarr;</a>
        </div>
    </section>
    @endif

        {{-- SCRIPT --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const checkIn = document.getElementById('check_in');
                const checkOut = document.getElementById('check_out');

                function updateCheckoutMin() {
                    if (!checkIn.value) return;
                    const d = new Date(checkIn.value);
                    d.setDate(d.getDate() + 1);
                    const min = d.toISOString().split('T')[0];
                    checkOut.min = min;
                    if (checkOut.value && checkOut.value < min) checkOut.value = min;
                }

                if (checkIn && checkOut) {
                    checkIn.addEventListener('change', updateCheckoutMin);
                }
            });
        </script>
@endsection