@extends('layouts.admin')

@section('header_title', 'Menu Management')

@section('content')
<div class="space-y-8" x-data="{ itemModal: false, activeCategory: null }">

    {{-- TOP BAR --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-slate-900">Food & Beverage Menu</h2>
            <p class="text-sm font-medium text-slate-500">Manage categories and items for In-Room Dining.</p>
        </div>
        <div class="flex gap-3">
             <button @click="$dispatch('open-modal', 'new-category')" class="px-6 py-3 bg-white border border-slate-200 text-slate-600 font-bold rounded-2xl hover:bg-slate-50 transition text-sm">
                + New Category
            </button>
            <button @click="itemModal = true" class="px-6 py-3 bg-slate-900 text-white font-bold rounded-2xl hover:bg-black transition text-sm shadow-xl shadow-slate-900/20">
                + Add Menu Item
            </button>
        </div>
    </div>

    {{-- MENU GRID --}}
    <div class="grid grid-cols-1 gap-10">
        @foreach($categories as $category)
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-widest">{{ $category->name }}</h3>
                    <div class="h-px bg-slate-200 flex-1"></div>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $category->items->count() }} Items</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($category->items as $item)
                        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition group overflow-hidden relative">
                            <div class="flex gap-4">
                                <div class="w-16 h-16 bg-slate-100 rounded-2xl shrink-0 flex items-center justify-center text-slate-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-slate-900">{{ $item->name }}</h4>
                                        <p class="font-black text-slate-900 text-sm">₹{{ number_format($item->price) }}</p>
                                    </div>
                                    <p class="text-[10px] text-slate-500 font-medium mt-1 line-clamp-2">{{ $item->description }}</p>
                                    
                                    <div class="flex items-center gap-3 mt-4">
                                        <form action="{{ route('admin.menu.item.toggle', $item) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-[9px] font-black uppercase tracking-widest px-2 py-1 rounded-lg {{ $item->is_available ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                                                {{ $item->is_available ? 'Available' : 'Out of Stock' }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.menu.item.delete', $item) }}" method="POST" onsubmit="return confirm('Delete this item?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-slate-300 hover:text-rose-500 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- MODALS --}}
    <!-- Add Item Modal -->
    <div x-show="itemModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="itemModal = false"></div>
        <div class="bg-white rounded-[2rem] p-8 max-w-md w-full relative z-10 shadow-2xl">
            <h3 class="text-xl font-black text-slate-900 mb-6">Add New Dish</h3>
            <form action="{{ route('admin.menu.item.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Category</label>
                    <select name="menu_category_id" required class="w-full bg-slate-50 border-slate-200 rounded-xl p-3 text-sm font-bold">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Dish Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-50 border-slate-200 rounded-xl p-3 text-sm font-bold">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Price (₹)</label>
                        <input type="number" name="price" required class="w-full bg-slate-50 border-slate-200 rounded-xl p-3 text-sm font-bold">
                    </div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full bg-slate-50 border-slate-200 rounded-xl p-3 text-sm font-medium"></textarea>
                </div>
                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-xl font-black text-sm uppercase tracking-widest hover:bg-black transition shadow-lg">Save Dish</button>
            </form>
        </div>
    </div>

    <!-- Category Modal -->
    <div x-data="{ open: false }" x-on:open-modal.window="if($event.detail === 'new-category') open = true" x-show="open" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
         <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="open = false"></div>
         <div class="bg-white rounded-[2rem] p-8 max-w-xs w-full relative z-10 shadow-2xl">
            <h3 class="text-xl font-black text-slate-900 mb-6">New Category</h3>
            <form action="{{ route('admin.menu.category.store') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="name" placeholder="e.g. Desserts" required class="w-full bg-slate-50 border-slate-200 rounded-xl p-3 text-sm font-bold">
                <button type="submit" class="w-full py-4 bg-slate-900 text-white rounded-xl font-black">Create</button>
            </form>
         </div>
    </div>

</div>
@endsection
