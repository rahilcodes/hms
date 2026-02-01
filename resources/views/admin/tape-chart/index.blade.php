@extends('layouts.admin')

@section('header_title')
    <div class="flex items-center gap-3">
        <div class="p-2 bg-blue-600 rounded-lg text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <h1 class="text-lg font-bold text-slate-900 leading-tight">Tape Chart</h1>
            <p class="text-xs text-slate-500 font-medium">Interactive Room Rack</p>
        </div>
    </div>
@endsection

@section('content')

    <div class="h-[calc(100vh-140px)] flex flex-col bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm"
        x-data="tapeChart()">

        {{-- TOOLBAR --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 bg-white z-20">
            <div class="flex items-center gap-4">
                {{-- Date Navigation --}}
                <div class="flex items-center gap-2 bg-slate-50 rounded-xl p-1 border border-slate-100">
                    <a href="{{ route('admin.tape-chart.index', ['start_date' => $startDate->copy()->subDays($days)->format('Y-m-d')]) }}"
                        class="p-2 hover:bg-white hover:shadow-sm rounded-lg text-slate-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>
                    <span class="text-xs font-bold text-slate-700 px-2 min-w-[100px] text-center">
                        {{ $startDate->format('d M') }} - {{ $endDate->format('d M, Y') }}
                    </span>
                    <a href="{{ route('admin.tape-chart.index', ['start_date' => $startDate->copy()->addDays($days)->format('Y-m-d')]) }}"
                        class="p-2 hover:bg-white hover:shadow-sm rounded-lg text-slate-500 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                <a href="{{ route('admin.tape-chart.index', ['start_date' => now()->format('Y-m-d')]) }}"
                    class="px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition">
                    Today
                </a>
            </div>

            <div class="flex items-center gap-2">
                {{-- Legend --}}
                <div class="flex items-center gap-3 text-[10px] font-bold uppercase tracking-wide text-slate-400 mr-4">
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>Upcoming
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-blue-600"></div>In-House
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-slate-400"></div>Checked Out
                    </div>
                    <div class="flex items-center gap-1.5">
                        <div class="w-2 h-2 rounded-full bg-rose-500"></div>Maintenance
                    </div>
                </div>
            </div>
        </div>

        {{-- CHART CONTAINER --}}
        <div class="flex-1 overflow-auto relative custom-scrollbar bg-slate-50/50" id="tape-chart-scroll">

            {{-- HEADERS (Sticky Top) --}}
            <div class="sticky top-0 z-10 flex min-w-max">
                {{-- Corner --}}
                <div
                    class="sticky left-0 w-48 bg-white border-b border-r border-slate-200 p-4 z-20 shadow-sm flex items-center">
                    <span class="text-xs font-black text-slate-400 uppercase tracking-widest">Rooms</span>
                </div>
                {{-- Date Columns --}}
                @foreach($dates as $date)
                    <div
                        class="w-[100px] border-b border-r border-slate-100 p-3 text-center flex-shrink-0 bg-white
                                            {{ $date['is_today'] ? 'bg-blue-50/30' : '' }} {{ $date['is_weekend'] ? 'bg-slate-50/50' : '' }}">
                        <p
                            class="text-[10px] font-bold uppercase tracking-widest {{ $date['is_today'] ? 'text-blue-600' : 'text-slate-400' }}">
                            {{ $date['day'] }}
                        </p>
                        <p class="text-sm font-black {{ $date['is_today'] ? 'text-blue-700' : 'text-slate-700' }}">
                            {{ $date['day_num'] }}
                        </p>
                    </div>
                @endforeach
            </div>

            {{-- ROOM ROWS --}}
            <div class="min-w-max relative">
                @foreach($roomTypes as $type)
                    {{-- Room Type Header Row --}}
                    <div
                        class="sticky left-0 w-full bg-slate-100 border-b border-slate-200 px-4 py-2 z-0 font-bold text-xs text-slate-500 uppercase tracking-widest">
                        {{ $type->name }}
                    </div>

                    @foreach($type->rooms as $room)
                        <div class="flex h-16 border-b border-slate-100 relative group hover:bg-blue-50/5 transition-colors">
                            {{-- Room Name (Sticky Left) --}}
                            <div
                                class="sticky left-0 w-48 bg-white border-r border-slate-200 p-4 flex items-center justify-between flex-shrink-0 z-10 group-hover:bg-blue-50/10 transition-colors">
                                <span class="font-bold text-sm text-slate-700">{{ $room->room_number }}</span>
                                <span
                                    class="text-[9px] font-bold px-1.5 py-0.5 rounded {{ $room->housekeeping_status === 'clean' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ ucfirst($room->housekeeping_status) }}
                                </span>
                            </div>

                            {{-- Date Cells --}}
                            @foreach($dates as $date)
                                <div
                                    class="w-[100px] border-r border-slate-100 h-full flex-shrink-0 {{ $date['is_weekend'] ? 'bg-slate-50/30' : '' }}">
                                </div>
                            @endforeach

                            {{-- BOOKINGS LAYER (Absolute Positioned) --}}
                            @foreach($bookings as $booking)
                                @php
                                    // Check if booking is assigned to this room
                                    // Logic: check assignedRooms collection
                                    $isAssigned = $booking->assignedRooms->contains('id', $room->id);
                                @endphp

                                @if($isAssigned)
                                    @php
                                        // Calculate Position and Width
                                        $checkIn = \Carbon\Carbon::parse($booking->check_in);
                                        $checkOut = \Carbon\Carbon::parse($booking->check_out);

                                        // Intersect range
                                        $start = $checkIn->max($startDate);
                                        $end = $checkOut->min($endDate->copy()->addDay()); // Visual end is next day start

                                        if ($start < $end) {
                                            $diffDays = $start->diffInDays($startDate); // Days from start of chart
                                            $duration = $start->diffInDays($end); // Visible duration

                                            // Styling
                                            $colorClass = match ($booking->operational_status) {
                                                'checked_in', 'in_house' => 'bg-blue-600 border-blue-700 text-white shadow-blue-200',
                                                'checked_out' => 'bg-slate-400 border-slate-500 text-white opacity-60',
                                                'pending_checkin', 'upcoming' => 'bg-emerald-500 border-emerald-600 text-white shadow-emerald-200',
                                                'cancelled' => 'bg-red-100 border-red-200 text-red-400 hidden',
                                                default => 'bg-slate-500'
                                            };
                                        } else {
                                            continue;
                                        }

                                        // Offset: Room Column Width (192px / 12rem) + (Day Width * DiffDays)
                                        // Note: Tailwind w-48 is 12rem (192px).
                                        // Each day col is 100px.
                                        $leftOffset = 192 + ($diffDays * 100);
                                        $width = $duration * 100;
                                    @endphp

                                    <a href="{{ route('admin.bookings.show', $booking) }}"
                                        class="absolute top-2 bottom-2 rounded-lg border shadow-sm px-3 flex flex-col justify-center overflow-hidden hover:scale-[1.02] hover:z-20 transition-all cursor-pointer {{ $colorClass }}"
                                        style="left: {{ $leftOffset }}px; width: {{ $width - 4 }}px;"
                                        title="#{{ $booking->id + 1000 }} - {{ $booking->guest_name }}">
                                        <div class="font-bold text-[10px] leading-tight truncate">
                                            {{ $booking->guest_name }}
                                        </div>
                                        <div class="text-[9px] opacity-80 truncate">
                                            #{{ $booking->id + 1000 }}
                                        </div>
                                    </a>
                                @endif
                            @endforeach

                            {{-- MAINTENANCE LAYER --}}
                            @foreach($maintenance as $maint)
                                @if($maint->room_id == $room->id)
                                    {{-- We'll assume a dummy duration of 1 day if not specified,
                                    or filter for those created/active in this range.
                                    For now, simplified logic as per Plan. --}}
                                    @php
                                        // Just for demo, assuming maintenance is TODAY if in progress
                                        // In real app, maintenance logs needed explicit start/end dates.
                                        // Skipping deep logic as per plan simplification.
                                    @endphp
                                @endif
                            @endforeach

                        </div>
                    @endforeach
                @endforeach

                {{-- UNASSIGNED (Bottom Section) --}}
                {{-- Future: Add unassigned drag-drop pool --}}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('tapeChart', () => ({
                init() {
                    // Scroll to today if possible?
                    // document.getElementById('tape-chart-scroll').scrollLeft = 0;
                }
            }))
        })
    </script>

@endsection