<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - {{ site('hotel_name', 'Hotel Booking') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-active {
            background: #f1f5f9;
            color: #0f172a;
            border-right: 3px solid #2563eb;
        }

        .glass-sidebar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased" x-data="{ sidebarOpen: false }">

    {{-- MOBILE HEADER --}}
    <div class="md:hidden flex items-center justify-between bg-white border-b px-5 py-3 sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <div
                class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <span class="font-black text-lg text-slate-900 tracking-tight">Admin<span
                    class="text-blue-600">.</span></span>
        </div>
        <div class="flex items-center gap-2">
            {{-- Mobile Search Trigger --}}
            <button @click="$dispatch('open-search')"
                class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-blue-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>

            <button @click="sidebarOpen = !sidebarOpen"
                class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-700 hover:bg-slate-50 transition shadow-sm">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>

    <div class="flex h-screen overflow-hidden">

        {{-- SIDEBAR --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-40 w-72 glass-sidebar border-r border-slate-200 transition-transform duration-300 md:static md:translate-x-0 flex-shrink-0 flex flex-col shadow-2xl md:shadow-none bg-white">

            {{-- SIDEBAR BRAND --}}
            <div class="h-20 flex items-center px-6 border-b border-slate-100 mb-2">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-base font-black text-slate-900 leading-none mb-1">Hotel Manager</h1>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Enterprise v2.0</p>
                    </div>
                </div>
            </div>

            {{-- SIDEBAR NAV --}}
            <nav class="flex-1 overflow-y-auto px-4 space-y-2 py-4 custom-scrollbar">
                @php $hotel = auth('admin')->user()->hotel; @endphp

                {{-- 1. FRONT DESK --}}
                @if($hotel->hasFeature('front-desk'))
                    <div class="px-2 pb-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 pl-2">Front Desk</p>

                        <a href="{{ route('admin.dashboard') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.dashboard') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Front Desk Dashboard
                        </a>

                        <a href="{{ route('admin.front-desk') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.front-desk') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Check-In / Check-Out
                        </a>

                        <a href="{{ route('admin.tape-chart.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.tape-chart.*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2-2v8a2 2 0 002 2z" />
                            </svg>
                            Room Chart
                        </a>

                        <a href="{{ route('admin.requests.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.requests*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            Guest Requests
                        </a>

                        <a href="{{ route('admin.dining.orders.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.dining.orders.*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            Dining Orders
                        </a>

                        <a href="{{ route('admin.menu.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.menu.*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Menu Management
                        </a>

                        <a href="{{ route('admin.bookings.calendar') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.bookings.calendar') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                            </svg>
                            Booking Calendar
                        </a>

                        @if(auth('admin')->user()->isRole('receptionist') || auth('admin')->user()->isSuperAdmin() || auth('admin')->user()->isRole('admin'))
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <a href="{{ route('admin.bookings.create') }}"
                                    class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-100 hover:bg-blue-700 transition duration-200 mb-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    New Reservation
                                </a>

                                <a href="{{ route('admin.bookings.index') }}"
                                    class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ (request()->routeIs('admin.bookings*') && !request()->routeIs('admin.bookings.calendar') && !request()->routeIs('admin.bookings.create')) ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                                    </svg>
                                    Booking List
                                </a>
                            </div>
                        @endif
                    </div>

                @endif

                {{-- 2. GUESTS & COMPANIES --}}
                @if($hotel->hasFeature('crm'))
                    <div class="px-2 pt-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 pl-2">Guests &
                            Companies</p>

                        <a href="{{ route('admin.guests.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.guests*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Guest Profiles
                        </a>

                        <a href="{{ route('admin.companies.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.companies*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Corporate Accounts
                        </a>
                    </div>

                @endif

                {{-- 3. OPERATIONS --}}
                <div class="px-2 pt-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 pl-2">Operations</p>

                    @if($hotel->hasFeature('financials'))

                        <a href="{{ route('admin.night-audit.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.night-audit*') ? 'sidebar-active text-emerald-600 shadow-sm ring-1 ring-emerald-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            End of Day
                        </a>

                    @endif

                    @if(auth('admin')->user()->isRole('housekeeping') || auth('admin')->user()->isSuperAdmin() || auth('admin')->user()->isRole('admin'))
                        @if($hotel->hasFeature('housekeeping'))
                            <a href="{{ route('admin.housekeeping.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.housekeeping*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                Housekeeping
                            </a>

                            <a href="{{ route('admin.maintenance.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.maintenance*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Maintenance Requests
                            </a>

                            <a href="{{ route('admin.lost-found.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.lost-found.*') ? 'sidebar-active text-amber-600 shadow-sm ring-1 ring-amber-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Lost & Found
                            </a>
                        @endif

                        @if($hotel->hasFeature('inventory'))
                            <a href="{{ route('admin.laundry.vendors.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.laundry.vendors*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                Suppliers
                            </a>
                        @endif
                    @endif
                </div>

                {{-- 4. INVENTORY --}}
                @if($hotel->hasFeature('inventory'))
                    <div class="px-2 pt-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 pl-2">Inventory</p>

                        @if(auth('admin')->user()->isRole('housekeeping') || auth('admin')->user()->isSuperAdmin() || auth('admin')->user()->isRole('admin'))
                            <a href="{{ route('admin.assets.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.assets*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Hotel Assets
                            </a>

                            <a href="{{ route('admin.linen.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.linen*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                Linen Stock
                            </a>

                            <a href="{{ route('admin.laundry.batches.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.laundry.batches*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Laundry Tracking
                            </a>
                        @endif
                    </div>

                @endif

                {{-- 5. REVENUE & OFFERS --}}
                @if(auth('admin')->user()->isRole('revenue') || auth('admin')->user()->isSuperAdmin() || auth('admin')->user()->isRole('admin'))
                    @if($hotel->hasFeature('financials'))
                        <div class="px-2 pt-4">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 pl-2">Revenue &
                                Offers</p>

                            <a href="{{ route('admin.inventory.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.inventory*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                                </svg>
                                Room Availability
                            </a>

                            <a href="{{ route('admin.pricing-rules.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.pricing-rules*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Room Pricing
                            </a>

                            <a href="{{ route('admin.blocked-dates.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.blocked-dates*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Blocked Dates
                            </a>

                            <a href="{{ route('admin.services.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.services*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Add-On Services
                            </a>

                            <a href="{{ route('admin.coupons.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.coupons*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                Offers & Promotions
                            </a>

                            <a href="{{ route('admin.yield.index') }}"
                                class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.yield*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                Yield Intelligence
                            </a>
                        </div>
                    @endif
                @endif

                {{-- 6. ACCOUNTS --}}
                <div class="px-2 pt-4">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 pl-2">Accounts</p>

                    @if(auth('admin')->user()->isSuperAdmin() || auth('admin')->user()->isRole('revenue') || auth('admin')->user()->isRole('admin'))
                        <a href="{{ route('admin.analytics') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.analytics') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Reports
                        </a>
                        @if(auth('admin')->user()->isSuperAdmin())
                            @if($hotel->hasFeature('financials'))
                                <a href="{{ route('admin.financials.index') }}"
                                    class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.financials*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                                    <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2-2v14a2 2 0 002 2z" />
                                    </svg>
                                    Accounts Ledger
                                </a>
                            @endif
                        @endif
                        <a href="{{ route('admin.room-types.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.room-types*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            Room Types
                        </a>
                    @endif
                </div>

                {{-- 7. SETTINGS --}}
                @if(auth('admin')->user()->isSuperAdmin())
                    <div class="px-2 pt-4">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 pl-2">Content &
                            Settings</p>

                        <a href="{{ route('admin.pages.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.pages*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            Website Pages
                        </a>

                        <a href="{{ route('admin.site-settings.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.site-settings*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Hotel Settings
                        </a>
                        <a href="{{ route('admin.staff.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.staff*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Staff Management
                        </a>
                        <a href="{{ route('admin.audit-logs.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.audit-logs*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.040L3 5.382V12c0 5.108 3.107 9.47 7.5 11.132 4.393-1.662 7.5-6.024 7.5-11.132V5.382l-.882-.398z" />
                            </svg>
                            Activity Log
                        </a>
                        <a href="{{ route('admin.subscription.index') }}"
                            class="flex items-center gap-3 px-4 py-3.5 text-sm font-bold rounded-2xl transition duration-200 {{ request()->routeIs('admin.subscription*') ? 'sidebar-active text-blue-600 shadow-sm ring-1 ring-blue-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="w-5 h-5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Subscription & Billing
                        </a>
                    </div>
                @endif
            </nav>

            {{-- SIDEBAR FOOTER --}}
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3 px-2 mb-4">
                    <div
                        class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold overflow-hidden border border-white shadow-sm">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                        </svg>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ auth('admin')->user()->name }}</p>
                        <p class="text-xs text-slate-500 truncate">{{ auth('admin')->user()->email }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl shadow-sm text-sm font-bold text-red-600 hover:bg-red-50 hover:border-red-100 transition duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col overflow-hidden" x-data="{ 
                searchOpen: false, 
                searchQuery: '', 
                searchResults: [],
                isSearching: false,
                searchError: false,
                performSearch() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        this.searchError = false;
                        return;
                    }
                    this.isSearching = true;
                    this.searchError = false;
                    fetch(`{{ route('admin.search') }}?query=${encodeURIComponent(this.searchQuery)}`)
                        .then(res => {
                            if(!res.ok) throw new Error();
                            return res.json();
                        })
                        .then(data => {
                            this.searchResults = data;
                            this.isSearching = false;
                        })
                        .catch(() => {
                            this.isSearching = false;
                            this.searchError = true;
                            this.searchResults = [];
                        });
                }
            }" @keydown.window.ctrl.k.prevent="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
            @keydown.window.cmd.k.prevent="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
            @open-search.window="searchOpen = true; $nextTick(() => $refs.searchInput.focus())">

            {{-- SEARCH OVERLAY --}}
            <div x-show="searchOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-sm flex items-start justify-center md:pt-20 pt-4 px-4"
                @click.self="searchOpen = false" style="display: none;">

                <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform h-fit"
                    x-show="searchOpen" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    @keydown.escape.window="searchOpen = false">

                    <div class="p-6 border-b border-slate-100 flex items-center gap-4">
                        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" x-ref="searchInput" x-model="searchQuery"
                            @input.debounce.300ms="performSearch()" placeholder="Search bookings, guests..."
                            class="flex-1 bg-transparent border-none text-lg font-semibold text-slate-900 focus:ring-0 placeholder-slate-300 outline-none">
                        <button @click="searchOpen = false" class="md:hidden p-2 text-slate-400 hover:text-slate-900">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="max-h-[60vh] overflow-y-auto p-4 custom-scrollbar">
                        <template x-if="isSearching">
                            <div class="py-12 text-center">
                                <div
                                    class="inline-block w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin">
                                </div>
                                <p class="text-[10px] font-bold text-slate-400 mt-4 uppercase tracking-widest">Searching records...</p>
                            </div>
                        </template>

                        <template x-if="searchError">
                            <div class="py-12 text-center text-rose-500">
                                <svg class="w-12 h-12 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <p class="text-sm font-bold">Search failed. Please check connection.</p>
                            </div>
                        </template>

                        <nav class="space-y-1">
                            <template x-for="result in searchResults" :key="result.url">
                                <a :href="result.url"
                                    class="flex items-center gap-4 p-4 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition group">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                                        :class="{
                                            'bg-blue-50 text-blue-600': result.type === 'Booking',
                                            'bg-emerald-50 text-emerald-600': result.type === 'Guest',
                                            'bg-amber-50 text-amber-600': result.type === 'Room Type'
                                        }">
                                        <template x-if="result.icon === 'calendar'"><svg class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z" />
                                            </svg></template>
                                        <template x-if="result.icon === 'user'"><svg class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg></template>
                                        <template x-if="result.icon === 'home'"><svg class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                            </svg></template>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <h4 class="text-sm font-bold text-slate-900 truncate" x-text="result.title">
                                            </h4>
                                            <span
                                                class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-slate-100 text-slate-400 uppercase tracking-tighter"
                                                x-text="result.type"></span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 font-medium truncate"
                                            x-text="result.subtitle">
                                        </p>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-600 transition" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            </template>
                        </nav>
                    </div>

                    <div
                        class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-between items-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                        <div class="flex items-center gap-4">
                            <span><span
                                    class="px-1.5 py-0.5 bg-white border border-slate-200 rounded shadow-sm text-slate-600 mr-1.5">Cmd
                                    + K</span> to search</span>
                            <span><span
                                    class="px-1.5 py-0.5 bg-white border border-slate-200 rounded shadow-sm text-slate-600 mr-1.5">Esc</span>
                                to close</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TOP HEADER --}}
            <header
                class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-20 flex items-center justify-between px-8 hidden md:flex sticky top-0 z-30">
                <div>
                    <h2 class="font-bold text-xl text-slate-900 tracking-tight">
                        @yield('header_title', 'Dashboard')
                    </h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Welcome back,
                        {{ explode(' ', auth('admin')->user()->name)[0] }}
                    </p>
                </div>

                <div class="flex items-center gap-6">
                    {{-- QUICK ACTIONS --}}
                    <div class="flex items-center gap-2 pr-6 border-r border-slate-100">
                        <a href="{{ route('home') }}" target="_blank"
                            class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-600 hover:text-blue-600 transition group">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Live Site
                        </a>
                    </div>

                    {{-- SEARCH BAR (Command Center Style) --}}
                    <button @click="searchOpen = true; $nextTick(() => $refs.searchInput.focus())"
                        class="hidden md:flex items-center gap-3 px-4 py-2.5 bg-slate-100/50 hover:bg-slate-100 text-slate-400 rounded-2xl w-64 transition-all border border-transparent hover:border-slate-200 group">
                        <svg class="w-4 h-4 opacity-70 group-hover:text-blue-600 transition" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="text-xs font-semibold tracking-tight">Command Center...</span>
                        <span
                            class="ml-auto text-[10px] font-bold px-1.5 py-0.5 bg-white border border-slate-200 rounded-lg shadow-sm">Cmd+K</span>
                    </button>

                    {{-- NOTIFICATION BELL & TOASTS --}}
                    <div x-data="{ 
                            notifOpen: false, 
                            unreadCount: 0,
                            notifications: [],
                            toasts: [],
                            fetchHistory() {
                                fetch('{{ route('admin.notifications.index') }}')
                                    .then(res => res.json())
                                    .then(data => {
                                        this.notifications = data;
                                        this.unreadCount = data.filter(n => !n.is_read).length;
                                    });
                            },
                            fetchNotifications() {
                                fetch('{{ route('admin.notifications.check') }}')
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.length > 0) {
                                            data.forEach(note => {
                                                // Check if we already have this notification in our local list
                                                const exists = this.notifications.some(existing => existing.id === note.id);
                                                
                                                if (!exists) {
                                                    this.notifications.unshift(note);
                                                    this.showToast(note);
                                                }
                                            });
                                            
                                            // Update unread count based on total unread items
                                            this.unreadCount = this.notifications.filter(n => !n.is_read).length;
                                        }
                                    });
                            },
                            showToast(note) {
                                const id = Date.now() + Math.random();
                                this.toasts.push({ ...note, id });
                                setTimeout(() => {
                                    this.toasts = this.toasts.filter(t => t.id !== id);
                                }, 8000);
                            },
                            markAllRead() {
                                fetch('{{ route('admin.notifications.markAllRead') }}', {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    }
                                }).then(() => {
                                    this.unreadCount = 0;
                                    this.notifications.forEach(n => n.is_read = true);
                                });
                            }
                        }"
                        x-init="fetchHistory(); setInterval(() => { if(document.visibilityState === 'visible') fetchNotifications() }, 15000)"
                        @click.outside="notifOpen = false" class="relative">

                        <!-- Toast Container -->
                        <div class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none">
                            <template x-for="toast in toasts" :key="toast.id">
                                <div class="w-80 p-5 bg-white rounded-3xl shadow-2xl border border-slate-100 flex gap-4 animate-in slide-in-from-right-10 duration-500 pointer-events-auto cursor-pointer"
                                    @click="notifOpen = true; toasts = toasts.filter(t => t.id !== toast.id)">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                                        :class="{
                                            'bg-blue-50 text-blue-600': toast.type === 'info',
                                            'bg-amber-50 text-amber-600': toast.type === 'warning',
                                            'bg-rose-50 text-rose-600': toast.type === 'urgent'
                                        }">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 overflow-hidden">
                                        <h4 class="text-sm font-black text-slate-900 leading-tight"
                                            x-text="toast.title"></h4>
                                        <p class="text-xs text-slate-500 mt-1 font-medium leading-relaxed truncate"
                                            x-text="toast.message"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <button @click="notifOpen = !notifOpen"
                            class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-blue-600 transition relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Badge -->
                            <span x-show="unreadCount > 0" style="display: none;"
                                class="absolute top-2 right-2 w-2 h-2 bg-rose-500 rounded-full ring-2 ring-white"></span>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="notifOpen" style="display: none;"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-2"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">

                            <div class="px-4 py-3 border-b border-slate-50 flex items-center justify-between">
                                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-widest">Notifications
                                </h3>
                                <button @click="markAllRead()"
                                    class="text-[10px] font-bold text-blue-600 hover:text-blue-700">Mark all
                                    read</button>
                            </div>

                            <div class="max-h-80 overflow-y-auto custom-scrollbar">
                                <template x-if="notifications.length === 0">
                                    <div class="p-8 text-center">
                                        <p class="text-xs text-slate-400">No new notifications</p>
                                    </div>
                                </template>

                                <template x-for="note in notifications" :key="note.id">
                                    <div class="p-4 border-b border-slate-50 hover:bg-slate-50 transition last:border-0"
                                        :class="note.is_read ? 'bg-white' : 'bg-blue-50/40'">
                                        <div class="flex items-start gap-3">
                                            <div class="w-2 h-2 mt-1.5 rounded-full flex-shrink-0" :class="{
                                                    'bg-blue-500': note.type === 'info',
                                                    'bg-amber-500': note.type === 'warning',
                                                    'bg-red-500': note.type === 'urgent'
                                                }"></div>
                                            <div>
                                                <h4 class="text-sm font-bold text-slate-900 leading-tight"
                                                    x-text="note.title"></h4>
                                                <p class="text-xs text-slate-500 mt-1 leading-relaxed"
                                                    x-text="note.message"></p>
                                                <p class="text-[10px] text-slate-400 mt-2 font-mono"
                                                    x-text="new Date(note.created_at).toLocaleTimeString()"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- SCROLLABLE CONTENT --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 md:p-8 bg-slate-50/50">

                {{-- STATUS MESSAGES --}}
                @if(session('success'))
                    <div
                        class="mb-8 bg-white border border-emerald-100 p-4 rounded-2xl shadow-sm flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
                        <div
                            class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-tight">Action Successful</p>
                            <p class="text-xs text-emerald-600 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div
                        class="mb-8 bg-white border border-rose-100 p-4 rounded-2xl shadow-sm flex items-start gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
                        <div
                            class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-600 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-slate-900 leading-tight">Wait, we found {{ $errors->count() }}
                                errors</p>
                            <ul class="mt-1 text-xs text-rose-600 font-medium list-disc pl-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                {{-- DYNAMIC PAGE CONTENT --}}
                <div class="max-w-[1600px] mx-auto">
                    @yield('content')
                </div>
            </main>
        </div>

        {{-- MOBILE OVERLAY --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-30 md:hidden">
        </div>
    </div>
    </div>

    <!-- Titanium Notification Poller with Toasts -->
    <div x-data="titaniumNotifications()" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none">
        <template x-for="note in notifications" :key="note.id">
            <div x-show="true" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-x-full opacity-0" x-transition:enter-end="translate-x-0 opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="translate-x-0 opacity-100" x-transition:leave-end="translate-x-full opacity-0"
                class="pointer-events-auto w-96 rounded-xl shadow-2xl border flex overflow-hidden backdrop-blur-md"
                :class="{
                    'bg-white/90 border-blue-100': note.type === 'info',
                    'bg-amber-50/95 border-amber-200': note.type === 'warning',
                    'bg-red-50/95 border-red-200': note.type === 'urgent'
                 }">

                <!-- Icon -->
                <div class="w-12 flex items-center justify-center shrink-0" :class="{
                        'bg-blue-500': note.type === 'info',
                        'bg-amber-500': note.type === 'warning',
                        'bg-red-500': note.type === 'urgent'
                     }">
                    <template x-if="note.type === 'info'">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </template>
                    <template x-if="note.type === 'warning'">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </template>
                    <template x-if="note.type === 'urgent'">
                        <svg class="w-6 h-6 text-white animate-pulse" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </template>
                </div>

                <!-- Content -->
                <div class="flex-1 p-4">
                    <div class="flex items-start justify-between">
                        <h4 class="font-bold text-sm" :class="{
                                'text-blue-900': note.type === 'info',
                                'text-amber-900': note.type === 'warning',
                                'text-red-900': note.type === 'urgent'
                            }" x-text="note.title"></h4>
                        <button @click="remove(note.id)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs mt-1" :class="{
                          'text-blue-700': note.type === 'info',
                          'text-amber-700': note.type === 'warning',
                          'text-red-700': note.type === 'urgent'
                       }" x-text="note.message"></p>
                    <p class="text-[10px] mt-2 opacity-60 font-mono uppercase" :class="{
                          'text-blue-800': note.type === 'info',
                          'text-amber-800': note.type === 'warning',
                          'text-red-800': note.type === 'urgent'
                       }">From: Creativals</p>
                </div>
            </div>
        </template>
    </div>

    <script>
        function titaniumNotifications() {
            return {
                notifications: [],
                init() {
                    // Check every 30 seconds
                    setInterval(() => this.poll(), 30000);
                    // Check immediately
                    this.poll();
                },
                poll() {
                    fetch('{{ route("admin.notifications.check") }}')
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                data.forEach(note => {
                                    this.add(note);
                                });
                            }
                        })
                        .catch(err => console.error('Notification Poll Error:', err));
                },
                add(note) {
                    // Prevent duplicates if polling overlaps
                    if (this.notifications.some(n => n.id === note.id)) return;

                    this.notifications.push(note);

                    // Auto dismiss after 10s unless urgent
                    if (note.type !== 'urgent') {
                        setTimeout(() => {
                            this.remove(note.id);
                        }, 10000);
                    }
                },
                remove(id) {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }
            }
        }
    </script>
</body>

</html>