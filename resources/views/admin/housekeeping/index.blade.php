@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8" x-data="housekeepingMatrix()">
        <!-- Header with Stats -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Housekeeping Matrix</h1>
                <p class="text-slate-500 mt-1">Real-time room status & arrivals tracking.</p>
            </div>

            <div class="flex gap-4">
                <div class="stat-card bg-emerald-50 border-emerald-100 text-emerald-700">
                    <span class="text-xs font-bold uppercase">Clean</span>
                    <span class="text-xl font-black">{{ $stats['clean'] }}</span>
                </div>
                <div class="stat-card bg-rose-50 border-rose-100 text-rose-700">
                    <span class="text-xs font-bold uppercase">Dirty</span>
                    <span class="text-xl font-black">{{ $stats['dirty'] }}</span>
                </div>
                <div class="stat-card bg-blue-50 border-blue-100 text-blue-700">
                    <span class="text-xs font-bold uppercase">Cleaning</span>
                    <span class="text-xl font-black">{{ $stats['cleaning'] }}</span>
                </div>
            </div>
        </div>

        <!-- Desktop Matrix View (Hidden on Mobile) -->
        <div class="hidden md:block bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <!-- Matrix Header -->
            <div
                class="grid grid-cols-8 bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">
                <div class="p-4 text-left">Room Type</div>
                <div class="p-4 bg-sky-50/50 text-sky-600 border-r border-slate-200">Arrivals</div>
                <div class="p-4 bg-emerald-50/50 text-emerald-600">Ready</div>
                <div class="p-4 bg-slate-100 text-slate-600">Occupied</div>
                <div class="p-4 bg-rose-50/50 text-rose-600">Dirty</div>
                <div class="p-4 bg-blue-50/50 text-blue-600">Cleaning</div>
                <div class="p-4 bg-amber-50/50 text-amber-600">Inspect</div>
                <div class="p-4 bg-slate-50 text-slate-400">Total</div>
            </div>

            <!-- Room Types Rows -->
            @foreach($roomTypes as $type)
                @php
                    $rooms = $type->rooms;
                    $ready = $rooms->filter(fn($r) => $r->status === 'available' && $r->housekeeping_status === 'clean');
                    $occupied = $rooms->filter(fn($r) => $r->status === 'occupied');
                    $dirty = $rooms->filter(fn($r) => $r->housekeeping_status === 'dirty');
                    $cleaning = $rooms->filter(fn($r) => $r->housekeeping_status === 'cleaning');
                    $inspect = $rooms->filter(fn($r) => $r->housekeeping_status === 'inspection_ready');
                    $pendingArrivals = $arrivals[$type->id] ?? 0;
                @endphp
                <div class="grid grid-cols-8 border-b border-slate-100 last:border-0 min-h-[100px]">
                    <!-- Room Type Name -->
                    <div class="p-4 flex flex-col justify-center border-r border-slate-100 bg-white">
                        <span class="font-bold text-slate-800">{{ $type->name }}</span>
                        <span class="text-xs text-slate-400 mt-1">{{ $type->rooms->count() }} Rooms</span>
                    </div>

                    <!-- Arrivals Column -->
                    <div class="p-4 border-r border-slate-100 bg-sky-50/10 flex items-center justify-center">
                        @if($pendingArrivals > 0)
                            <div class="flex flex-col items-center animate-pulse">
                                <span class="text-2xl font-black text-sky-600">{{ $pendingArrivals }}</span>
                                <span class="text-[10px] font-bold text-sky-400 uppercase tracking-wide">Pending</span>
                            </div>
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </div>

                    <!-- Ready Column -->
                    <div class="p-3 bg-emerald-50/10 border-r border-slate-100 flex flex-wrap content-start gap-2">
                        @foreach($ready as $room)
                            <button @click="openManageModal({{ $room }})"
                                class="room-pill bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                                {{ $room->room_number }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Occupied Column -->
                    <div class="p-3 bg-slate-100/50 border-r border-slate-100 flex flex-wrap content-start gap-2">
                        @foreach($occupied as $room)
                            @php
                                $hasRequest = $room->bookings->flatMap->guestRequests->where('status', 'pending')->count() > 0;
                            @endphp
                            <button @click="openManageModal({{ $room }})"
                                class="room-pill bg-slate-200 text-slate-600 hover:bg-slate-300 relative">
                                @if($hasRequest)
                                    <span
                                        class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse border border-white"></span>
                                @endif
                                {{ $room->room_number }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Dirty Column -->
                    <div class="p-3 bg-rose-50/10 border-r border-slate-100 flex flex-wrap content-start gap-2">
                        @foreach($dirty as $room)
                            <button @click="openManageModal({{ $room }})"
                                class="room-pill bg-rose-100 text-rose-700 hover:bg-rose-200">
                                {{ $room->room_number }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Cleaning Column -->
                    <div class="p-3 bg-blue-50/10 border-r border-slate-100 flex flex-wrap content-start gap-2">
                        @foreach($cleaning as $room)
                            <button @click="openManageModal({{ $room }})"
                                class="room-pill bg-blue-100 text-blue-700 hover:bg-blue-200">
                                {{ $room->room_number }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Inspect Column -->
                    <div class="p-3 bg-amber-50/10 border-r border-slate-100 flex flex-wrap content-start gap-2">
                        @foreach($inspect as $room)
                            <button @click="openManageModal({{ $room }})"
                                class="room-pill bg-amber-100 text-amber-700 hover:bg-amber-200">
                                {{ $room->room_number }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Total Column -->
                    <div class="p-4 flex items-center justify-center bg-slate-50 text-sm font-bold text-slate-600">
                        {{ $rooms->count() }}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Mobile Card View (Visible on Mobile) -->
        <div class="md:hidden space-y-6">
            @foreach($roomTypes as $type)
                <div class="bg-white rounded-3xl p-5 border border-slate-100 shadow-sm">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-50 pb-3">
                        <h3 class="font-black text-slate-800 text-lg">{{ $type->name }}</h3>
                        <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded">{{ $type->rooms->count() }}
                            Rooms</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        @foreach($type->rooms as $room)
                            @php
                                $colorClass = match ($room->housekeeping_status) {
                                    'clean' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                    'dirty' => 'bg-rose-100 text-rose-700 border-rose-200',
                                    'cleaning' => 'bg-blue-100 text-blue-700 border-blue-200',
                                    'inspection_ready' => 'bg-amber-100 text-amber-700 border-amber-200',
                                    default => 'bg-slate-100 text-slate-600'
                                };

                                // Visual indicator for occupied status
                                $isOccupied = $room->status === 'occupied';

                                // Check for pending requests
                                $hasRequest = $room->bookings->flatMap->guestRequests->where('status', 'pending')->count() > 0;
                            @endphp
                            <button @click="openManageModal({{ $room }})"
                                class="relative flex flex-col items-center justify-center p-3 rounded-2xl border {{ $colorClass }} transition transform active:scale-95 shadow-sm h-20">

                                @if($isOccupied)
                                    <div class="absolute top-1 right-1 w-2 h-2 rounded-full bg-slate-800 ring-2 ring-white"></div>
                                @endif

                                @if($hasRequest)
                                    <div
                                        class="absolute -top-1 -left-1 bg-red-500 text-white rounded-full p-1 border-2 border-white shadow-sm animate-bounce">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                                            </path>
                                        </svg>
                                    </div>
                                @endif

                                <span class="text-lg font-black leading-none mb-1">{{ $room->room_number }}</span>
                                <span class="text-[9px] uppercase font-bold opacity-70 leading-none">
                                    {{ $room->housekeeping_status }}
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Manage Room Modal -->
        <div x-show="showModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true"
            style="display: none;">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div @click.away="showModal = false"
                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">

                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-xl font-bold text-slate-800">Manage Room <span
                                    x-text="currentRoom.room_number"></span></h3>
                            <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Room Edit Form -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Room Number</label>
                                <input type="text" x-model="form.room_number"
                                    class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Housekeeping Status</label>
                                <div class="grid grid-cols-2 gap-3 mt-2">
                                    <button @click="updateStatus('clean')" :class="getClass('clean')"
                                        class="p-4 text-sm font-black rounded-xl border-2 transition flex items-center justify-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div> Clean
                                    </button>
                                    <button @click="updateStatus('dirty')" :class="getClass('dirty')"
                                        class="p-4 text-sm font-black rounded-xl border-2 transition flex items-center justify-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-rose-500"></div> Dirty
                                    </button>
                                    <button @click="updateStatus('cleaning')" :class="getClass('cleaning')"
                                        class="p-4 text-sm font-black rounded-xl border-2 transition flex items-center justify-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-blue-500"></div> Cleaning
                                    </button>
                                    <button @click="updateStatus('inspection_ready')" :class="getClass('inspection_ready')"
                                        class="p-4 text-sm font-black rounded-xl border-2 transition flex items-center justify-center gap-2">
                                        <div class="w-3 h-3 rounded-full bg-amber-500"></div> Inspect
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Occupancy Status</label>
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-800 text-sm rounded-full font-medium"
                                    x-text="currentRoom.status"></span>
                                <p class="text-xs text-slate-400 mt-1">Managed via Bookings/Check-in.</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                                <textarea x-model="form.notes" rows="3"
                                    class="w-full rounded-lg border-slate-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <button @click="showModal = false"
                                class="px-4 py-2 border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50">Cancel</button>
                            <button @click="saveRoom()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 shadow-lg shadow-blue-200">Save
                                Changes</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .stat-card {
            @apply flex flex-col items-center justify-center w-24 h-16 rounded-xl border shadow-sm;
        }

        .room-pill {
            @apply px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm border border-transparent;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('housekeepingMatrix', () => ({
                showModal: false,
                currentRoom: {},
                form: {
                    room_number: '',
                    notes: '',
                    status: '' // housekeeping status
                },

                openManageModal(room) {
                    this.currentRoom = room;
                    this.form.room_number = room.room_number;
                    this.form.notes = room.notes;
                    this.form.status = room.housekeeping_status; // Local state for immediate UI feedback
                    this.showModal = true;
                },

                getClass(status) {
                    const active = this.form.status === status;
                    switch (status) {
                        case 'clean': return active ? 'bg-emerald-100 border-emerald-500 text-emerald-700' : 'bg-white border-slate-200 text-slate-500';
                        case 'dirty': return active ? 'bg-rose-100 border-rose-500 text-rose-700' : 'bg-white border-slate-200 text-slate-500';
                        case 'cleaning': return active ? 'bg-blue-100 border-blue-500 text-blue-700' : 'bg-white border-slate-200 text-slate-500';
                        case 'inspection_ready': return active ? 'bg-amber-100 border-amber-500 text-amber-700' : 'bg-white border-slate-200 text-slate-500';
                    }
                },

                updateStatus(status) {
                    // Optimistic update
                    this.form.status = status;

                    fetch(`/admin/housekeeping/${this.currentRoom.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ status: status })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) alert('Error updating status');
                        });
                },

                saveRoom() {
                    fetch(`/admin/housekeeping/${this.currentRoom.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            room_number: this.form.room_number,
                            notes: this.form.notes
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                window.location.reload();
                            } else {
                                alert(data.message || 'Error updating room');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Network error');
                        });
                }
            }));
        });
    </script>
@endsection