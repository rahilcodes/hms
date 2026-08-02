@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 pb-20">
        {{-- HEADER --}}
        <div class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
            <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <a href="{{ route('guest.dashboard') }}"
                        class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-sm font-black text-slate-900 tracking-tight">Enhance Your Stay</h2>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Available Add-ons</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-0.5">Live Balance</p>
                    <p class="text-lg font-black text-slate-900 leading-none">₹{{ number_format($booking->total_bill) }}</p>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-6 mt-12">
            <div class="mb-10 text-center">
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-4">Premium Experiences</h1>
                <p class="text-slate-500 font-medium max-w-md mx-auto">Make your stay even more memorable with our curated
                    selection of extras and services.</p>
            </div>

            @if(session('success'))
                <div
                    class="mb-8 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-700 text-sm font-bold flex items-center gap-3 animate-bounce-short">
                    <div
                        class="w-8 h-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center shadow-lg shadow-emerald-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    <div
                        class="bg-white rounded-[2.5rem] border border-slate-200 shadow-sm overflow-hidden flex flex-col group hover:shadow-xl hover:border-blue-500 transition-all duration-300">
                        {{-- Service Content --}}
                        <div class="p-8 flex-1">
                            <div
                                class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 transition duration-300">
                                @if($service->icon_class)
                                    <i class="{{ $service->icon_class }} text-xl"></i>
                                @else
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z" />
                                    </svg>
                                @endif
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-2 truncate">{{ $service->name }}</h3>
                            <p class="text-slate-500 text-xs font-medium leading-relaxed mb-6 line-clamp-2">
                                {{ $service->description ?? 'Experience luxury with this exclusive service tailored for your comfort.' }}
                            </p>

                            <div class="flex items-baseline gap-1">
                                <span class="text-2xl font-black text-slate-900">₹{{ number_format($service->price) }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">/
                                    {{ str_replace('per_', '', $service->price_unit) }}</span>
                            </div>
                        </div>

                        {{-- Action Button --}}
                        <form action="{{ route('guest.book-addon') }}" method="POST"
                            class="p-8 bg-slate-50 border-t border-slate-100 flex items-center gap-3">
                            @csrf
                            <input type="hidden" name="service_id" value="{{ $service->id }}">
                            <input type="number" name="qty" value="1" min="1"
                                class="w-16 bg-white border border-slate-200 rounded-xl px-2 py-3 text-center text-xs font-black focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            <button type="submit"
                                class="flex-1 bg-slate-900 text-white rounded-xl py-3 text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:shadow-lg hover:shadow-blue-500/20 transition-all active:scale-95 duration-200">
                                Book Now
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- INVOICE CONTEXT --}}
            <div class="mt-16 bg-slate-900 rounded-[2.5rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div
                    class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8 text-center md:text-left">
                    <div>
                        <h4 class="text-2xl font-black mb-2 tracking-tighter">Your Active Stay Add-ons</h4>
                        <p class="text-slate-400 text-sm font-medium">Any new bookings will be directly added to your room
                            bill.</p>
                    </div>
                    <div class="px-8 py-4 bg-white/10 rounded-2xl border border-white/10">
                        <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Items in History</p>
                        <p class="text-2xl font-black leading-none">{{ count($booking->services_json ?? []) }} <span
                                class="text-xs text-slate-500">Service(s)</span></p>
                    </div>
                </div>
                <div
                    class="absolute -bottom-10 -right-10 w-40 h-40 bg-blue-600 rounded-full blur-3xl opacity-20 animate-pulse">
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes bounce-short {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        .animate-bounce-short {
            animation: bounce-short 1s ease-in-out;
        }
    </style>
@endsection