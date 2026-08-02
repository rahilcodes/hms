@extends('layouts.admin')

@section('content')
    <!-- Alpine.js Component for Custom Selects -->
    <div x-data="assetForm()" class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-8">
            <a href="{{ route('admin.assets.index') }}"
                class="text-slate-500 hover:text-slate-700 flex items-center gap-2 mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Asset List
            </a>
            <h1 class="text-3xl font-bold text-slate-800">Register New Asset</h1>
            <p class="text-slate-500 mt-1">Add a new item to your inventory to track its lifecycle and maintenance.</p>
        </div>

        <form action="{{ route('admin.assets.store') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Basic Info -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Item Details</h3>
                        <div class="space-y-4">
                            <!-- Name -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Asset Name <span
                                        class="text-rose-500">*</span></label>
                                <input type="text" name="name"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold placeholder:font-normal placeholder:text-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm"
                                    placeholder="e.g. Samsung Split AC 1.5T" required>
                            </div>

                            <!-- Type -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Asset Type <span
                                        class="text-rose-500">*</span></label>
                                <input type="hidden" name="type" x-model="type">
                                <div class="relative" x-data="{ open: false }">
                                    <button type="button" @click="open = !open" @click.away="open = false"
                                        class="w-full text-left bg-slate-50 border border-slate-200 text-slate-700 font-bold py-3 px-4 rounded-xl flex justify-between items-center hover:bg-slate-100 hover:border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                                        <span x-text="type ? assetTypes[type] : 'Select Type'" :class="{'text-slate-400 font-normal': !type}"></span>
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <div x-show="open" x-cloak
                                        class="absolute z-10 mt-1 w-full bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden max-h-60 overflow-y-auto ring-1 ring-black/5">
                                        <template x-for="(label, key) in assetTypes" :key="key">
                                            <div @click="type = key; open = false"
                                                class="px-4 py-3 hover:bg-blue-50 hover:text-blue-700 cursor-pointer text-sm font-bold text-slate-600 transition border-b border-slate-50 last:border-0"
                                                :class="{'bg-blue-50 text-blue-700': type === key}">
                                                <span x-text="label"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Room Selection Grid -->
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Room Type -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Room Type</label>
                                    <input type="hidden" name="room_type_id" x-model="room_type_id">
                                    <div class="relative" x-data="{ open: false }">
                                        <button type="button" @click="open = !open" @click.away="open = false"
                                            class="w-full text-left bg-slate-50 border border-slate-200 text-slate-700 font-bold py-3 px-4 rounded-xl flex justify-between items-center hover:bg-slate-100 hover:border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                                            <span x-text="getRoomTypeName() || 'General Storage'"></span>
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>
                                        <div x-show="open" x-cloak
                                            class="absolute z-10 mt-1 w-full bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden max-h-60 overflow-y-auto ring-1 ring-black/5">
                                            <div @click="setRoomType('')"
                                                class="px-4 py-3 hover:bg-blue-50 hover:text-blue-700 cursor-pointer text-sm font-bold text-slate-600 border-b border-slate-50">
                                                General Storage
                                            </div>
                                            <template x-for="rt in roomTypes" :key="rt.id">
                                                <div @click="setRoomType(rt.id)"
                                                    class="px-4 py-3 hover:bg-blue-50 hover:text-blue-700 cursor-pointer text-sm font-bold text-slate-600 transition border-b border-slate-50 last:border-0"
                                                    :class="{'bg-blue-50 text-blue-700': room_type_id == rt.id}">
                                                    <span x-text="rt.name"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <!-- Specific Room -->
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Specific Room</label>
                                    <input type="hidden" name="room_id" x-model="room_id">
                                    <div class="relative" x-data="{ open: false }">
                                        <button type="button" @click="open = !open" @click.away="open = false"
                                            class="w-full text-left bg-slate-50 border border-slate-200 text-slate-700 font-bold py-3 px-4 rounded-xl flex justify-between items-center hover:bg-slate-100 hover:border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm"
                                            :disabled="!room_type_id"
                                            :class="{'opacity-50 cursor-not-allowed bg-slate-100': !room_type_id}">
                                            <span x-text="getRoomName() || '-- None --'" :class="{'text-slate-400': !room_id}"></span>
                                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </button>

                                        <div x-show="open && room_type_id" x-cloak
                                            class="absolute z-10 mt-1 w-full bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden max-h-60 overflow-y-auto ring-1 ring-black/5">
                                            <div @click="room_id = ''"
                                                class="px-4 py-3 hover:bg-blue-50 hover:text-blue-700 cursor-pointer text-sm font-bold text-slate-600 border-b border-slate-50">
                                                -- None --
                                            </div>

                                            <template x-if="filteredRooms.length === 0">
                                                <div class="px-4 py-3 text-xs text-slate-400 font-bold italic">No rooms in
                                                    this category</div>
                                            </template>

                                            <template x-for="room in filteredRooms" :key="room.id">
                                                <div @click="room_id = room.id; open = false"
                                                    class="px-4 py-3 hover:bg-blue-50 hover:text-blue-700 cursor-pointer text-sm font-bold text-slate-600 transition border-b border-slate-50 last:border-0"
                                                    :class="{'bg-blue-50 text-blue-700': room_id == room.id}">
                                                    <span x-text="`Room ${room.room_number}`"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Select a specific room if the asset is installed there.
                            </p>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Status <span
                                        class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <select name="status"
                                        class="w-full appearance-none bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 pr-8 text-slate-700 font-bold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                                        <option value="active" selected>Active</option>
                                        <option value="in-repair">In Repair</option>
                                        <option value="retired">Retired / Disposed</option>
                                    </select>
                                    <div
                                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Technical Info -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Technical Specs
                        </h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Brand</label>
                                    <input type="text" name="brand"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold placeholder:font-normal placeholder:text-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm"
                                        placeholder="e.g. Samsung">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Model</label>
                                    <input type="text" name="model"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold placeholder:font-normal placeholder:text-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm"
                                        placeholder="e.g. AR18NV3HLTR">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Serial Number</label>
                                <input type="text" name="serial_number"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold placeholder:font-normal placeholder:text-slate-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm"
                                    placeholder="Unique Manufacturer ID">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Purchase Date</label>
                                    <input type="date" name="purchase_date"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Warranty Expiry</label>
                                    <input type="date" name="warranty_expiry"
                                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-slate-900 font-bold focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all shadow-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl border border-blue-100">
                        <div class="flex gap-3">
                            <div class="text-blue-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-blue-900 text-sm">QR Code Generation</h4>
                                <p class="text-xs text-blue-700 mt-1">A unique QR Code ID will be automatically generated
                                    for this asset upon saving. You can print the label from the list view.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-slate-100 flex justify-end gap-4">
                <a href="{{ route('admin.assets.index') }}"
                    class="px-6 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">Cancel</a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">Register
                    Asset</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('assetForm', () => ({
                type: '',
                room_type_id: '',
                room_id: '',
                rooms: @json($rooms),
                roomTypes: @json($roomTypes),
                assetTypes: {
                    'AC': 'Air Conditioner (AC)',
                    'TV': 'Television (TV)',
                    'Furniture': 'Furniture',
                    'Appliance': 'Appliance (Fridge, Kettle)',
                    'Linen': 'Linen / Bedding',
                    'Other': 'Other'
                },

                get filteredRooms() {
                    if (!this.room_type_id) return [];
                    return this.rooms.filter(r => r.room_type_id == this.room_type_id);
                },

                setRoomType(id) {
                    this.room_type_id = id;
                    this.room_id = ''; // Reset specific room when type changes
                },

                getRoomTypeName() {
                    if (!this.room_type_id) return '';
                    const rt = this.roomTypes.find(t => t.id == this.room_type_id);
                    return rt ? rt.name : '';
                },

                getRoomName() {
                    if (!this.room_id) return '';
                    const r = this.rooms.find(rm => rm.id == this.room_id);
                    return r ? `Room ${r.room_number}` : '';
                }
            }));
        });
    </script>
@endsection