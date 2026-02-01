<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Titanium Master Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-900 text-gray-100 antialiased">

    @auth('titanium')
        <div class="min-h-screen flex flex-col bg-gray-900 text-white" x-data="{ sidebarOpen: false }">
            <!-- Mobile Header -->
            <div
                class="md:hidden flex items-center justify-between bg-black/80 backdrop-blur-md border-b border-gray-800 px-6 py-4 sticky top-0 z-50">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center border border-gray-600">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>
                    <h1 class="font-black text-sm tracking-tighter uppercase">Titanium</h1>
                </div>
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-400 hover:text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Desktop Nav / Mobile Sidebar Container -->
            <nav :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed md:static inset-y-0 left-0 z-[60] w-64 md:w-full bg-black/95 md:bg-black/50 backdrop-blur-xl md:backdrop-blur border-r md:border-r-0 md:border-b border-gray-800 px-8 py-4 flex flex-col md:flex-row justify-between items-center transition-transform duration-300 md:translate-x-0">
                <div class="flex items-center gap-4 w-full md:w-auto mb-8 md:mb-0">
                    <div
                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-gray-700 to-gray-900 flex items-center justify-center border border-gray-600 shadow-xl hidden md:flex">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold tracking-tight">TITANIUM</h1>
                        <p class="text-[10px] text-gray-500 font-mono uppercase tracking-widest">Master Command</p>
                    </div>
                    <button @click="sidebarOpen = false" class="md:hidden ml-auto p-2 text-gray-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <div class="flex flex-col md:flex-row items-start md:items-center gap-6 w-full md:w-auto">
                    <p class="text-sm text-gray-400">Logged as <span
                            class="text-white font-bold">{{ auth('titanium')->user()->name }}</span></p>
                    <form method="POST" action="{{ route('titanium.logout') }}" class="w-full md:w-auto">
                        @csrf
                        <button
                            class="w-full md:w-auto text-xs font-black text-red-500 hover:text-white uppercase tracking-widest border border-red-900/30 bg-red-900/10 px-6 py-3 rounded-xl hover:bg-rose-600 transition duration-300">
                            Sign Out
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Mobile Overlay -->
            <div x-show="sidebarOpen" @click="sidebarOpen = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 md:hidden"></div>

            <!-- Content -->
            <main class="flex-1 p-8">
                @yield('content')
            </main>
        </div>
    @else
        <div class="min-h-screen bg-gray-950 flex items-center justify-center p-4">
            @yield('content')
        </div>
    @endauth

</body>

</html>