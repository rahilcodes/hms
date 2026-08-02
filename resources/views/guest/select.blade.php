@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-slate-50 py-20 px-6">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-black text-slate-900 tracking-tighter mb-4">Select Your Room</h1>
                <p class="text-slate-500 font-medium">We found multiple reservations under your name for today.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($bookings as $booking)
                    <a href="{{ route('guest.switch', $booking) }}"
                        class="group bg-white rounded-[2.5rem] p-8 border border-slate-200 shadow-sm hover:shadow-xl hover:border-blue-500 transition-all duration-300 relative overflow-hidden">

                        <div class="relative z-10 flex flex-col h-full justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-6">
                                    <div
                                        class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-black">
                                        #{{ $booking->id }}
                                    </div>
                                    <span
                                        class="px-4 py-1.5 bg-slate-100 text-slate-600 rounded-full text-[10px] font-black uppercase tracking-widest group-hover:bg-blue-600 group-hover:text-white transition">
                                        {{ $booking->status }}
                                    </span>
                                </div>

                                <h3 class="text-2xl font-black text-slate-900 mb-2">
                                    @if($booking->assignedRooms->count() > 0)
                                        Room {{ $booking->assignedRooms->first()->room_number }}
                                    @else
                                        {{ $booking->roomType->name }}
                                    @endif
                                </h3>
                                <p class="text-slate-500 text-sm font-medium mb-8">
                                    @if($booking->assignedRooms->count() > 0)
                                        {{ $booking->roomType->name }} • Floor
                                        {{ ceil($booking->assignedRooms->first()->room_number / 100) }}
                                    @else
                                        Allocation Pending
                                    @endif
                                </p>
                            </div>

                            <div class="flex items-center gap-3 text-blue-600 font-bold text-sm">
                                Manage Stay <span class="group-hover:translate-x-1 transition">→</span>
                            </div>
                        </div>

                        {{-- Background Accent --}}
                        <div
                            class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-blue-50 rounded-full opacity-0 group-hover:opacity-100 transition duration-500">
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <form action="{{ route('guest.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="text-slate-400 font-bold text-sm hover:text-slate-600 underline decoration-slate-200 underline-offset-8 transition">
                        Not your bookings? Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection