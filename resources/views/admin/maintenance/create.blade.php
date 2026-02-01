@extends('layouts.admin')

@section('content')
    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="mb-8">
            <a href="{{ route('admin.maintenance.index') }}"
                class="text-slate-500 hover:text-slate-700 flex items-center gap-2 mb-4 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Back to Dashboard
            </a>
            <h1 class="text-3xl font-bold text-slate-800">Report Maintenance Issue</h1>
            <p class="text-slate-500 mt-1">Log a new repair request or general maintenance task.</p>
        </div>

        <form action="{{ route('admin.maintenance.store') }}" method="POST"
            class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            @csrf

            <div class="space-y-6">
                <!-- Asset Selection -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Affected Asset (Optional)</label>
                    <div class="relative">
                        <select name="asset_id"
                            class="w-full p-3 rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm font-medium appearance-none">
                            <option value="">-- No Specific Asset --</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">
                                    {{ $asset->name }}
                                    @if($asset->room)
                                        (Room {{ $asset->room->room_number }})
                                    @elseif($asset->roomType)
                                        ({{ $asset->roomType->name }})
                                    @else
                                        (Storage)
                                    @endif
                                    - {{ $asset->qr_code }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-slate-100"></div>
                    <span class="flex-shrink-0 mx-4 text-xs font-bold text-slate-400 uppercase tracking-widest">OR</span>
                    <div class="flex-grow border-t border-slate-100"></div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">General Room Issue</label>
                    <div class="relative">
                        <select name="room_id"
                            class="w-full p-3 rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm font-medium appearance-none">
                            <option value="">-- Select Room --</option>
                            @foreach($rooms as $room)
                                <option value="{{ $room->id }}">
                                    Room {{ $room->room_number }} ({{ $room->roomType->name }})
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-slate-400 mt-2 font-medium">If the issue is not related to a specific asset (e.g.
                        Broken
                        Tile, Wall Paint).</p>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Priority Level <span
                            class="text-rose-500">*</span></label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="cursor-pointer group">
                            <input type="radio" name="priority" value="low" class="peer sr-only">
                            <div
                                class="text-center py-3 rounded-xl border border-slate-200 font-bold text-slate-500 peer-checked:bg-slate-100 peer-checked:border-slate-400 peer-checked:text-slate-900 transition hover:bg-slate-50 shadow-sm">
                                Low
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="priority" value="medium" class="peer sr-only" checked>
                            <div
                                class="text-center py-3 rounded-xl border border-slate-200 font-bold text-slate-500 peer-checked:bg-blue-50 peer-checked:border-blue-400 peer-checked:text-blue-700 transition hover:bg-blue-50/50 shadow-sm">
                                Medium
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="priority" value="high" class="peer sr-only">
                            <div
                                class="text-center py-3 rounded-xl border border-slate-200 font-bold text-slate-500 peer-checked:bg-amber-50 peer-checked:border-amber-400 peer-checked:text-amber-700 transition hover:bg-amber-50/50 shadow-sm">
                                High
                            </div>
                        </label>
                        <label class="cursor-pointer group">
                            <input type="radio" name="priority" value="critical" class="peer sr-only">
                            <div
                                class="text-center py-3 rounded-xl border border-slate-200 font-bold text-slate-500 peer-checked:bg-rose-50 peer-checked:border-rose-400 peer-checked:text-rose-700 transition hover:bg-rose-50/50 shadow-sm">
                                Critical
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Issue Description <span
                            class="text-rose-500">*</span></label>
                    <textarea name="description" rows="4"
                        class="w-full p-3 rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm font-medium"
                        placeholder="Describe the problem in detail (e.g., AC not cooling, leaking water)..."
                        required></textarea>
                </div>

                <!-- Technician -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Assign Technician (Optional)</label>
                    <input type="text" name="technician_name"
                        class="w-full p-3 rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 text-sm font-medium"
                        placeholder="e.g. John Doe">
                </div>
            </div>

            <div class="mt-8 pt-8 border-t border-slate-100 flex justify-end gap-4">
                <a href="{{ route('admin.maintenance.index') }}"
                    class="px-6 py-2.5 rounded-lg border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 transition">Cancel</a>
                <button type="submit"
                    class="px-6 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition shadow-lg shadow-blue-500/30">Log
                    Issue</button>
            </div>
        </form>
    </div>
@endsection