<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ site('hotel_name', 'LuxeStay') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
        }
    </style>
</head>

<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full">
        <div class="text-center mb-12">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2">
                <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-xl">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <span class="text-2xl font-black tracking-tighter">{{ site('hotel_name', 'LuxeStay') }}</span>
            </a>
        </div>

        <div class="glass border border-white rounded-[3rem] p-12 shadow-2xl shadow-slate-200/50 text-center">
            <h1 class="text-7xl font-black text-slate-900 mb-6 tracking-tighter">@yield('code')</h1>
            <h2 class="text-xl font-bold text-slate-800 mb-4">@yield('message')</h2>
            <p class="text-slate-500 mb-10 text-sm leading-relaxed">
                @yield('description', "We apologize for the inconvenience. Our concierge is looking into it.")
            </p>
            <a href="{{ route('home') }}"
                class="inline-flex items-center gap-3 bg-slate-900 text-white px-10 py-5 rounded-3xl font-bold hover:bg-blue-600 transition-all shadow-xl shadow-blue-100/10">
                Back to Safety &rarr;
            </a>
        </div>
    </div>
</body>

</html>