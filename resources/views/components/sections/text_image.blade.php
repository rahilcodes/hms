@props(['data'])

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-32">
    <div class="grid md:grid-cols-2 gap-16 items-center">
        <div class="{{ ($data['align'] ?? 'left') === 'right' ? 'md:order-2' : '' }}">
            <div class="relative">
                <div class="absolute inset-0 bg-blue-500 blur-[100px] opacity-10 rounded-full"></div>
                @if(!empty($data['image']))
                    <img src="{{ $data['image'] }}" alt="{{ $data['title'] }}"
                        class="relative rounded-2xl shadow-xl border border-white/10 rotate-1 hover:rotate-0 transition duration-700 ease-out">
                @else
                    <div
                        class="relative rounded-2xl shadow-xl border border-gray-100 bg-gray-100 h-96 flex items-center justify-center text-gray-400">
                        No Image Provided
                    </div>
                @endif
            </div>
        </div>
        <div>
            <h2 class="text-3xl font-bold mb-6 text-slate-900">{{ $data['title'] }}</h2>
            <div class="prose prose-slate prose-lg text-slate-500 font-light leading-relaxed">
                {!! $data['content'] !!}
            </div>
        </div>
    </div>
</div>