@extends('layouts.admin')

@section('header_title', 'Content Management')

@section('content')

    <div class="flex min-h-screen">
        <aside class="w-64 bg-gray-800 text-white p-4">
            <h2 class="font-semibold mb-4">Website Content</h2>
            <ul class="space-y-2 text-sm">
                <li>Global Settings</li>
                <li>Homepage</li>
                <li>Policies</li>
            </ul>
        </aside>

        <main class="flex-1 p-8">
            <h1 class="text-2xl font-semibold">
                Select content section
            </h1>
        </main>
    </div>

@endsection