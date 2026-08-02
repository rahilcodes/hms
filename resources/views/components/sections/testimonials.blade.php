@props(['data'])

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-32">
    @if(!empty($data['title']))
        <h2 class="text-3xl font-bold text-center mb-12 text-slate-900">{{ $data['title'] }}</h2>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @foreach($data['items'] as $item)
            <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm relative">
                <div class="text-blue-200 absolute top-6 left-6 opacity-30">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H15.017C14.4647 8 14.017 8.44772 14.017 9V11C14.017 11.5523 13.5693 12 13.017 12H12.017V5H22.017V15C22.017 18.3137 19.3307 21 16.017 21H14.017ZM5.0166 21L5.0166 18C5.0166 16.8954 5.91203 16 7.0166 16H10.0166C10.5689 16 11.0166 15.5523 11.0166 15V9C11.0166 8.44772 10.5689 8 10.0166 8H6.0166C5.46432 8 5.0166 8.44772 5.0166 9V11C5.0166 11.5523 4.56889 12 4.0166 12H3.0166V5H13.0166V15C13.0166 18.3137 10.3303 21 7.0166 21H5.0166Z">
                        </path>
                    </svg>
                </div>
                <p class="text-slate-600 italic text-sm mb-6 relative z-10 leading-relaxed">"{{ $item['text'] }}"</p>
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-slate-200 rounded-full flex items-center justify-center font-bold text-slate-500 text-xs">
                        {{ substr($item['name'], 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-sm text-slate-900">{{ $item['name'] }}</p>
                        <p class="text-xs text-slate-400">{{ $item['location'] ?? 'Guest' }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>