<div x-data="{
        open: false,
        query: '',
        results: [],
        selectedIndex: 0,
        loading: false,

        init() {
            window.addEventListener('keydown', (e) => {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    this.open = !this.open;
                    if(this.open) this.$nextTick(() => this.$refs.searchInput.focus());
                }
                if (e.key === 'Escape') this.open = false;
            });

            this.$watch('query', (value) => {
                if (value.length < 2) {
                    this.results = [];
                    return;
                }
                this.loading = true;
                
                // Debounce
                clearTimeout(this.debounce);
                this.debounce = setTimeout(() => {
                    fetch('/admin/search?query=' + encodeURIComponent(value))
                        .then(res => res.json())
                        .then(data => {
                            this.results = data;
                            this.selectedIndex = 0;
                            this.loading = false;
                        });
                }, 300);
            });
        },

        onKeydown(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                this.selectedIndex = (this.selectedIndex + 1) % this.results.length;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.selectedIndex = (this.selectedIndex - 1 + this.results.length) % this.results.length;
            } else if (e.key === 'Enter') {
                if (this.results[this.selectedIndex]) {
                    window.location.href = this.results[this.selectedIndex].url;
                }
            }
        }
    }" class="relative z-50">
    <!-- Trigger Button (Mobile/Desktop) -->
    <div @click="open = true"
        class="hidden md:flex items-center gap-3 bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg cursor-pointer hover:bg-white hover:border-gray-300 transition text-gray-500 w-64 shadow-inner">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <span class="text-sm font-medium flex-1">Search...</span>
        <div class="flex items-center gap-1">
            <kbd
                class="hidden sm:inline-block px-1.5 py-0.5 text-[10px] font-bold text-gray-400 bg-gray-100 border border-gray-300 rounded-md shadow-sm">⌘K</kbd>
        </div>
    </div>

    <!-- Modal Backdrop -->
    <div x-show="open" x-transition.opacity.duration.200ms class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"
        @click="open = false" x-cloak>
    </div>

    <!-- Modal Content -->
    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
        @click.away="open = false"
        class="fixed top-[15%] left-1/2 -translate-x-1/2 w-full max-w-2xl bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200 flex flex-col max-h-[60vh]"
        x-cloak>

        <!-- Search Input -->
        <div class="flex items-center px-6 py-4 border-b border-slate-100 bg-white">
            <svg class="w-6 h-6 text-slate-400 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input x-ref="searchInput" x-model="query" @keydown="onKeydown" type="text"
                placeholder="Where to? (e.g. 'John', 'Settings', 'New Booking')..."
                class="w-full text-lg outline-none text-slate-900 placeholder:text-slate-300 font-medium bg-transparent h-10">
            <button @click="open = false"
                class="text-xs font-bold bg-slate-100 text-slate-500 px-2 py-1 rounded-md uppercase tracking-wide hover:bg-slate-200">Esc</button>
        </div>

        <!-- Results List -->
        <div class="overflow-y-auto flex-1 p-2 bg-slate-50" x-show="results.length > 0">
            <template x-for="(result, index) in results" :key="index">
                <a :href="result.url" class="flex items-center gap-4 px-4 py-3 rounded-xl transition group duration-150"
                    :class="selectedIndex === index ? 'bg-blue-600 shadow-md transform scale-[1.01]' : 'hover:bg-white hover:shadow-sm'"
                    @mousemove="selectedIndex = index">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center transition"
                        :class="selectedIndex === index ? 'bg-white/20 text-white' : 'bg-white text-slate-400 border border-slate-100'">
                        <!-- Dynamic Icons based on type if needed, or generic -->
                        <template x-if="result.icon === 'calendar'"><svg class="w-5 h-5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg></template>
                        <template x-if="result.icon === 'user'"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg></template>
                        <template x-if="result.icon === 'home'"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                                </path>
                            </svg></template>
                        <template x-if="result.icon === 'arrow-right-circle'"><svg class="w-5 h-5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg></template>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-bold"
                                :class="selectedIndex === index ? 'text-white' : 'text-slate-900'"
                                x-text="result.title"></h4>
                            <span class="text-[10px] uppercase tracking-widest font-bold opacity-70"
                                :class="selectedIndex === index ? 'text-white' : 'text-slate-400'"
                                x-text="result.type"></span>
                        </div>
                        <p class="text-xs mt-0.5" :class="selectedIndex === index ? 'text-blue-100' : 'text-slate-500'"
                            x-text="result.subtitle"></p>
                    </div>

                    <div x-show="selectedIndex === index" class="text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                            </path>
                        </svg>
                    </div>
                </a>
            </template>
        </div>

        <!-- Empty State -->
        <div x-show="query.length > 1 && results.length === 0 && !loading" class="p-8 text-center text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-sm font-medium">No results found for "<span x-text="query"></span>"</p>
        </div>

        <!-- Initial State / Footer -->
        <div x-show="results.length === 0 && query.length < 2" class="p-4 bg-slate-50 border-t border-slate-100">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Quick Commands</p>
            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('admin.bookings.create') }}"
                    class="flex items-center gap-2 p-2 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 transition group">
                    <div class="w-6 h-6 rounded bg-emerald-100 text-emerald-600 flex items-center justify-center"><svg
                            class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg></div>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900">New Booking</span>
                </a>
                <a href="{{ route('admin.guests.index') }}"
                    class="flex items-center gap-2 p-2 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 transition group">
                    <div class="w-6 h-6 rounded bg-purple-100 text-purple-600 flex items-center justify-center"><svg
                            class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg></div>
                    <span class="text-xs font-bold text-slate-600 group-hover:text-slate-900">Guest List</span>
                </a>
            </div>
        </div>
    </div>
</div>