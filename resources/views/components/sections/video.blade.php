@props(['data'])

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-32">
    @if(!empty($data['title']))
        <h2 class="text-3xl font-bold text-center mb-12 text-slate-900">{{ $data['title'] }}</h2>
    @endif

    <div class="relative rounded-2xl overflow-hidden shadow-2xl bg-black aspect-video md:aspect-[21/9]">
        @if(!empty($data['video']))
            <video class="w-full h-full object-cover" controls poster="{{ $data['poster'] ?? '' }}">
                <source src="{{ $data['video'] }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @else
            <!-- Placeholder Video UI -->
            <div class="absolute inset-0 flex items-center justify-center bg-gray-900 text-white">
                <div class="text-center">
                    <div
                        class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm border border-white/20">
                        <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>
                    </div>
                    <p class="font-bold">See Life at Our Hotel</p>
                </div>
                @if(!empty($data['poster']))
                    <img src="{{ $data['poster'] }}" class="absolute inset-0 w-full h-full object-cover -z-10 opacity-60">
                @endif
            </div>
        @endif
    </div>
</div>