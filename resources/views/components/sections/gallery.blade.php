@props(['data'])

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-32">
    @if(!empty($data['title']))
        <h2 class="text-3xl font-bold text-center mb-12 text-slate-900">{{ $data['title'] }}</h2>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        @foreach($data['images'] as $image)
            <div class="relative group overflow-hidden rounded-2xl aspect-square bg-gray-100">
                <img src="{{ $image }}"
                    class="object-cover w-full h-full transform group-hover:scale-110 transition duration-700">
                <div class="absolute inset-0 bg-black/20 group-hover:bg-black/0 transition duration-500"></div>
            </div>
        @endforeach
    </div>
</div>