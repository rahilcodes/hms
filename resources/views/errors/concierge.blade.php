<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Concierge | LuxeStay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body
    class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-6 bg-[url('https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?q=80&w=2670&auto=format&fit=crop')] bg-cover bg-center bg-no-repeat relative">

    <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm"></div>

    <div
        class="relative z-10 max-w-md w-full bg-white/5 border border-white/10 p-10 rounded-[2rem] shadow-2xl text-center">

        <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-md">
            <span class="text-3xl">🛎️</span>
        </div>

        <h1 class="text-3xl font-bold mb-3 tracking-tight">Our Apologies.</h1>
        <p class="text-slate-300 font-light mb-8 leading-relaxed">
            We encountered an unexpected interruption. Our digital concierge has been notified and is attending to the
            matter.
        </p>

        @if(app()->bound('sentry'))
            <p class="text-[10px] text-slate-500 font-mono mb-6 uppercase tracking-widest">Error ID:
                {{ app('sentry')->getLastEventId() }}</p>
        @endif

        <div class="flex flex-col gap-3">
            <a href="{{ url('/') }}"
                class="bg-white text-slate-900 py-4 rounded-xl font-bold hover:bg-slate-100 transition shadow-lg transform hover:-translate-y-1">
                Return to Lobby
            </a>
            <a href="javascript:history.back()" class="text-white/50 text-sm hover:text-white transition py-2">
                Try Previous Page
            </a>
        </div>
    </div>
</body>

</html>