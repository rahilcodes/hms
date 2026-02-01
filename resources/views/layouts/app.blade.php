<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO ENGINE --}}
    <title>{{ site('seo_title', site('hotel_name', 'LuxeStay') . ' | ' . site('seo_tagline', 'Luxury Redefined')) }}
    </title>
    <meta name="description"
        content="{{ site('seo_description', 'Experience the pinnacle of luxury and comfort in our exclusive suites.') }}">
    <meta name="keywords" content="{{ site('seo_keywords', 'luxury hotel, suites, booking, 5 star') }}">
    <meta property="og:title" content="{{ site('seo_title', site('hotel_name', 'LuxeStay')) }}">
    <meta property="og:description" content="{{ site('seo_description') }}">
    <meta property="og:image" content="{{ site('seo_og_image', asset('images/og-default.jpg')) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">

    <!-- FONTS -->
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">

    <!-- SCRIPTS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    <script>
        window.SITE_WHATSAPP = "{{ site('contact_whatsapp') }}";
    </script>

    @stack('scripts')
</head>

<body class="bg-slate-50 text-slate-900 antialiased flex flex-col min-h-screen">

    {{-- HEADER --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">

                {{-- LOGO --}}
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        @if(site('site_logo'))
                            <img src="{{ asset('storage/' . site('site_logo')) }}"
                                alt="{{ site('hotel_name', 'LuxeStay') }}"
                                class="h-10 w-auto transition-transform group-hover:scale-105">
                        @else
                            <div
                                class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white shadow-lg transition-transform group-hover:scale-105">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif
                        <div class="ml-3">
                            <h1 class="text-xl font-bold text-slate-900 tracking-tight leading-none">
                                {{ site('hotel_name', 'LuxeStay') }}
                            </h1>
                            <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mt-0.5">Hotels &
                                Resorts</p>
                        </div>
                    </a>
                </div>

                {{-- NAV --}}
                <nav class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Home</a>
                    <a href="{{ route('pages.about') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">About</a>
                    <a href="{{ route('rooms') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Suites</a>
                    <a href="{{ route('pages.dining') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Dining</a>
                    <a href="{{ route('pages.gallery') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Gallery</a>
                    <a href="{{ route('pages.contact') }}"
                        class="text-sm font-semibold text-slate-600 hover:text-blue-600 transition">Contact</a>
                </nav>

                {{-- ACTIONS --}}
                <div class="flex items-center gap-4">
                    {{-- GUEST PORTAL BTN --}}
                    <a href="{{ route('guest.login') }}"
                        class="hidden md:flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-blue-600 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Guest Login
                    </a>

                    <a href="{{ route('rooms') }}" class="btn-primary flex items-center gap-2 shadow-blue-200">
                        <span>Book Now</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>

                    {{-- MOBILE MENU BTN --}}
                    <button class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-slate-900 text-white pt-16 pb-8 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">

                {{-- COL 1 --}}
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span
                            class="text-xl font-bold font-display tracking-tight">{{ site('hotel_name', 'LuxeStay') }}</span>
                    </div>
                    <p class="text-slate-400 text-sm leading-relaxed mb-6">
                        Experience the pinnacle of luxury and comfort. Designed for the modern traveler.
                    </p>
                    <div class="flex gap-4">
                        {{-- SOCIAL ICONS --}}
                        <a href="#"
                            class="w-8 h-8 hover:bg-white/10 rounded-full flex items-center justify-center transition">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-8 h-8 hover:bg-white/10 rounded-full flex items-center justify-center transition">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- COL 2 --}}
                <div>
                    <h3 class="text-white font-bold mb-4">Explore</h3>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('pages.about') }}" class="hover:text-blue-400 transition">Our Story</a>
                        </li>
                        <li><a href="{{ route('rooms') }}" class="hover:text-blue-400 transition">Accommodations</a>
                        </li>
                        <li><a href="{{ route('pages.dining') }}" class="hover:text-blue-400 transition">Dining</a></li>
                        <li><a href="{{ route('pages.gallery') }}" class="hover:text-blue-400 transition">Gallery</a>
                        </li>
                    </ul>
                </div>

                {{-- COL 3 --}}
                <div>
                    <h3 class="text-white font-bold mb-4">Support</h3>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-blue-400 transition">Manage Booking</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="hover:text-blue-400 transition">Contact Us</a>
                        </li>
                        <li><a href="{{ route('titanium.login') }}"
                                class="hover:text-indigo-400 font-bold transition flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full"></span>
                                Platform Control
                            </a>
                        </li>
                        <li><a href="{{ route('admin.login') }}"
                                class="hover:text-blue-400 transition flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>
                                Hotel Admin
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- COL 4 --}}
                <div>
                    <h3 class="text-white font-bold mb-4">Stay Updated</h3>
                    <p class="text-slate-400 text-sm mb-4">Subscribe for exclusive offers and news.</p>
                    <form action="#" class="flex">
                        <input type="email" placeholder="Email address"
                            class="bg-slate-800 border-none rounded-l-lg text-sm w-full focus:ring-1 focus:ring-blue-500 text-white placeholder-slate-500">
                        <button class="bg-blue-600 px-4 rounded-r-lg hover:bg-blue-500 transition font-bold text-sm">
                            &rarr;
                        </button>
                    </form>
                </div>

            </div>

            <div
                class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} {{ site('hotel_name', 'Hotel Booking') }}. All rights reserved.</p>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- TOAST SYSTEM --}}
    <div id="toast-container"
        class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[1000] flex flex-col gap-3 pointer-events-none"></div>

    <script>
        window.showToast = function (message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            const colors = {
                success: 'bg-slate-900 border-slate-800 text-white',
                error: 'bg-rose-600 border-rose-500 text-white',
                info: 'bg-blue-600 border-blue-500 text-white'
            };

            const icons = {
                success: '<svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                error: '<svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                info: '<svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
            };

            toast.className = `pointer-events-auto flex items-center gap-4 px-6 py-4 rounded-2xl border shadow-2xl transition-all duration-500 transform translate-y-10 opacity-0 ${colors[type] || colors.success}`;
            toast.innerHTML = `<div class="shrink-0">${icons[type] || icons.success}</div> <span class="text-sm font-bold tracking-tight">${message}</span>`;

            container.appendChild(toast);

            // Animation In
            setTimeout(() => {
                toast.classList.remove('translate-y-10', 'opacity-0');
            }, 10);

            // Animation Out
            setTimeout(() => {
                toast.classList.add('translate-y-10', 'opacity-0');
                setTimeout(() => toast.remove(), 500);
            }, 4000);
        };

        // Auto-show Laravel Flashes
        @if(session('success')) showToast("{{ session('success') }}", 'success'); @endif
        @if(session('error')) showToast("{{ session('error') }}", 'error'); @endif
        @if($errors->any()) showToast("Something needs your attention.", 'error'); @endif
    </script>
</body>

</html>