@extends('layouts.admin')

@section('content')

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Site Settings</h1>
        {{-- Save Button (Global) --}}
        <button form="settings-form"
            class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-sm hover:bg-blue-700 transition">
            Save Changes
        </button>
    </div>

    <div x-data="{ activeTab: 'general' }" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

        {{-- TABS HEADER --}}
        <div class="border-b border-gray-200 bg-gray-50 flex overflow-x-auto">
            <button @click="activeTab = 'general'"
                :class="activeTab === 'general' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap focus:outline-none transition">
                General
            </button>
            <button @click="activeTab = 'branding'"
                :class="activeTab === 'branding' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap focus:outline-none transition">
                Branding & Assets
            </button>
            <button @click="activeTab = 'hero'"
                :class="activeTab === 'hero' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap focus:outline-none transition">
                Homepage Content
            </button>
            <button @click="activeTab = 'contact'"
                :class="activeTab === 'contact' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap focus:outline-none transition">
                Contact Info
            </button>
            <button @click="activeTab = 'social'"
                :class="activeTab === 'social' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap focus:outline-none transition">
                Social Media
            </button>
            <button @click="activeTab = 'theme'"
                :class="activeTab === 'theme' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap focus:outline-none transition">
                Theme & Colors
            </button>
            <button @click="activeTab = 'seo'"
                :class="activeTab === 'seo' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap focus:outline-none transition">
                SEO & Scripts
            </button>
            <button @click="activeTab = 'payments'"
                :class="activeTab === 'payments' ? 'border-blue-600 text-blue-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-100'"
                class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap focus:outline-none transition">
                Payments & Deposits
            </button>
        </div>

        {{-- FORM START --}}
        <form id="settings-form" action="{{ route('admin.site-settings.update') }}" method="POST"
            enctype="multipart/form-data" class="p-6 md:p-8">
            @csrf

            {{-- 1. GENERAL TAB --}}
            <div x-show="activeTab === 'general'" class="space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Hotel Identity</h3>
                    <p class="text-sm text-gray-500 mb-4">Basic information about your property.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Hotel Name</label>
                        <input type="text" name="settings[hotel_name]" value="{{ $settings['hotel_name'] ?? '' }}"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">System Status</label>
                        <label class="flex items-center space-x-3 p-2.5 bg-gray-50 border border-gray-300 rounded">
                            <input type="checkbox" name="settings[maintenance_mode]" value="1" {{ ($settings['maintenance_mode'] ?? 0) ? 'checked' : '' }}
                                class="h-5 w-5 text-red-600 rounded focus:ring-red-500">
                            <span class="text-sm font-medium text-gray-700">Enable Maintenance Mode</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Default Check-in Time</label>
                        <input type="text" name="settings[checkin_time]"
                            value="{{ $settings['checkin_time'] ?? '12:00 PM' }}"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Default Check-out Time</label>
                        <input type="text" name="settings[checkout_time]"
                            value="{{ $settings['checkout_time'] ?? '11:00 AM' }}"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- 2. BRANDING TAB --}}
            <div x-show="activeTab === 'branding'" class="space-y-6" x-cloak>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Logos & Assets</h3>
                    <p class="text-sm text-gray-500 mb-4">Upload your hotel's branding.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Site Logo</label>
                    <div class="flex items-center gap-4">
                        @if(isset($settings['site_logo']))
                            <div class="bg-gray-100 p-2 rounded border">
                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" class="h-12 w-auto">
                            </div>
                        @endif
                        <input type="file" name="site_logo" class="block w-full text-sm text-gray-500
                                                                  file:mr-4 file:py-2 file:px-4
                                                                  file:rounded-full file:border-0
                                                                  file:text-sm file:font-semibold
                                                                  file:bg-blue-50 file:text-blue-700
                                                                  hover:file:bg-blue-100
                                                                " />
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hero Image (Homepage)</label>
                    <p class="text-xs text-gray-500 mb-2">Recommended size: 1920x1080px</p>
                    <div class="flex items-center gap-4">
                        @if(isset($settings['hero_image']))
                            <div class="bg-gray-100 p-2 rounded border">
                                <img src="{{ asset('storage/' . $settings['hero_image']) }}" class="h-16 w-auto">
                            </div>
                        @endif
                        <input type="file" name="hero_image" class="block w-full text-sm text-gray-500
                                                                  file:mr-4 file:py-2 file:px-4
                                                                  file:rounded-full file:border-0
                                                                  file:text-sm file:font-semibold
                                                                  file:bg-blue-50 file:text-blue-700
                                                                  hover:file:bg-blue-100
                                                                " />
                    </div>
                </div>
            </div>

            {{-- 3. HOMEPAGE TAB --}}
            <div x-show="activeTab === 'hero'" class="space-y-6" x-cloak>
                <div class="flex justify-between items-end">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Homepage Builder</h3>
                        <p class="text-sm text-gray-500">Configure the 12 key sections of your landing page.</p>
                    </div>
                </div>

                {{-- SECTION 1: HERO --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: true }">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">01</span>
                            <h4 class="font-bold text-gray-800">Hero Section</h4>
                        </div>
                        <div class="flex items-center gap-3">
                             {{-- TOGGLE --}}
                            <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                                <input type="checkbox" name="settings[home_hero_enabled]" value="1" 
                                    {{ ($settings['home_hero_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                            </label>
                            <svg class="w-5 h-5 text-gray-400 transform transition" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    <div x-show="open" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Badge</label>
                            <input type="text" name="settings[home_hero_badge]" value="{{ $settings['home_hero_badge'] ?? 'The Pinnacle of Luxury' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Heading</label>
                            <input type="text" name="settings[home_hero_heading]" value="{{ $settings['home_hero_heading'] ?? '' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded font-bold">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Subheading</label>
                            <textarea name="settings[home_hero_subheading]" rows="2" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">{{ $settings['home_hero_subheading'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: SEARCH --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">02</span>
                            <h4 class="font-bold text-gray-800">Booking Widget</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                            <input type="checkbox" name="settings[home_search_enabled]" value="1" {{ ($settings['home_search_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div x-show="open" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Check-in Label</label>
                             <input type="text" name="settings[home_search_label_in]" value="{{ $settings['home_search_label_in'] ?? 'Check-in' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Check-out Label</label>
                             <input type="text" name="settings[home_search_label_out]" value="{{ $settings['home_search_label_out'] ?? 'Check-out' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Guests Label</label>
                             <input type="text" name="settings[home_search_label_guests]" value="{{ $settings['home_search_label_guests'] ?? 'Guests' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Search Button Text</label>
                             <input type="text" name="settings[home_search_cta]" value="{{ $settings['home_search_cta'] ?? 'Search' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>

                {{-- SECTION 3: BRAND STORY [NEW] --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">03</span>
                            <h4 class="font-bold text-gray-800">Brand Story (About)</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                            <input type="checkbox" name="settings[home_story_enabled]" value="1" {{ ($settings['home_story_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div x-show="open" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Badge</label>
                             <input type="text" name="settings[home_story_badge]" value="{{ $settings['home_story_badge'] ?? 'About Us' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                             <input type="text" name="settings[home_story_title]" value="{{ $settings['home_story_title'] ?? 'Our Philosophy' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                        <div class="md:col-span-2">
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Story Text</label>
                             <textarea name="settings[home_story_text]" rows="3" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">{{ $settings['home_story_text'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- SECTION 4: FEATURED ROOMS --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                             <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">04</span>
                             <h4 class="font-bold text-gray-800">Featured Rooms</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                            <input type="checkbox" name="settings[home_rooms_enabled]" value="1" {{ ($settings['home_rooms_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div x-show="open" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Badge</label>
                             <input type="text" name="settings[home_rooms_badge]" value="{{ $settings['home_rooms_badge'] ?? 'Accommodations' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                        <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                             <input type="text" name="settings[home_rooms_title]" value="{{ $settings['home_rooms_title'] ?? 'Stay in Style' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                        </div>
                    </div>
                </div>

                {{-- SECTION 5: OFFERS [NEW] --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                             <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">05</span>
                             <h4 class="font-bold text-gray-800">Exclusive Offers</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                            <input type="checkbox" name="settings[home_offers_enabled]" value="1" {{ ($settings['home_offers_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div x-show="open" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div class="md:col-span-2">
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                             <input type="text" name="settings[home_offers_title]" value="{{ $settings['home_offers_title'] ?? 'Exclusive Experiences' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded font-bold">
                         </div>
                         <div class="p-4 bg-gray-100/50 rounded-lg">
                             <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Offer 1</label>
                             <input type="text" name="settings[home_offer_1_title]" value="{{ $settings['home_offer_1_title'] ?? 'Royal Romance Package' }}" class="w-full p-2 bg-white border border-gray-300 rounded mb-2 font-bold" placeholder="Title">
                             <textarea name="settings[home_offer_1_desc]" rows="2" class="w-full p-2 bg-white border border-gray-300 rounded text-sm" placeholder="Description">{{ $settings['home_offer_1_desc'] ?? 'Includes a private candlelit dinner, couple\'s spa, and late checkout.' }}</textarea>
                         </div>
                         <div class="p-4 bg-gray-100/50 rounded-lg">
                             <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Offer 2</label>
                             <input type="text" name="settings[home_offer_2_title]" value="{{ $settings['home_offer_2_title'] ?? 'Wellness Sanctuary' }}" class="w-full p-2 bg-white border border-gray-300 rounded mb-2 font-bold" placeholder="Title">
                             <textarea name="settings[home_offer_2_desc]" rows="2" class="w-full p-2 bg-white border border-gray-300 rounded text-sm" placeholder="Description">{{ $settings['home_offer_2_desc'] ?? '3 nights stay with unlimited access to spa and personalized detox menu.' }}</textarea>
                         </div>
                    </div>
                </div>

                {{-- SECTION 6: LIFESTYLE --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                     <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                             <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">06</span>
                             <h4 class="font-bold text-gray-800">Lifestyle & Video</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                            <input type="checkbox" name="settings[home_lifestyle_enabled]" value="1" {{ ($settings['home_lifestyle_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div x-show="open" class="p-6 space-y-4">
                         <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Section Title</label>
                             <input type="text" name="settings[home_lifestyle_title]" value="{{ $settings['home_lifestyle_title'] ?? 'Unforgettable Moments' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded font-bold">
                         </div>
                         <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                             @for($i=1; $i<=3; $i++)
                             <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                 <label class="block text-[10px] font-bold uppercase text-gray-400 mb-2">Card {{ $i }}</label>
                                 <input type="text" name="settings[home_lifestyle_{{ $i }}_title]" value="{{ $settings['home_lifestyle_'.$i.'_title'] ?? ($i==1?'Signature Spa':($i==2?'Ocean Dining':'Yacht Charters')) }}" class="w-full p-2 bg-white border border-gray-300 rounded mb-2 font-semibold text-sm" placeholder="Title">
                                 <textarea name="settings[home_lifestyle_{{ $i }}_desc]" rows="2" class="w-full p-2 bg-white border border-gray-300 rounded text-xs" placeholder="Description">{{ $settings['home_lifestyle_'.$i.'_desc'] ?? '' }}</textarea>
                             </div>
                             @endfor
                         </div>
                    </div>
                </div>

                 {{-- SECTION 7: AMENITIES --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                     <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                             <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">07</span>
                             <h4 class="font-bold text-gray-800">Amenities Grid</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                            <input type="checkbox" name="settings[home_amenities_enabled]" value="1" {{ ($settings['home_amenities_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div x-show="open" class="p-6 space-y-4">
                         <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Section Title</label>
                             <input type="text" name="settings[home_amenities_title]" value="{{ $settings['home_amenities_title'] ?? 'World Class Services' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded font-bold">
                         </div>
                         <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                             @php $amenDefaults = ['High-Speed WiFi', 'Wellness Center', 'Michelin Dining', '24/7 Concierge']; @endphp
                             @foreach($amenDefaults as $idx => $def)
                             <div>
                                 <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1">Amenity {{ $idx+1 }}</label>
                                 <input type="text" name="settings[home_amenity_{{ $idx+1 }}_label]" value="{{ $settings['home_amenity_'.($idx+1).'_label'] ?? $def }}" class="w-full p-2 bg-gray-50 border border-gray-300 rounded text-sm">
                             </div>
                             @endforeach
                         </div>
                    </div>
                </div>

                {{-- SECTION 8: STATS [NEW] --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                     <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                             <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">08</span>
                             <h4 class="font-bold text-gray-800">Impact Stats</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                            <input type="checkbox" name="settings[home_stats_enabled]" value="1" {{ ($settings['home_stats_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    <div x-show="open" class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                         @for($i=1; $i<=4; $i++)
                         <div class="space-y-2">
                             <label class="block text-[10px] font-bold text-gray-400 mb-1 uppercase">Stat {{ $i }}</label>
                             <input type="text" name="settings[home_stats_{{ $i }}_num]" value="{{ $settings['home_stats_'.$i.'_num'] ?? ($i==1?'10k+':($i==2?'15+':($i==3?'98%':'24h'))) }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-sm font-bold" placeholder="Num">
                             <input type="text" name="settings[home_stats_{{ $i }}_label]" value="{{ $settings['home_stats_'.$i.'_label'] ?? ($i==1?'Guests':($i==2?'Awards':($i==3?'Stars':'Service'))) }}" class="w-full p-2 bg-gray-50 border border-gray-200 rounded text-[10px]" placeholder="Label">
                         </div>
                         @endfor
                    </div>
                </div>

                {{-- SECTION 9: TESTIMONIALS [NEW] --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                     <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                             <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">09</span>
                             <h4 class="font-bold text-gray-800">Testimonials</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                             <input type="checkbox" name="settings[home_reviews_enabled]" value="1" {{ ($settings['home_reviews_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                     <div x-show="open" class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Section Title</label>
                            <input type="text" name="settings[home_reviews_title]" value="{{ $settings['home_reviews_title'] ?? 'What Our Guests Say' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded font-bold">
                        </div>
                        <div class="space-y-4">
                             @for($i=1; $i<=3; $i++)
                             <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                 <div class="grid grid-cols-2 gap-4 mb-2">
                                     <input type="text" name="settings[home_review_{{ $i }}_name]" value="{{ $settings['home_review_'.$i.'_name'] ?? 'Guest '.$i }}" class="p-2 border border-gray-300 rounded text-sm font-bold" placeholder="Name">
                                     <input type="text" name="settings[home_review_{{ $i }}_loc]" value="{{ $settings['home_review_'.$i.'_loc'] ?? 'London, UK' }}" class="p-2 border border-gray-300 rounded text-xs" placeholder="Location">
                                 </div>
                                 <textarea name="settings[home_review_{{ $i }}_text]" rows="2" class="w-full p-2 border border-gray-300 rounded text-xs italic" placeholder="Review Text">{{ $settings['home_review_'.$i.'_text'] ?? '' }}</textarea>
                             </div>
                             @endfor
                        </div>
                    </div>
                </div>

               {{-- SECTION 10: GALLERY [NEW] --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                     <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                             <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">10</span>
                             <h4 class="font-bold text-gray-800">Gallery</h4>
                        </div>
                         <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                             <input type="checkbox" name="settings[home_gallery_enabled]" value="1" {{ ($settings['home_gallery_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                     <div x-show="open" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Section Title</label>
                            <input type="text" name="settings[home_gallery_title]" value="{{ $settings['home_gallery_title'] ?? 'Through the Lens' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded font-bold">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                             @for($i=1; $i<=5; $i++)
                             <div>
                                 <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Image {{ $i }} URL</label>
                                 <input type="text" name="settings[home_gallery_img_{{ $i }}]" value="{{ $settings['home_gallery_img_'.$i] ?? '' }}" class="w-full p-2 bg-gray-50 border border-gray-300 rounded text-[10px]" placeholder="https://...">
                             </div>
                             @endfor
                        </div>
                    </div>
                </div>

                 {{-- SECTION 11: FAQ [NEW] --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                     <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                             <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">11</span>
                             <h4 class="font-bold text-gray-800">FAQ</h4>
                        </div>
                         <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                             <input type="checkbox" name="settings[home_faq_enabled]" value="1" {{ ($settings['home_faq_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                     <div x-show="open" class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Section Title</label>
                            <input type="text" name="settings[home_faq_title]" value="{{ $settings['home_faq_title'] ?? 'Common Questions' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded font-bold">
                        </div>
                        <div class="space-y-4">
                             @for($i=1; $i<=3; $i++)
                             <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                                 <input type="text" name="settings[home_faq_{{ $i }}_q]" value="{{ $settings['home_faq_'.$i.'_q'] ?? '' }}" class="w-full p-2 border border-gray-300 rounded text-sm font-bold mb-2" placeholder="Question {{ $i }}">
                                 <textarea name="settings[home_faq_{{ $i }}_a]" rows="2" class="w-full p-2 border border-gray-300 rounded text-sm" placeholder="Answer {{ $i }}">{{ $settings['home_faq_'.$i.'_a'] ?? '' }}</textarea>
                             </div>
                             @endfor
                        </div>
                    </div>
                </div>

                {{-- SECTION 12: MAP --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden" x-data="{ open: false }">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center cursor-pointer" @click="open = !open">
                        <div class="flex items-center gap-3">
                            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded">12</span>
                            <h4 class="font-bold text-gray-800">Map</h4>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer" @click.stop>
                            <input type="checkbox" name="settings[home_map_enabled]" value="1" {{ ($settings['home_map_enabled'] ?? '1') === '1' ? 'checked' : '' }} class="sr-only peer">
                             <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                     <div x-show="open" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                         <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Title</label>
                             <input type="text" name="settings[home_map_title]" value="{{ $settings['home_map_title'] ?? 'Our Location' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                         </div>
                         <div>
                             <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                             <input type="text" name="settings[home_map_desc]" value="{{ $settings['home_map_desc'] ?? 'Nestled in Kashmir...' }}" class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded">
                         </div>
                    </div>
                </div>

            </div>

            {{-- 4. CONTACT TAB --}}
            <div x-show="activeTab === 'contact'" class="space-y-6" x-cloak>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Contact Details</h3>
                    <p class="text-sm text-gray-500 mb-4">This information appears in the footer and header.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hotel Phone</label>
                    <input type="text" name="settings[hotel_phone]" value="{{ $settings['hotel_phone'] ?? '' }}"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Hotel Email</label>
                    <input type="email" name="settings[hotel_email]" value="{{ $settings['hotel_email'] ?? '' }}"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                    <textarea name="settings[hotel_address]" rows="3"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">{{ $settings['hotel_address'] ?? '' }}</textarea>
                </div>

                <div>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="settings[show_whatsapp_cta]" value="1" {{ ($settings['show_whatsapp_cta'] ?? false) ? 'checked' : '' }}
                            class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">Show WhatsApp CTA in Header</span>
                    </label>
                </div>
            </div>

            {{-- 5. SOCIAL MEDIA TAB --}}
            <div x-show="activeTab === 'social'" class="space-y-6" x-cloak>
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">Social Media</h3>
                    <p class="text-sm text-gray-500 mb-4">Connect with your guests on social platforms.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Facebook URL</label>
                    <input type="url" name="settings[facebook_url]" value="{{ $settings['facebook_url'] ?? '' }}"
                        placeholder="https://facebook.com/your-hotel"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Instagram URL</label>
                    <input type="url" name="settings[instagram_url]" value="{{ $settings['instagram_url'] ?? '' }}"
                        placeholder="https://instagram.com/your-hotel"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            {{-- 6. THEME TAB --}}
            <div x-show="activeTab === 'theme'" x-cloak x-data="{ 
                                theme: {
                                    primary: '{{ $settings['primary_color'] ?? '#2563eb' }}',
                                    secondary: '{{ $settings['secondary_color'] ?? '#16a34a' }}',
                                    accent: '{{ $settings['accent_color'] ?? '#f59e0b' }}',
                                    btn_bg: '{{ $settings['button_primary_color'] ?? '#2563eb' }}',
                                    header_txt: '{{ $settings['header_text_color'] ?? '#111827' }}',
                                    footer_txt: '{{ $settings['footer_text_color'] ?? '#4b5563' }}',
                                    heading_font: '{{ $settings['heading_font_family'] ?? 'Inter' }}',
                                    body_font: '{{ $settings['body_font_family'] ?? 'Inter' }}',
                                    heading_weight: '{{ $settings['heading_font_weight'] ?? '700' }}',
                                    body_size: '{{ $settings['body_font_size'] ?? '16' }}',
                                    radius: '{{ $settings['border_radius'] ?? '0.5' }}',
                                    width: '{{ $settings['container_width'] ?? '1280' }}',
                                    footer_copy: '{{ $settings['footer_text'] ?? '© 2026 Hotel Name. All rights reserved.' }}'
                                },
                                resetDefaults() {
                                    if(confirm('Are you sure you want to reset all theme settings to system defaults?')) {
                                        this.theme.primary = '#2563eb';
                                        this.theme.secondary = '#16a34a';
                                        this.theme.accent = '#f59e0b';
                                        this.theme.btn_bg = '#2563eb';
                                        this.theme.header_txt = '#111827';
                                        this.theme.footer_txt = '#4b5563';
                                        this.theme.heading_font = 'Inter';
                                        this.theme.body_font = 'Inter';
                                        this.theme.heading_weight = '700';
                                        this.theme.body_size = '16';
                                        this.theme.radius = '0.5';
                                        this.theme.width = '1280';
                                        this.theme.footer_copy = '© 2026 Hotel Name. All rights reserved.';
                                    }
                                }
                             }" class="space-y-8">

                {{-- RESET BUTTON BAR --}}
                <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
                    <div>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Appearance Presets</h3>
                        <p class="text-xs text-gray-500">Quickly restore or change your theme style.</p>
                    </div>
                    <button type="button" @click="resetDefaults()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-lg transition flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Set to Default
                    </button>
                </div>

                {{-- COLOR PALETTE SECTION --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                    <div class="mb-6 border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-bold text-gray-800">Color Palette</h3>
                        <p class="text-sm text-gray-500">Define your brand colors and button styles.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {{-- BRAND COLORS --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Primary Color (Brand)</label>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="theme.primary"
                                    class="h-11 w-16 p-1 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" name="settings[primary_color]" x-model="theme.primary"
                                    class="flex-1 p-2.5 bg-white border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Secondary Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="theme.secondary"
                                    class="h-11 w-16 p-1 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" name="settings[secondary_color]" x-model="theme.secondary"
                                    class="flex-1 p-2.5 bg-white border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Accent Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="theme.accent"
                                    class="h-11 w-16 p-1 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" name="settings[accent_color]" x-model="theme.accent"
                                    class="flex-1 p-2.5 bg-white border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        {{-- COMPONENT COLORS --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Button Background Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="theme.btn_bg"
                                    class="h-11 w-16 p-1 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" name="settings[button_primary_color]" x-model="theme.btn_bg"
                                    class="flex-1 p-2.5 bg-white border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Header Text Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="theme.header_txt"
                                    class="h-11 w-16 p-1 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" name="settings[header_text_color]" x-model="theme.header_txt"
                                    class="flex-1 p-2.5 bg-white border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Footer Text Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" x-model="theme.footer_txt"
                                    class="h-11 w-16 p-1 border border-gray-300 rounded-lg cursor-pointer">
                                <input type="text" name="settings[footer_text_color]" x-model="theme.footer_txt"
                                    class="flex-1 p-2.5 bg-white border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TYPOGRAPHY & LAYOUT SECTION --}}
                <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                    <div class="mb-6 border-b border-gray-200 pb-4">
                        <h3 class="text-lg font-bold text-gray-800">Typography & Layout</h3>
                        <p class="text-sm text-gray-500">Configure sizes, fonts, and global spacing.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Heading Font family</label>
                            <select name="settings[heading_font_family]" x-model="theme.heading_font"
                                class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <optgroup label="Sans-Serif (Modern)">
                                    <option value="Inter">Inter (System Default)</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Poppins">Poppins</option>
                                    <option value="Open Sans">Open Sans</option>
                                </optgroup>
                                <optgroup label="Serif (Elegant)">
                                    <option value="Playfair Display">Playfair Display</option>
                                    <option value="Lora">Lora</option>
                                    <option value="Merriweather">Merriweather</option>
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Body Font family</label>
                            <select name="settings[body_font_family]" x-model="theme.body_font"
                                class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <optgroup label="Sans-Serif">
                                    <option value="Inter">Inter (Standard)</option>
                                    <option value="Roboto">Roboto</option>
                                    <option value="Open Sans">Open Sans</option>
                                    <option value="Montserrat">Montserrat</option>
                                </optgroup>
                                <optgroup label="Serif (Classic)">
                                    <option value="Lora">Lora</option>
                                    <option value="Merriweather">Merriweather</option>
                                </optgroup>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Heading Weight</label>
                            <select name="settings[heading_font_weight]" x-model="theme.heading_weight"
                                class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="400">Normal (400)</option>
                                <option value="500">Medium (500)</option>
                                <option value="600">Semi-Bold (600)</option>
                                <option value="700">Bold (700)</option>
                                <option value="800">Extra-Bold (800)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Body Font Size</label>
                            <select name="settings[body_font_size]" x-model="theme.body_size"
                                class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="14">Small (14px)</option>
                                <option value="16">Standard (16px)</option>
                                <option value="18">Large (18px)</option>
                                <option value="20">Extra Large (20px)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Card & Button Roundness</label>
                            <select name="settings[border_radius]" x-model="theme.radius"
                                class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="0">Sharp Corners</option>
                                <option value="0.25">Subtle (Small)</option>
                                <option value="0.5">Standard (Medium)</option>
                                <option value="1">Soft (Large)</option>
                                <option value="2">Very Rounded</option>
                                <option value="100px">Pill-Shaped</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Website Width</label>
                            <select name="settings[container_width]" x-model="theme.width"
                                class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="1024">Narrow (1024px)</option>
                                <option value="1280">Standard (1280px)</option>
                                <option value="1440">Wide (1440px)</option>
                                <option value="1600">Full Screen (1600px)</option>
                            </select>
                        </div>

                        <div class="lg:col-span-3">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Footer Copyright Text</label>
                            <input type="text" name="settings[footer_text]" x-model="theme.footer_copy"
                                class="w-full p-2.5 bg-white border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 7. SEO TAB --}}
            <div x-show="activeTab === 'seo'" class="space-y-6" x-cloak>
                @php
                    $open = '{' . '{';
                    $close = '}' . '}';
                    $defaultMetaFormat = "{$open}page{$close} | {$open}site{$close}";
                @endphp
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">SEO & Scripts</h3>
                    <p class="text-sm text-gray-500 mb-4">Improve search engine visibility.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Meta Title Format</label>
                    <input type="text" name="settings[meta_title_format]"
                        value="{{ $settings['meta_title_format'] ?? $defaultMetaFormat }}"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Use <code>@{{page}}</code> and <code>@{{site}}</code> as
                        placeholders.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Global Meta Description</label>
                    <textarea name="settings[seo_description]" rows="3"
                        class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500">{{ $settings['seo_description'] ?? '' }}</textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                     <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">SEO Title Tagline</label>
                        <input type="text" name="settings[seo_tagline]" value="{{ $settings['seo_tagline'] ?? 'Luxury Redefined' }}"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500">
                     </div>
                     <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">SEO Keywords</label>
                        <input type="text" name="settings[seo_keywords]" value="{{ $settings['seo_keywords'] ?? 'hotel, luxury, booking' }}"
                            class="w-full p-2.5 bg-gray-50 border border-gray-300 rounded focus:ring-blue-500">
                     </div>
                </div>
            </div>

            {{-- 8. PAYMENTS TAB --}}
            <div x-show="activeTab === 'payments'" class="space-y-8" x-cloak
                x-data="{ mode: '{{ $settings['payment_mode'] ?? 'hotel_only' }}' }">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-1">Payment & Deposit Settings</h3>
                        <p class="text-sm text-gray-500">Manage how guests pay for their reservations.</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[payment_feature_enabled]" value="1" 
                               {{ ($settings['payment_feature_enabled'] ?? '0') === '1' ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-sm font-bold text-gray-900">Feature Active</span>
                    </label>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded-2xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Acceptance Model</label>
                            <select name="settings[payment_mode]" x-model="mode"
                                class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                <option value="hotel_only">Pay Entirely at Hotel (Offline)</option>
                                <option value="online_only">Full Payment Online (Enforced)</option>
                                <option value="partial_deposit">Partial Deposit / Advance Payment</option>
                            </select>
                            <p class="text-[10px] text-gray-400 mt-2 font-medium italic">
                                * "Partial Deposit" allows you to collect a commitment fee while keeping the balance at the
                                property.
                            </p>
                        </div>

                        <div x-show="mode === 'partial_deposit'" x-transition>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deposit Type</label>
                                    <select name="settings[deposit_type]"
                                        class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="percentage" {{ ($settings['deposit_type'] ?? '') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                        <option value="fixed" {{ ($settings['deposit_type'] ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount (₹)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Value</label>
                                    <input type="number" name="settings[deposit_value]"
                                        value="{{ $settings['deposit_value'] ?? '0' }}"
                                        class="w-full p-2.5 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4">
                    <div
                        class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900 mb-1">Important Integration Note</h4>
                        <p class="text-xs text-amber-700 leading-relaxed">
                            To accept online payments or deposits, ensure your **Stripe** or **Razorpay** keys are
                            configured in the environment file. If no gateway is active, the system will default to "Pay at
                            Hotel" regardless of these settings to prevent guest checkout failures.
                        </p>
                    </div>
                </div>
            </div>

        </form>
    </div>

@endsection