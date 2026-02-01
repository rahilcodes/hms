@extends('titanium.layouts.app')

@section('content')
    <div class="w-full max-w-md bg-gray-900 border border-gray-800 p-8 rounded-2xl shadow-2xl">
        <div class="text-center mb-8">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center border border-gray-600 shadow-xl mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Access Control</h1>
            <p class="text-sm text-gray-500 mt-2 font-mono">AUTHORIZED PERSONNEL ONLY</p>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-900/20 border border-red-900/50 p-3 rounded text-red-400 text-sm font-bold text-center">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('titanium.login.submit') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Identifier</label>
                <input type="email" name="email" required
                    class="w-full bg-black/50 border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-gray-500 transition placeholder-gray-600"
                    placeholder="admin@platform.com">
            </div>

            <div class="mb-8">
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Passcode</label>
                <input type="password" name="password" required
                    class="w-full bg-black/50 border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:border-gray-500 transition placeholder-gray-600"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full bg-white text-black font-bold py-3 rounded-lg hover:bg-gray-200 transition tracking-wide">
                AUTHENTICATE
            </button>
        </form>
    </div>
@endsection