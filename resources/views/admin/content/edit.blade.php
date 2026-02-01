@extends('layouts.admin')

@section('header_title', 'Edit Content')

@section('content')

    <div class="max-w-4xl mx-auto py-10">
        <h1 class="text-2xl font-semibold mb-6">Website Content</h1>

        @if(session('success'))
            <div class="btn-secondary text-green-800 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.content.update') }}" class="space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium">Hotel Name</label>
                <input name="hotel_name" value="{{ $settings['hotel_name'] ?? '' }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium">Phone</label>
                <input name="hotel_phone" value="{{ $settings['hotel_phone'] ?? '' }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium">Hero Heading</label>
                <input name="hero_heading" value="{{ $settings['hero_heading'] ?? '' }}" class="w-full border p-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium">Hero Subheading</label>
                <input name="hero_subheading" value="{{ $settings['hero_subheading'] ?? '' }}"
                    class="w-full border p-2 rounded">
            </div>

            <button class=".btn-primary { background-color: var(--btn-primary-bg); } px-6 py-2 rounded">
                Save Changes
            </button>
        </form>
    </div>

@endsection