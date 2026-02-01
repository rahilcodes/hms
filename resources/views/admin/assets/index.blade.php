@extends('layouts.admin')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-slate-800">Asset Management</h1>
                <p class="text-slate-500 mt-1">Track and manage hotel inventory, appliances, and furniture.</p>
            </div>
            <div class="flex gap-4">
                <button
                    class="bg-white border border-slate-200 text-slate-600 px-4 py-2 rounded-lg hover:bg-slate-50 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Print Labels
                </button>
                <a href="{{ route('admin.assets.create') }}"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-lg shadow-blue-600/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Asset
                </a>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Asset Name
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Location
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">QR Code
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500">
                                        @if($asset->type === 'AC') ❄️
                                        @elseif($asset->type === 'TV') 📺
                                        @elseif($asset->type === 'Furniture') 🪑
                                        @elseif($asset->type === 'Linen') 🧺
                                        @else 📦
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $asset->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $asset->brand ?? 'Generic' }} {{ $asset->model }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $asset->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($asset->room)
                                    <span class="text-sm font-bold text-slate-800">Room {{ $asset->room->room_number }}</span>
                                    <span class="text-xs text-slate-500 block">({{ $asset->roomType->name ?? 'N/A' }})</span>
                                @elseif($asset->roomType)
                                    <span class="text-sm font-medium text-slate-700">{{ $asset->roomType->name }}</span>
                                    <span class="text-xs text-slate-400 block">(General Type)</span>
                                @else
                                    <span class="text-sm font-medium text-slate-400 italic">Storage / General</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($asset->status === 'active')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center gap-1 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @elseif($asset->status === 'in-repair')
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100 flex items-center gap-1 w-fit">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> In Repair
                                    </span>
                                @else
                                    <span
                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 flex items-center gap-1 w-fit">
                                        Retired
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-mono text-xs bg-slate-900 text-white px-2 py-1 rounded w-fit">
                                    {{ $asset->qr_code }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.assets.edit', $asset) }}"
                                        class="p-2 hover:bg-blue-50 text-slate-400 hover:text-blue-600 rounded-lg transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 hover:bg-rose-50 text-slate-400 hover:text-rose-600 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-slate-900 font-bold text-lg">No Assets Found</h3>
                                    <p class="text-slate-500 text-sm mt-1 max-w-sm">Start by adding your ACs, TVs, and furniture
                                        to track maintenance and inventory.</p>
                                    <a href="{{ route('admin.assets.create') }}"
                                        class="mt-4 text-blue-600 font-bold text-sm hover:underline">Add First Asset &rarr;</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $assets->links() }}
        </div>
    </div>
@endsection