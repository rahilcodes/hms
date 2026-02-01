@extends('titanium.layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto">
        <!-- Header Ribbon -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
            <div class="flex items-center gap-6">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[2rem] flex items-center justify-center text-white shadow-2xl shadow-blue-500/20">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2-2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                        </path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-black text-white tracking-tighter">Owner Dashboard</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em]">Live Property
                            Control</span>
                        <div class="w-1 h-1 rounded-full bg-blue-500/50"></div>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Managing:
                            {{ $hotel->name ?? 'System Hotel' }}</span>
                    </div>
                </div>
            </div>

            @if($hotel)
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('titanium.hotels.impersonate', $hotel) }}">
                        @csrf
                        <button
                            class="flex items-center gap-2 px-6 py-3 bg-gray-800 hover:bg-gray-700 border border-gray-700 rounded-2xl text-xs font-black uppercase tracking-[0.1em] text-white transition shadow-xl">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            <span>Access Admin Panel</span>
                        </button>
                    </form>
                    <a href="{{ route('titanium.hotels.show', $hotel) }}"
                        class="flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-500 rounded-2xl text-xs font-black uppercase tracking-[0.1em] text-white transition shadow-2xl shadow-blue-600/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z">
                            </path>
                        </svg>
                        <span>Manage Features</span>
                    </a>
                </div>
            @endif
        </div>

        @if(session('success'))
            <div
                class="mb-8 bg-emerald-500 border border-emerald-400 p-4 rounded-3xl flex items-center gap-4 animate-fade-in-up shadow-xl shadow-emerald-500/20">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-white shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="text-sm font-black text-white uppercase tracking-widest">Success Confirmation</h4>
                    <p class="text-emerald-50 text-xs font-medium">{{ session('success') }}</p>
                </div>
                <button @click="$el.parentElement.remove()" class="ml-auto text-white/50 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT COLUMN: Status & Billing -->
            <div class="space-y-8">
                <div class="bg-gray-800 border border-gray-700 rounded-[2.5rem] p-8 relative overflow-hidden group">
                    <div
                        class="absolute top-0 right-0 w-48 h-48 bg-blue-500/5 rounded-full blur-[80px] -mr-24 -mt-24 group-hover:bg-blue-500/10 transition duration-700">
                    </div>

                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Service Tier</h3>
                        @if($hotel && $hotel->subscription && $hotel->subscription->status === 'active')
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-emerald-500/10 border border-emerald-500/30 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] font-black text-emerald-400 uppercase tracking-widest">Active</span>
                            </div>
                        @else
                            <div
                                class="flex items-center gap-2 px-3 py-1 bg-rose-500/10 border border-rose-500/30 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                <span class="text-[10px] font-black text-rose-400 uppercase tracking-widest">Inactive</span>
                            </div>
                        @endif
                    </div>

                    @if($hotel && $hotel->subscription && $hotel->subscription->status === 'active')
                        <div class="mb-8">
                            <div class="text-xs text-gray-500 font-bold uppercase tracking-widest mb-1">
                                {{ $hotel->subscription->plan_name }}</div>
                            <div class="flex items-baseline gap-1">
                                <span
                                    class="text-4xl font-black text-white tracking-tighter">${{ number_format($hotel->subscription->price, 2) }}</span>
                                <span class="text-sm text-gray-500 font-medium">/mo</span>
                            </div>
                        </div>

                        <div class="space-y-4 mb-8">
                            <div
                                class="flex items-center justify-between p-3 bg-gray-900/40 rounded-2xl border border-gray-700/50">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Renewal</span>
                                <span
                                    class="text-xs font-bold text-white">{{ $hotel->subscription->next_billing_date->format('M d, Y') }}</span>
                            </div>
                            <div
                                class="flex items-center justify-between p-3 bg-gray-900/40 rounded-2xl border border-gray-700/50">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Card On File</span>
                                <span class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M2 10h20v10a2 2 0 01-2 2H4a2 2 0 01-2-2V10zM2 4a2 2 0 012-2h16a2 2 0 012 2v4H2V4z" />
                                    </svg>
                                    7112
                                </span>
                            </div>
                        </div>

                        <form action="{{ route('titanium.subscription.toggle') }}" method="POST" x-data="{ loading: false }">
                            @csrf
                            <button type="submit" @click="loading = true" :disabled="loading"
                                class="w-full bg-gray-700 hover:bg-rose-600 text-white font-black py-4 rounded-2xl text-[10px] uppercase tracking-widest transition-all duration-300 disabled:opacity-50 flex items-center justify-center gap-2">
                                <svg x-show="loading" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="loading ? 'Processing...' : 'Deactivate Plan'"></span>
                            </button>
                        </form>
                    @else
                        <div class="py-6 text-center">
                            <div
                                class="w-16 h-16 bg-gray-900 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-700">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">No Active Subscription</p>
                        </div>

                        <form action="{{ route('titanium.subscription.toggle') }}" method="POST" x-data="{ loading: false }">
                            @csrf
                            <button type="submit" @click="loading = true" :disabled="loading"
                                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-2xl text-[10px] uppercase tracking-widest transition shadow-xl shadow-blue-500/20 disabled:opacity-50 flex items-center justify-center gap-2">
                                <svg x-show="loading" class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="loading ? 'Provisioning...' : 'Activate Basic Plan'"></span>
                            </button>
                        </form>
                    @endif
                </div>

                <!-- System Health -->
                <div class="bg-gray-800 border border-gray-700 rounded-3xl p-8">
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-4">System Logistics</h3>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <span class="text-sm font-bold text-gray-300 tracking-tight">All Operations Normal</span>
                    </div>
                    <p class="text-[10px] text-gray-600 font-medium">Last system handshake: {{ now()->format('H:i A') }}</p>
                </div>
            </div>

            <!-- RIGHT COLUMN: Communication Center -->
            <div class="lg:col-span-2">
                <div class="bg-gray-800 border-2 border-gray-700/50 rounded-[3rem] p-10 shadow-2xl shadow-black/50 overflow-hidden relative" x-data='{
                                            title: "",
                                            message: "",
                                            type: "info",
                                            template: "",
                                            sending: false,
                                            templates: @json($templates->keyBy("name")),
                                            applyTemplate() {
                                                if(this.template && this.templates[this.template]) {
                                                    this.title = this.templates[this.template].title;
                                                    this.message = this.templates[this.template].message;
                                                    this.type = this.templates[this.template].type.trim();
                                                }
                                            }
                                        }'>
                        <div class="absolute top-0 right-0 w-96 h-96 bg-blue-500/5 rounded-full blur-[100px] -mr-48 -mt-48 pointer-events-none"></div>

                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10 relative z-10">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-600/20">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-white tracking-tight">Staff Nexus</h3>
                                    <p class="text-gray-500 text-xs font-bold uppercase tracking-widest mt-0.5">Global Administrative Dispatch</p>
                                </div>
                            </div>

                            <div class="bg-gray-900/50 p-1.5 rounded-2xl border border-gray-700 w-full md:w-auto">
                                 <select x-model="template" @change="applyTemplate()"
                                        class="w-full md:w-48 bg-transparent border-none text-xs font-black uppercase tracking-widest text-blue-400 focus:ring-0 cursor-pointer">
                                        <option value="" class="bg-gray-900 text-gray-500 italic">Select Template</option>
                                        @foreach($templates as $tpl)
                                            <option value="{{ $tpl->name }}" class="bg-gray-900 text-white">{{ $tpl->title }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>

                        <form action="{{ route('titanium.notifications.store') }}" method="POST" @submit="sending = true" class="relative z-10">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                                 <div class="md:col-span-2">
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Subject Header</label>
                                    <input type="text" name="title" required x-model="title"
                                        class="w-full bg-gray-950 border-gray-800 rounded-2xl text-white placeholder-gray-700 focus:ring-blue-600/50 focus:border-blue-600 py-4 transition shadow-inner"
                                        placeholder="Enter concise subject line...">
                                 </div>
                                 <div>
                                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Dispatch Priority</label>
                                    <div class="relative">
                                        <select name="type" x-model="type"
                                            class="w-full bg-gray-950 border-gray-800 rounded-2xl text-white focus:ring-blue-600/50 focus:border-blue-600 py-4 transition appearance-none pl-12 font-bold text-sm">
                                            <option value="info">Info</option>
                                            <option value="warning">Notice</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <div x-show="type === 'info'" class="w-4 h-4 rounded-full bg-blue-600 shadow-[0_0_12px_rgba(37,99,235,0.4)]"></div>
                                            <div x-show="type === 'warning'" class="w-4 h-4 rounded-full bg-amber-500 shadow-[0_0_12px_rgba(245,158,11,0.4)]"></div>
                                            <div x-show="type === 'urgent'" class="w-4 h-4 rounded-full bg-rose-600 animate-pulse shadow-[0_0_12px_rgba(225,29,72,0.4)]"></div>
                                        </div>
                                    </div>
                                 </div>
                            </div>

                            <div class="mb-8">
                                <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-3 ml-1">Dispatch Content</label>
                                <textarea name="message" rows="5" required x-model="message"
                                    class="w-full bg-gray-950 border-gray-800 rounded-3xl text-white placeholder-gray-700 focus:ring-blue-600/50 focus:border-blue-600 p-6 transition resize-none shadow-inner leading-relaxed"
                                    placeholder="Write the authoritative message to be displayed in all staff dashboards..."></textarea>
                            </div>

                            <button type="submit" :disabled="sending || !message"
                                class="w-full bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:grayscale text-white font-black py-5 rounded-[2rem] shadow-2xl shadow-blue-900/40 transition-all duration-500 flex items-center justify-center gap-4 group">
                                <span x-text="sending ? 'DISPATCHING...' : 'BROADCAST TO STAFF'"></span>
                                <svg x-show="!sending" class="w-5 h-5 transform group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <svg x-show="sending" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </button>
                        </form>
                    </div>

                    <!-- Recent Activity Log -->
                    <div class="mt-12 px-2">
                        <div class="flex items-center justify-between mb-6">
                             <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Live Dispatch Feed</h3>
                             <span class="text-[10px] text-gray-600 font-bold">LATEST 5 ENTRIES</span>
                        </div>

                        @if($notifications->count() > 0)
                            <div class="grid grid-cols-1 gap-4">
                                @foreach($notifications as $note)
                                                                                <div class="bg-gray-800/40 backdrop-blur-sm border border-gray-700/50 rounded-3xl p-5 flex items-center justify-between hover:bg-gray-800 transition-colors group">
                                                                                    <div class="flex items-center gap-5">
                                                                                        <div class="w-3 h-3 rounded-full 
                                                                                            {{ $note->type === 'urgent' ? 'bg-rose-500 shadow-[0_0_10px_rgba(225,29,72,0.5)] animate-pulse' :
                                    ($note->type === 'warning' ? 'bg-amber-500 shadow-[0_0_10px_rgba(245,158,11,0.5)]' : 'bg-blue-500 shadow-[0_0_10px_rgba(37,99,235,0.5)]') }}">
                                                                                        </div>
                                                                                        <div>
                                                                                            <p class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">{{ $note->title }}</p>
                                                                                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">{{ $note->created_at->diffForHumans() }}</p>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="px-3 py-1 rounded-full border border-gray-700 bg-gray-950/50">
                                                                                         <span class="text-[9px] font-black tracking-[0.2em] {{ $note->is_read ? 'text-emerald-500' : 'text-gray-500' }}">
                                                                                            {{ $note->is_read ? 'ACKNOWLEDGED' : 'PENDING' }}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-gray-800/20 border border-gray-800 border-dashed rounded-[2rem] p-12 text-center">
                                <svg class="w-12 h-12 text-gray-800 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                <p class="text-gray-600 font-bold text-xs uppercase tracking-widest">No active dispatches found</p>
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </div>
@endsection