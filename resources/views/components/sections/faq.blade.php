@props(['data'])

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mb-32">
    @if(!empty($data['title']))
        <h2 class="text-3xl font-bold text-center mb-12 text-slate-900">{{ $data['title'] }}</h2>
    @endif

    <div class="space-y-4">
        @foreach($data['items'] as $item)
            <div x-data="{ open: false }" class="border border-gray-200 rounded-xl bg-white overflow-hidden">
                <button @click="open = !open"
                    class="w-full px-6 py-4 text-left flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition">
                    <span class="font-bold text-slate-800">{{ $item['question'] }}</span>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="{'rotate-180': open}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="px-6 py-4 text-slate-600 text-sm leading-relaxed border-t border-gray-100">
                    {{ $item['answer'] }}
                </div>
            </div>
        @endforeach
    </div>
</div>