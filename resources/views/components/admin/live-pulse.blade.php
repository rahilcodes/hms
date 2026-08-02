<div x-data="{
        events: [],
        loading: true,

        init() {
            this.fetchPulse();
            setInterval(() => {
                if(document.visibilityState === 'visible') this.fetchPulse()
            }, 15000); // Poll every 15s
        },

        fetchPulse() {
            fetch('{{ route('admin.pulse') }}')
                .then(res => res.json())
                .then(data => {
                    this.events = data;
                    this.loading = false;
                });
        }
    }" class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 relative h-full flex flex-col">
    <div class="flex items-center justify-between mb-6 flex-shrink-0">
        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
            <span class="relative flex h-2 w-2">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            Live Pulse
        </h3>
        <span class="text-[10px] font-bold text-slate-300">Real-time</span>
    </div>

    <div class="space-y-4 relative flex-1 overflow-y-auto pr-2 custom-scrollbar">
        <!-- Skeleton Loader -->
        <template x-if="loading">
            <div class="space-y-4 animate-pulse">
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-slate-100 rounded-full"></div>
                    <div class="flex-1 space-y-2 py-1">
                        <div class="h-2 bg-slate-100 rounded w-3/4"></div>
                        <div class="h-2 bg-slate-100 rounded w-1/2"></div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-10 h-10 bg-slate-100 rounded-full"></div>
                    <div class="flex-1 space-y-2 py-1">
                        <div class="h-2 bg-slate-100 rounded w-3/4"></div>
                        <div class="h-2 bg-slate-100 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Events List -->
        <template x-for="event in events" :key="event.raw_date + event.title">
            <div class="flex gap-4 items-start group animate-in slide-in-from-right-4 fade-in duration-500">
                <!-- Icon Bubble -->
                <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center border transition"
                    :class="{
                        'bg-blue-50 border-blue-100 text-blue-600': event.color === 'blue',
                        'bg-emerald-50 border-emerald-100 text-emerald-600': event.color === 'emerald',
                        'bg-slate-50 border-slate-100 text-slate-500': event.color === 'slate',
                        'bg-purple-50 border-purple-100 text-purple-600': event.color === 'purple'
                    }">

                    <template x-if="event.icon === 'sparkles'"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 3.214L13 21l-2.286-6.857L5 12l5.714-3.214z">
                            </path>
                        </svg></template>
                    <template x-if="event.icon === 'currency-rupee'"><svg class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 8h6m-5 0a3 3 0 110 6H9l3 3m-3-6h6m6 1a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg></template>
                    <template x-if="event.icon === 'clipboard-list'"><svg class="w-5 h-5" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                            </path>
                        </svg></template>

                </div>

                <!-- Text -->
                <div class="flex-1 pt-0.5">
                    <div class="flex justify-between items-center mb-0.5">
                        <p class="text-sm font-bold text-slate-800" x-text="event.title"></p>
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded-full"
                            x-text="event.timestamp"></span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed" x-text="event.description"></p>
                </div>
            </div>
        </template>

        <!-- Empty State -->
        <template x-if="!loading && events.length === 0">
            <div class="text-center py-8 text-slate-400">
                <p class="text-xs italic">No recent activity</p>
            </div>
        </template>
    </div>
</div>