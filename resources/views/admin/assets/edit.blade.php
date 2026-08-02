@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="mb-8">
            <a href="{{ route('admin.assets.index') }}"
                class="text-slate-500 hover:text-slate-700 flex items-center gap-2 mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Asset List
            </a>
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800">Edit Asset: {{ $asset->name }}</h1>
                    <p class="text-slate-500 mt-1">Update asset information and status.</p>
                </div>
                <div class="bg-slate-100 rounded-lg px-4 py-2 font-mono text-sm text-slate-600 border border-slate-200">
                    Correct QR: <span class="font-bold text-slate-900">{{ $asset->qr_code }}</span>
                </div>
            </div>
        </div>

        <form action="{{ route('admin.assets.update', $asset) }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Basic Info -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2 mb-4">Item Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Asset Name <span
                                        class="text-rose-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $asset->name) }}"
                                    class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="e.g. Samsung Split AC 1.5T" required>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Asset Type <span
                                        class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <select name="type"
                                        class="w-full appearance-none rounded-xl border-slate-200 bg-slate-50 px-4 py-3 pr-8 text-slate-700 focus:border-blue-500 focus:bg-white focus:ring-blue-500 font-bold shadow-sm transition-all"
                                        required>
                                        <option value="" disabled>Select Type</option>
                                        <option value="AC" {{ $asset->type === 'AC' ? 'selected' : '' }}>Air Conditioner (AC)
                                        </option>
                                        <option value="TV" {{ $asset->type === 'TV' ? 'selected' : '' }}>Television (TV)</option>
                                        <option value="Furniture" {{ $asset->type === 'Furniture' ? 'selected' : '' }}>Furniture
                                        </option>
                                        <option value="Appliance" {{ $asset->type === 'Appliance' ? 'selected' : '' }}>Appliance
                                            (Fridge, Kettle)</option>
                                        <option value="Linen" {{ $asset->type === 'Linen' ? 'selected' : '' }}>Linen / Bedding
                                        </option>
                                        <option value="Other" {{ $asset->type === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Room Type (Category)</label>
                                    <div class="relative">
                                        <select name="room_type_id"
                                            class="w-full appearance-none rounded-xl border-slate-200 bg-slate-50 px-4 py-3 pr-8 text-slate-700 focus:border-blue-500 focus:bg-white focus:ring-blue-500 font-bold shadow-sm transition-all">
                                            <option value="">General Storage</option>
                                            @foreach($roomTypes as $roomType)
                                                <option value="{{ $roomType->id }}" {{ $asset->room_type_id == $roomType->id ? 'selected' : '' }}>{{ $roomType->name }}</option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Specific Room</label>
                                    <div class="relative">
                                        <select name="room_id"
                                            class="w-full appearance-none rounded-xl border-slate-200 bg-slate-50 px-4 py-3 pr-8 text-slate-700 focus:border-blue-500 focus:bg-white focus:ring-blue-500 font-bold shadow-sm transition-all">
                                            <option value="">-- None --</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}" {{ $asset->room_id == $room->id ? 'selected' : '' }}>
                                                    {{ $room->room_number }} ({{ $room->roomType->name ?? 'N/A' }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">Select a specific room if the asset is installed there.
                            </p>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Status <span
                                        class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <select name="status"
                                        class="w-full appearance-none rounded-xl border-slate-200 bg-slate-50 px-4 py-3 pr-8 text-slate-700 focus:border-blue-500 focus:bg-white focus:ring-blue-500 font-bold shadow-sm transition-all">
                                        <option value="active" {{ $asset->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="in-repair" {{ $asset->status === 'in-repair' ? 'selected' : '' }}>In Repair
                                        </option>
                                        <option value="retired" {{ $asset->status === 'retired' ? 'selected' : '' }}>Retired /
                                            Disposed</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
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
                                    <input type="text" name="brand" value="{{ old('brand', $asset->brand) }}"
                                        class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="e.g. Samsung">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Model</label>
                                    <input type="text" name="model" value="{{ old('model', $asset->model) }}"
                                        class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                                        placeholder="e.g. AR18NV3HLTR">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Serial Number</label>
                                <input type="text" name="serial_number"
                                    value="{{ old('serial_number', $asset->serial_number) }}"
                                    class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500"
                                    placeholder="Unique Manufacturer ID">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Purchase Date</label>
                                    <input type="date" name="purchase_date"
                                        value="{{ $asset->purchase_date ? $asset->purchase_date->format('Y-m-d') : '' }}"
                                        class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Warranty Expiry</label>
                                    <input type="date" name="warranty_expiry"
                                        value="{{ $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : '' }}"
                                        class="w-full rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <div class="flex gap-3">
                            <div class="text-slate-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-sm">Last Updated</h4>
                                <p class="text-xs text-slate-500 mt-1">{{ $asset->updated_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-slate-100 flex justify-end gap-4">
                <a href="{{ route('admin.assets.index') }}"
                    class="px-6 py-2.5 rounded-lg border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">Cancel</a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">Update
                    Asset</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roomTypeSelect = document.querySelector('select[name="room_type_id"]');
            const roomSelect = document.querySelector('select[name="room_id"]');
            const allRooms = @json($rooms);
            // Get initial/old value for room_id
            const initialRoomId = "{{ old('room_id', $asset->room_id) }}";

            function filterRooms(preserveValue = false) {
                const selectedType = roomTypeSelect.value;
                const currentRoomId = preserveValue ? initialRoomId : roomSelect.value;

                // Clear current options
                roomSelect.innerHTML = '<option value="">-- None --</option>';

                if (selectedType) {
                    const filtered = allRooms.filter(room => room.room_type_id == selectedType);

                    if (filtered.length > 0) {
                        filtered.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.id;
                            option.textContent = `Room ${room.room_number}`;

                            // Check if this option should be selected
                            if (room.id == currentRoomId) {
                                option.selected = true;
                            }

                            roomSelect.appendChild(option);
                        });
                    } else {
                        const option = document.createElement('option');
                        option.textContent = "No rooms found for this type";
                        option.disabled = true;
                        roomSelect.appendChild(option);
                    }
                } else {
                    const option = document.createElement('option');
                    option.textContent = "Select a Room Type first";
                    option.disabled = true;
                    roomSelect.appendChild(option);
                }
            }

            roomTypeSelect.addEventListener('change', () => filterRooms(false));

            // Initial execution - preserve the existing assignment
            if (roomTypeSelect.value) {
                filterRooms(true);
            }
        });
    </script>
@endsection