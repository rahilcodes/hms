@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Laundry Vendors</h1>
            <button onclick="document.getElementById('add-vendor-modal').showModal()"
                class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                + Add Vendor
            </button>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-slate-50 text-xs uppercase font-bold text-gray-500">
                    <tr>
                        <th class="px-6 py-4">Vendor Name</th>
                        <th class="px-6 py-4">Contact Person</th>
                        <th class="px-6 py-4">Contact Info</th>
                        <th class="px-6 py-4 text-center">Active Batches</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $vendor->name }}</td>
                            <td class="px-6 py-4">{{ $vendor->contact_person ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs">{{ $vendor->phone }}</span>
                                    <span class="text-xs text-slate-400">{{ $vendor->email }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-xs font-bold">
                                    {{ $vendor->batches()->where('status', '!=', 'completed')->count() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right gap-2">
                                {{-- Edit (Future) --}}
                                <form action="{{ route('admin.laundry.vendors.destroy', $vendor) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Delete vendor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-rose-600 hover:text-rose-800 font-bold text-xs uppercase">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-400 italic">No vendors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden grid grid-cols-1 gap-4">
            @forelse($vendors as $vendor)
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col gap-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-slate-900 text-lg">{{ $vendor->name }}</h3>
                            <p class="text-xs font-semibold text-slate-500">{{ $vendor->contact_person }}</p>
                        </div>
                        <span
                            class="px-2 py-1 bg-blue-50 text-blue-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-blue-100">
                            {{ $vendor->batches()->where('status', '!=', 'completed')->count() }} Active
                        </span>
                    </div>

                    <div class="space-y-2 mt-2">
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                </path>
                            </svg>
                            {{ $vendor->phone }}
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-600">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            {{ $vendor->email }}
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50 flex justify-end">
                        <form action="{{ route('admin.laundry.vendors.destroy', $vendor) }}" method="POST" class="inline"
                            onsubmit="return confirm('Delete vendor?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-rose-500 hover:text-rose-700 font-black text-xs uppercase tracking-widest py-2">Delete
                                Vendor</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-10">
                    <p class="text-slate-400 text-sm font-bold">No vendors found.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ADD MODAL --}}
    <dialog id="add-vendor-modal" class="p-0 rounded-xl shadow-2xl backdrop:bg-black/30 w-full max-w-lg">
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800">Add Laundry Vendor</h3>
                <button onclick="document.getElementById('add-vendor-modal').close()"
                    class="text-slate-400 hover:text-slate-600">✕</button>
            </div>
            <form action="{{ route('admin.laundry.vendors.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Company Name</label>
                    <input type="text" name="name" required
                        class="w-full p-2 border border-slate-300 rounded focus:border-blue-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Contact Person</label>
                        <input type="text" name="contact_person"
                            class="w-full p-2 border border-slate-300 rounded focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                        <input type="text" name="phone"
                            class="w-full p-2 border border-slate-300 rounded focus:border-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email"
                        class="w-full p-2 border border-slate-300 rounded focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="2"
                        class="w-full p-2 border border-slate-300 rounded focus:border-blue-500"></textarea>
                </div>
                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('add-vendor-modal').close()"
                        class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save
                        Vendor</button>
                </div>
            </form>
        </div>
    </dialog>
@endsection