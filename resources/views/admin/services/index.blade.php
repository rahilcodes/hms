@extends('layouts.admin')

@section('header_title', 'Upsell Services')

@section('content')
    <div class="space-y-8" x-data="{ showModal: false, editingService: null }">

        {{-- HEADER ACTION --}}
        <div class="flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Available Services</h3>
                <p class="text-sm text-slate-500 font-medium">Manage add-ons and experiences for your guests.</p>
            </div>
            <button @click="showModal = true; editingService = null" class="btn-primary flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Service
            </button>
        </div>

        {{-- SERVICES GRID --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($services as $service)
                <div
                    class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 flex flex-col hover:shadow-md transition group">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="{{ $service->icon_class ?? 'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4' }}">
                                </path>
                            </svg>
                        </div>
                        <div class="flex gap-2">
                            <button @click="showModal = true; editingService = {{ json_encode($service) }}"
                                class="p-2 text-slate-400 hover:text-blue-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                    </path>
                                </svg>
                            </button>
                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST"
                                onsubmit="return confirm('Delete this service?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-slate-400 hover:text-red-600 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <h4 class="font-bold text-slate-900 group-hover:text-blue-600 transition">{{ $service->name }}</h4>
                    <p class="text-xs text-slate-500 mt-1 mb-4 flex-1">{{ $service->description }}</p>
                    <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-100">
                        <span class="text-lg font-bold text-blue-600">₹{{ number_format($service->price, 0) }}</span>
                        <span
                            class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-widest {{ $service->is_active ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-400' }}">
                            {{ $service->is_active ? 'Active' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-300">
                    <div
                        class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-sm font-bold text-slate-400 uppercase tracking-widest">No services created yet</p>
                </div>
            @endforelse
        </div>

        {{-- MODAL --}}
        <div x-show="showModal"
            class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-cloak>
            <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden" @click.away="showModal = false">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-900" x-text="editingService ? 'Edit Service' : 'Add New Service'"></h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                </div>
                <form
                    :action="editingService ? `/admin/services/${editingService.id}` : '{{ route('admin.services.store') }}'"
                    method="POST" class="p-6 space-y-4">
                    @csrf
                    <template x-if="editingService">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Service
                            Name</label>
                        <input type="text" name="name" :value="editingService ? editingService.name : ''" required
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:bg-white transition outline-none"
                            placeholder="e.g. Guided City Tour">
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Description</label>
                        <textarea name="description" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:bg-white transition outline-none"
                            placeholder="Briefly describe what's included..."
                            x-text="editingService ? editingService.description : ''"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Price</label>
                            <input type="number" name="price" :value="editingService ? editingService.price : ''" required
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:bg-white transition outline-none"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Price
                                Unit</label>
                            <select name="price_unit"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:bg-white transition outline-none">
                                <option value="fixed"
                                    :selected="editingService ? editingService.price_unit === 'fixed' : true">Fixed Fee
                                </option>
                                <option value="per_hour"
                                    :selected="editingService ? editingService.price_unit === 'per_hour' : false">Per Hour
                                </option>
                                <option value="per_person"
                                    :selected="editingService ? editingService.price_unit === 'per_person' : false">Per
                                    Person</option>
                                <option value="per_night"
                                    :selected="editingService ? editingService.price_unit === 'per_night' : false">Per Night
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label
                                class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5 ml-1">Status</label>
                            <select name="is_active"
                                class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm font-bold focus:ring-2 focus:ring-blue-500 focus:bg-white transition outline-none">
                                <option value="1" :selected="editingService ? editingService.is_active : true">Active
                                </option>
                                <option value="0" :selected="editingService ? !editingService.is_active : false">Disabled
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4 drop-shadow-sm">
                        <button type="submit" class="w-full btn-primary py-3 font-bold"
                            x-text="editingService ? 'Update Service' : 'Create Service'"></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection