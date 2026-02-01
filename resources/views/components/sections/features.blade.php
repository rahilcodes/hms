@props(['data'])

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-32">
    @if(!empty($data['title']))
        <h2 class="text-3xl font-bold text-center mb-12 text-slate-900">{{ $data['title'] }}</h2>
    @endif

    <div class="grid md:grid-cols-3 gap-8">
        @foreach($data['items'] as $item)
            <div
                class="p-8 rounded-2xl bg-white border border-gray-100 shadow-sm hover:shadow-md transition text-center group">
                <div
                    class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                    <svg class="w-6 h-6 text-gray-400 group-hover:text-white transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2 text-slate-900">{{ $item['title'] }}</h3>
                <p class="text-slate-500 text-sm leading-relaxed">{{ $item['desc'] }}</p>
            </div>
        @endforeach
    </div>
</div>