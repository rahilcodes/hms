@props(['data'])

<div class="mb-0">
    <div class="bg-slate-100 h-96 w-full flex items-center justify-center text-slate-400 font-medium">
        {{-- Ideally an iframe or map component --}}
        <div class="text-center">
            <svg class="w-12 h-12 mx-auto mb-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <p class="font-bold text-slate-900">{{ $data['title'] ?? 'Our Location' }}</p>
            <p class="text-sm">{{ $data['address'] ?? '' }}</p>
        </div>
    </div>
</div>