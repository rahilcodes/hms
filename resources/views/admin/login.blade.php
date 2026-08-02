<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white w-full max-w-sm p-6 rounded shadow">
        <h1 class="text-xl font-bold mb-4 text-center">Admin Login</h1>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mb-3 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="mb-3">
                <label class="block text-sm mb-1">Email</label>
                <input type="email" name="email" required class="w-full border p-2 rounded">
            </div>

            <div class="mb-4">
                <label class="block text-sm mb-1">Password</label>
                <input type="password" name="password" required class="w-full border p-2 rounded">
            </div>

            <button class="w-full bg-black text-white py-2 rounded">
                Login
            </button>
        </form>
    </div>

</body>

</html>