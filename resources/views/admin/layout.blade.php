<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">

    <header class="bg-white shadow mb-6">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center gap-6">
                <h1 class="text-lg font-bold">Admin Dashboard</h1>
                <x-admin.command-center />
            </div>

            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button class="text-red-600">Logout</button>
            </form>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4">
        @if(session('success'))
            <div class="btn-secondary text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>

</html>