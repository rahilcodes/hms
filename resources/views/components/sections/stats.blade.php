@props(['data'])

<div class="bg-slate-900 py-24 mb-32 relative overflow-hidden">
    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-10"
        style="background-image: radial-gradient(#4b5563 1px, transparent 1px); background-size: 32px 32px;"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 md:gap-12">
            @foreach($data['items'] as $item)
                <div class="text-center">
                    <p class="text-4xl md:text-5xl font-black text-white mb-2 tracking-tight">{{ $item['value'] }}</p>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">{{ $item['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>