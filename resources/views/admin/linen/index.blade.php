@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Linen Inventory</h1>
                <p class="text-sm text-gray-500 mt-1">Track stock levels and par requirements</p>
            </div>
            <button onclick="document.getElementById('add-linen-modal').showModal()" 
                class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Inventory Type
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            {{-- Summary Cards --}}
            <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Valuation</p>
                <h3 class="text-2xl font-black text-slate-800 mt-2">
                    ₹ {{ number_format($linens->sum(function($l) { return $l->total_stock * $l->cost_per_unit; })) }}
                </h3>
            </div>
            
            <div class="bg-white p-6 rounded-xl border border-slate-100 shadow-sm">
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Low Stock Items</p>
                <h3 class="text-2xl font-black text-rose-600 mt-2">
                    {{ $linens->filter(function($l) { return $l->total_stock < $l->par_level; })->count() }}
                </h3>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-slate-50 text-xs uppercase font-bold text-gray-500">
                        <tr>
                            <th class="px-6 py-4">Item Name</th>
                            <th class="px-6 py-4">Category</th>
                            <th class="px-6 py-4 text-center">Total Stock</th>
                            <th class="px-6 py-4 text-center">Par Level</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($linens as $linen)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 font-semibold text-gray-800">{{ $linen->name }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest
                                        {{ $linen->category == 'bedding' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : '' }}
                                        {{ $linen->category == 'bath' ? 'bg-cyan-50 text-cyan-600 border border-cyan-100' : '' }}
                                        {{ $linen->category == 'fb' ? 'bg-orange-50 text-orange-600 border border-orange-100' : '' }}
                                        {{ $linen->category == 'staff' ? 'bg-slate-100 text-slate-600 border border-slate-200' : '' }}
                                    ">
                                        {{ $linen->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-bold">{{ $linen->total_stock }}</td>
                                <td class="px-6 py-4 text-center text-slate-400">{{ $linen->par_level }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($linen->total_stock < $linen->par_level)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-rose-50 text-rose-600 text-[10px] font-bold border border-rose-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Low Stock
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold border border-emerald-100">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Healthy
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    {{-- Edit Trigger would go here --}}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400 italic">No linen items found. Add your first item.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- CREATE MODAL (Simplest implementation using <dialog> or standard hidden div) --}}
    <dialog id="add-linen-modal" class="p-0 rounded-xl shadow-2xl backdrop:bg-black/30 w-full max-w-lg">
        <div class="bg-white">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="font-bold text-slate-800">Add New Linen Type</h3>
                <button onclick="document.getElementById('add-linen-modal').close()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="{{ route('admin.linen.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Item Name</label>
                    <input type="text" name="name" required class="w-full p-2 border border-slate-300 rounded focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Bath Towel - White">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                        <select name="category" class="w-full p-2 border border-slate-300 rounded focus:ring-blue-500 focus:border-blue-500">
                            <option value="bedding">Bedding</option>
                            <option value="bath">Bath</option>
                            <option value="fb">F&B</option>
                            <option value="staff">Staff Uniform</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Cost Per Unit</label>
                        <input type="number" name="cost_per_unit" step="0.01" class="w-full p-2 border border-slate-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Total Stock</label>
                        <input type="number" name="total_stock" required min="0" class="w-full p-2 border border-slate-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Par Level Alert</label>
                        <input type="number" name="par_level" required min="0" value="10" class="w-full p-2 border border-slate-300 rounded focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="pt-4 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('add-linen-modal').close()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-lg text-sm font-semibold">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 shadow-sm">Save Item</button>
                </div>
            </form>
        </div>
    </dialog>
@endsection
