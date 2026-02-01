@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 flex items-center justify-center p-6" x-data="{ step: 1, identity: '' }">
        <div class="w-full max-w-md">
            {{-- GLASS CARD --}}
            <div
                class="bg-white/80 backdrop-blur-xl border border-white rounded-[2.5rem] shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-500">
                <div class="p-10 text-center">
                    {{-- LOGO / ICON --}}
                    <div
                        class="w-20 h-20 bg-blue-600 rounded-3xl mx-auto mb-8 flex items-center justify-center shadow-2xl shadow-blue-500/20 rotate-3">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>

                    <h1 class="text-3xl font-black text-slate-900 tracking-tight mb-2">My Reservation</h1>
                    <p class="text-slate-500 font-medium">Access your stay dashboard without a password.</p>
                </div>

                <form action="{{ route('guest.verify') }}" method="POST" class="p-10 pt-0 space-y-6">
                    @csrf

                    @if($errors->any())
                        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl animate-in shake duration-300">
                            @foreach ($errors->all() as $error)
                                <p class="text-xs font-bold text-rose-600 text-center">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    {{-- STEP 1: IDENTITY --}}
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Email
                                    or Phone</label>
                                <input type="text" name="identity" x-model="identity" required
                                    placeholder="john@example.com or +91..."
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none text-slate-900 placeholder:text-slate-300">
                            </div>
                            <button type="button" @click="if(identity) step = 2"
                                class="w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black text-sm shadow-2xl hover:bg-black transition transform active:scale-95 duration-200">
                                Continue
                            </button>
                        </div>
                    </div>

                    {{-- STEP 2: CHECK-IN DATE --}}
                    <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-4"
                        x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="space-y-4">
                            <button type="button" @click="step = 1"
                                class="text-[10px] font-bold text-blue-600 uppercase tracking-widest flex items-center gap-1 hover:gap-2 transition-all">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Back to Identity
                            </button>
                            <div class="space-y-2">
                                <label
                                    class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Check-in
                                    Date</label>
                                <input type="date" name="check_in" required
                                    class="w-full px-6 py-4 bg-slate-50 border border-slate-100 rounded-2xl text-sm font-bold focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition outline-none text-slate-900">
                            </div>
                            <button type="submit" x-data="{ loading: false }" @click="loading = true" :disabled="loading"
                                class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black text-sm shadow-2xl shadow-blue-500/20 hover:bg-blue-700 transition transform active:scale-95 duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="loading ? 'Authenticating...' : 'Verify & Enter Dashboard'"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- BACK LINK --}}
            <div class="mt-8 text-center space-x-6 flex items-center justify-center">
                <a href="/"
                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-blue-600 transition">
                    &larr; Home
                </a>
                <span class="w-1 h-1 bg-slate-200 rounded-full"></span>
                <a href="{{ route('titanium.login') }}"
                    class="text-[10px] font-black text-slate-500 uppercase tracking-widest hover:text-indigo-600 transition">
                    Staff/Owner Login
                </a>
            </div>
        </div>
    </div>

    <style>
        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        .animate-in.shake {
            animation: shake 0.3s ease-in-out;
        }
    </style>
@endsection