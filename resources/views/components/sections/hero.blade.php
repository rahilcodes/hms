@props(['data'])

<div class="relative bg-slate-900 px-6 pt-32 pb-20 sm:px-12 lg:px-16 mb-20">
    <div class="absolute inset-0 overflow-hidden">
        @if(!empty($data['image']))
            <img src="{{ $data['image'] }}" class="h-full w-full object-cover opacity-30">
        @else
            <div class="h-full w-full bg-slate-900"></div>
            {{-- Default pattern or gradient --}}
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 opacity-90"></div>
        @endif
    </div>

    <div class="relative max-w-5xl mx-auto text-center">
        @if(!empty($data['badge']))
            <span
                class="inline-block py-1 px-3 rounded-full bg-blue-600/20 border border-blue-500/30 text-blue-400 text-xs font-bold uppercase tracking-wider mb-6">
                {{ $data['badge'] }}
            </span>
        @endif

        <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight mb-6">
            {{ $data['heading'] }}
        </h1>

        @if(!empty($data['subheading']))
            <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-light leading-relaxed">
                {{ $data['subheading'] }}
            </p>
        @endif
    </div>
</div>