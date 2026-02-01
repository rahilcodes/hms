@extends('titanium.layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto">
        <!-- Breadcrumb -->
        <a href="{{ route('titanium.dashboard') }}"
            class="inline-flex items-center gap-2 text-gray-500 hover:text-white mb-6 transition text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Back to Dashboard
        </a>

        <!-- Header -->
        <div class="flex items-start justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-white mb-2">{{ $hotel->name }}</h1>
                <p class="text-gray-400 flex items-center gap-2">
                    <span
                        class="px-2 py-0.5 rounded bg-gray-800 text-gray-300 text-xs font-mono border border-gray-700">{{ $hotel->slug }}</span>
                    <span class="text-gray-600">•</span>
                    {{ $hotel->email }}
                </p>
            </div>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('titanium.hotels.impersonate', $hotel) }}">
                    @csrf
                    <button
                        class="bg-blue-600 text-white font-bold py-2 px-4 rounded text-sm hover:bg-blue-500 transition shadow-lg shadow-blue-900/20">
                        Shadow Login
                    </button>
                </form>
            </div>
        </div>

        <!-- Feature Toggling -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden mb-8">
            <div class="p-6 border-b border-gray-700 bg-gray-800/50">
                <h3 class="font-bold text-lg text-white">Feature Management</h3>
                <p class="text-sm text-gray-500">Enable or disable system modules for this property.</p>
            </div>

            <div class="divide-y divide-gray-700">
                @foreach($allFeatures as $feature)
                    @php
                        $isActive = $hotel->hasFeature($feature->slug);
                    @endphp
                    <div class="p-6 flex items-center justify-between hover:bg-gray-750 transition">
                        <div>
                            <h4 class="font-bold text-white text-base">{{ $feature->name }}</h4>
                            <p class="text-sm text-gray-400 mt-0.5">{{ $feature->description }}</p>
                        </div>

                        <form method="POST" action="{{ route('titanium.hotels.features.toggle', $hotel) }}">
                            @csrf
                            <input type="hidden" name="feature_id" value="{{ $feature->id }}">

                            <!-- Toggle Switch -->
                            <div class="flex items-center gap-3">
                                <span
                                    class="text-xs font-bold uppercase tracking-widest {{ $isActive ? 'text-green-500' : 'text-gray-600' }}">
                                    {{ $isActive ? 'Enabled' : 'Disabled' }}
                                </span>

                                <button type="submit" name="is_enabled" value="{{ $isActive ? '0' : '1' }}"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900 {{ $isActive ? 'bg-green-600' : 'bg-gray-600' }}">
                                    <span class="sr-only">Use setting</span>
                                    <span aria-hidden="true"
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $isActive ? 'translate-x-5' : 'translate-x-0' }}">
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Subscription Overview -->
        <!-- Subscription Management -->
        @if($hotel->subscription)
            <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-lg">
                <div class="p-6 border-b border-gray-700 bg-gray-800/50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg text-white">Subscription & Billing</h3>
                        <p class="text-sm text-gray-500">Manage plan and billing details.</p>
                    </div>
                    <span
                        class="inline-flex items-center rounded-md bg-green-400/10 px-2 py-1 text-xs font-medium text-green-400 ring-1 ring-inset ring-green-400/20">Active</span>
                </div>

                <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Current Status -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Current Plan</p>
                                <p class="text-xl font-bold text-white">{{ $hotel->subscription->plan_name }}</p>
                            </div>
                            <div class="bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Price</p>
                                <p class="text-xl font-bold text-white">₹{{ number_format($hotel->subscription->price) }}<span
                                        class="text-sm font-normal text-gray-400">/mo</span></p>
                            </div>
                            <div class="bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Next Billing</p>
                                <p
                                    class="text-lg font-bold {{ $hotel->subscription->next_billing_date->isPast() ? 'text-red-400' : 'text-white' }}">
                                    {{ $hotel->subscription->next_billing_date->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="bg-gray-700/50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 uppercase tracking-widest mb-1">Cycle</p>
                                <p class="text-lg font-bold text-white capitalize">{{ $hotel->subscription->billing_cycle }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Upgrade/Downgrade -->
                    <div class="bg-gray-900/50 p-6 rounded-xl border border-gray-700">
                        <form method="POST" action="{{ route('titanium.hotels.subscription.update', $hotel) }}">
                            @csrf
                            @method('PATCH')

                            <h4 class="font-bold text-white mb-4">Change Plan</h4>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Select New Plan</label>
                                    <select name="plan"
                                        class="w-full bg-gray-800 border-gray-600 rounded-lg text-white focus:ring-blue-500 focus:border-blue-500">
                                        @php
                                            $currentSlug = strtolower(explode(' ', $hotel->subscription->plan_name)[0]);
                                        @endphp

                                        <option value="basic" {{ $currentSlug === 'basic' ? 'selected' : '' }}>Basic Plan
                                            - ₹49/mo</option>
                                        <option value="pro" {{ $currentSlug === 'pro' ? 'selected' : '' }}>Pro Plan - ₹149/mo
                                        </option>
                                        <option value="enterprise" {{ $currentSlug === 'enterprise' ? 'selected' : '' }}>
                                            Enterprise Plan - ₹499/mo</option>
                                    </select>
                                </div>


                                <div class="bg-blue-900/20 text-blue-200 text-xs p-3 rounded border border-blue-900/50">
                                    <p><strong>Note:</strong> Changing the plan will automatically update the available features
                                        for this hotel.</p>
                                </div>

                                <button type="submit"
                                    class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-4 rounded-lg transition shadow-lg shadow-indigo-900/20">
                                    Update Subscription
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Notification Center -->
        <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden shadow-lg h-fit mt-8" x-data='{
                                    title: "",
                                    message: "",
                                    type: "info",
                                    template: "",
                                    templates: @json($templates->keyBy('name')),
                                    applyTemplate() {
                                        if(this.template && this.templates[this.template]) {
                                            this.title = this.templates[this.template].title;
                                            this.message = this.templates[this.template].message;
                                            this.type = this.templates[this.template].type.trim();
                                        }
                                    }
                                }">
                            <div class="p-6 border-b border-gray-700 bg-gray-800/50 flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-white">Staff Communication</h3>
                                    <p class="text-sm text-gray-500">Send urgent announcements directly to the Admin Panel.</p>
                                </div>
                            </div>

                            <div class="p-6">
                                <form method="POST" action="{{ route('titanium.notifications.store') }}">
                                    @csrf
                                    <div class="space-y-4">

                                        <!-- Template Selector -->
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Quick
                                                Templates</label>
                                            <select x-model="template" @change="applyTemplate()"
                                                class="w-full bg-gray-900 border-gray-600 rounded-lg text-gray-300 text-sm focus:ring-blue-500 focus:border-blue-500">
                                                <option value="">Select a template...</option>
                                                @foreach($templates as $tpl)
                                                    <option value="{{ $tpl->name }}">{{ $tpl->title }} ({{ ucfirst($tpl->type) }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Title</label>
                                                <input type="text" name="title" x-model="title" placeholder="e.g. System Maintenance"
                                                    class="w-full bg-gray-900 border-gray-600 rounded-lg text-white placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500">
                                            </div>
                                            <div>
                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Priority Level</label>
                                            <div class="relative">
                                                <select name="type" x-model="type"
                                                    class="w-full bg-gray-900 border-gray-600 rounded-lg text-white appearance-none focus:ring-blue-500 focus:border-blue-500 pl-11">
                                                    <option value="info">Info (Blue)</option>
                                                    <option value="warning">Warning (Yellow)</option>
                                                    <option value="urgent">Urgent (Red)</option>
                                                </select>
                                                <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                                    <div x-show="type === ' info'"
            class="w-3 h-3 bg-blue-500 rounded-full shadow-[0_0_8px_rgba(59,130,246,0.5)]"></div>
        <div x-show="type === 'warning'" class="w-3 h-3 bg-amber-500 rounded-full shadow-[0_0_8px_rgba(245,158,11,0.5)]">
        </div>
        <div x-show="type === 'urgent'"
            class="w-3 h-3 bg-rose-500 rounded-full animate-pulse shadow-[0_0_8px_rgba(244,63,94,0.5)]"></div>
    </div>
    </div>
    </div>
    </div>

    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-2">Message</label>
        <textarea name="message" x-model="message" rows="3" placeholder="Write your message to the staff..."
            class="w-full bg-gray-900 border-gray-600 rounded-lg text-white placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
    </div>

    <div class="flex justify-end pt-2">
        <button type="submit"
            class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-6 rounded-lg transition shadow-lg shadow-blue-900/20 flex items-center gap-2">
            <span>Send Notification</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </div>
    </div>
    </form>
    </div>
    </div>
    </div>
@endsection