@extends('titanium.layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold tracking-tight">Onboard New Hotel</h2>
            <a href="{{ route('titanium.dashboard') }}" class="text-sm text-gray-400 hover:text-white transition">Back to
                Dashboard</a>
        </div>

        <form action="{{ route('titanium.hotels.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- 1. Hotel Details -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h3 class="text-xl font-bold mb-4 text-blue-400">1. Property Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Hotel
                            Name</label>
                        <input type="text" name="hotel_name" value="{{ old('hotel_name') }}" required
                            class="w-full bg-gray-900 border-gray-700 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g. Grand Plaza Hotel">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Slug
                            (Subdomain)</label>
                        <input type="text" name="hotel_slug" value="{{ old('hotel_slug') }}" required
                            class="w-full bg-gray-900 border-gray-700 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="e.g. grand-plaza">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Phone</label>
                        <input type="text" name="hotel_phone" value="{{ old('hotel_phone') }}"
                            class="w-full bg-gray-900 border-gray-700 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="+1 234 567 890">
                    </div>
                </div>
            </div>

            <!-- 2. Super Admin -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h3 class="text-xl font-bold mb-4 text-green-400">2. Super Admin Account</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Full
                            Name</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" required
                            class="w-full bg-gray-900 border-gray-700 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="John Doe">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Email
                            Address</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" required
                            class="w-full bg-gray-900 border-gray-700 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="admin@hotel.com">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Initial
                            Password</label>
                        <input type="password" name="admin_password" required
                            class="w-full bg-gray-900 border-gray-700 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500"
                            placeholder="••••••••">
                    </div>
                </div>
            </div>

            <!-- 3. Subscription Plan -->
            <div class="bg-gray-800 border border-gray-700 rounded-xl p-6">
                <h3 class="text-xl font-bold mb-4 text-purple-400">3. Subscription Plan</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="plan" value="basic" class="peer sr-only" {{ old('plan') == 'basic' ? 'checked' : '' }}>
                        <div
                            class="p-4 rounded-xl border border-gray-700 bg-gray-900 peer-checked:border-blue-500 peer-checked:bg-blue-900/20 transition hover:border-gray-500">
                            <div class="text-lg font-bold text-white mb-1">Basic</div>
                            <div class="text-2xl font-bold text-gray-300 mb-2">$49<span
                                    class="text-sm font-normal text-gray-500">/mo</span></div>
                            <ul class="text-xs text-gray-400 space-y-1">
                                <li>• Housekeeping</li>
                            </ul>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="plan" value="pro" class="peer sr-only" {{ old('plan', 'pro') == 'pro' ? 'checked' : '' }}>
                        <div
                            class="p-4 rounded-xl border border-gray-700 bg-gray-900 peer-checked:border-blue-500 peer-checked:bg-blue-900/20 transition hover:border-gray-500">
                            <div class="text-lg font-bold text-white mb-1">Pro</div>
                            <div class="text-2xl font-bold text-gray-300 mb-2">$149<span
                                    class="text-sm font-normal text-gray-500">/mo</span></div>
                            <ul class="text-xs text-gray-400 space-y-1">
                                <li>• Housekeeping</li>
                                <li>• Financials</li>
                                <li>• CRM</li>
                            </ul>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="plan" value="enterprise" class="peer sr-only" {{ old('plan') == 'enterprise' ? 'checked' : '' }}>
                        <div
                            class="p-4 rounded-xl border border-gray-700 bg-gray-900 peer-checked:border-blue-500 peer-checked:bg-blue-900/20 transition hover:border-gray-500">
                            <div class="text-lg font-bold text-white mb-1">Enterprise</div>
                            <div class="text-2xl font-bold text-gray-300 mb-2">$499<span
                                    class="text-sm font-normal text-gray-500">/mo</span></div>
                            <ul class="text-xs text-gray-400 space-y-1">
                                <li>• All Features</li>
                                <li>• Priority Support</li>
                            </ul>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-4" x-data="{ loading: false }">
                <button type="submit" :disabled="loading" @click="loading = true"
                    class="w-full md:w-auto bg-blue-600 hover:bg-blue-500 text-white font-black py-4 px-10 rounded-xl shadow-xl shadow-blue-900/20 transition transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 uppercase tracking-widest text-xs">
                    <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span x-text="loading ? 'Creating Property...' : 'Launch Hotel'"></span>
                </button>
            </div>

            @if($errors->any())
                <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-4 mt-4">
                    <ul class="list-disc list-inside text-sm text-red-400">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

        </form>
    </div>
@endsection