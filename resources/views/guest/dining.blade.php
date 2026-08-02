@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 pb-20" x-data="diningApp()">
    {{-- TITLE BAR --}}
    <div class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('guest.dashboard') }}" class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 hover:bg-slate-200 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <div>
                    <h2 class="text-sm font-black text-slate-900 tracking-tight">In-Room Dining</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $booking->roomType->name }} • Suite #{{ $booking->id + 100 }}</p>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <button @click="cartOpen = true" class="relative p-2 text-slate-600 hover:text-blue-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                    <span x-show="cartCount > 0" x-text="cartCount" class="absolute -top-1 -right-1 w-5 h-5 bg-blue-600 text-white text-[10px] font-black rounded-full flex items-center justify-center border-2 border-white"></span>
                </button>
            </div>
        </div>
    </div>

    {{-- MENU AREA --}}
    <div class="max-w-7xl mx-auto px-6 mt-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            
            {{-- CATEGORIES & ITEMS --}}
            <div class="lg:col-span-8 space-y-12">
                @foreach($categories as $category)
                    <section id="cat-{{ $category->id }}">
                        <h3 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-4">
                            {{ $category->name }}
                            <div class="h-px bg-slate-200 flex-1"></div>
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($category->items as $item)
                                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition flex gap-4">
                                    <div class="w-24 h-24 bg-slate-100 rounded-3xl shrink-0 overflow-hidden">
                                        @if($item->image)
                                            <img src="{{ asset('storage/'.$item->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 flex flex-col justify-between">
                                        <div>
                                            <h4 class="font-bold text-slate-900">{{ $item->name }}</h4>
                                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ $item->description }}</p>
                                        </div>
                                        <div class="flex items-center justify-between mt-4">
                                            <p class="font-black text-slate-900">₹{{ number_format($item->price) }}</p>
                                            <button @click="addToCart({{ $item }})" class="w-8 h-8 bg-slate-900 text-white rounded-full flex items-center justify-center hover:bg-blue-600 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>

            {{-- RECENT ORDERS --}}
            <div class="lg:col-span-4 space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Order History</h3>
                    <div class="space-y-4">
                        @forelse($recentOrders as $order)
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">#{{ $order->id }} • {{ $order->created_at->format('H:i') }}</p>
                                    <p class="text-sm font-bold text-slate-900">₹{{ number_format($order->total_amount) }}</p>
                                </div>
                                <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest {{ $order->status === 'delivered' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' }}">
                                    {{ $order->status }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-6 text-slate-400 text-xs font-medium italic">No orders yet</div>
                        @endforelse
                    </div>
                </div>

                <div class="bg-blue-600 p-8 rounded-[2.5rem] text-white shadow-xl shadow-blue-200">
                    <h4 class="font-black text-lg mb-2">Need Assistance?</h4>
                    <p class="text-sm text-blue-100 font-medium mb-6">Dial 9 from your room phone for immediate service.</p>
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- CART SLIDE OVER --}}
    <div x-show="cartOpen" class="fixed inset-0 z-[60]" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" @click="cartOpen = false" x-show="cartOpen" x-transition:enter="transition-opacity ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>
        
        <div class="absolute inset-y-0 right-0 max-w-md w-full bg-white shadow-2xl flex flex-col" x-show="cartOpen" x-transition:enter="transition-transform ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition-transform ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
            
            <div class="p-8 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-xl font-black text-slate-900">Your Basket</h3>
                <button @click="cartOpen = false" class="p-2 text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-6">
                <template x-if="cart.length === 0">
                    <div class="h-full flex flex-col items-center justify-center text-slate-300">
                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="font-bold">Emptier than a midnight lobby.</p>
                    </div>
                </template>

                <template x-for="item in cart" :key="item.id">
                    <div class="flex items-center gap-6 p-4 bg-slate-50 rounded-3xl border border-slate-100">
                        <div class="flex-1">
                            <h4 x-text="item.name" class="font-bold text-slate-900 text-sm"></h4>
                            <p class="text-xs text-slate-500" x-text="'₹' + item.price"></p>
                        </div>
                        <div class="flex items-center gap-4 bg-white p-1 rounded-xl shadow-sm border border-slate-100">
                            <button @click="changeQty(item.id, -1)" class="w-6 h-6 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-900">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            </button>
                            <span x-text="item.qty" class="text-xs font-black w-4 text-center"></span>
                            <button @click="changeQty(item.id, 1)" class="w-6 h-6 bg-slate-50 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 hover:text-slate-900">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-8 bg-slate-50 border-t border-slate-100 space-y-6">
                <div>
                     <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Special Instructions</label>
                     <textarea x-model="notes" class="w-full bg-white border-slate-200 rounded-2xl p-4 text-xs font-medium focus:ring-blue-500 focus:border-blue-500" rows="2" placeholder="Allergies, door code, etc."></textarea>
                </div>

                <div class="flex justify-between items-center px-2">
                    <p class="text-sm font-bold text-slate-500">Order Subtotal</p>
                    <p class="text-2xl font-black text-slate-900" x-text="'₹' + cartTotal"></p>
                </div>

                <button @click="submitOrder" :disabled="cart.length === 0 || loading" class="w-full py-5 bg-slate-900 text-white rounded-[1.5rem] font-black tracking-tight shadow-xl shadow-slate-900/20 hover:bg-black transition flex items-center justify-center gap-3 disabled:opacity-50">
                    <template x-if="loading">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    Place Room Service Order
                </button>
            </div>
        </div>
    </div>

</div>

<script>
function diningApp() {
    return {
        cartOpen: false,
        cart: [],
        notes: '',
        loading: false,

        get cartCount() {
            return this.cart.reduce((acc, item) => acc + item.qty, 0);
        },

        get cartTotal() {
            return this.cart.reduce((acc, item) => acc + (item.price * item.qty), 0);
        },

        addToCart(item) {
            const existing = this.cart.find(i => i.id === item.id);
            if (existing) {
                existing.qty++;
            } else {
                this.cart.push({ ...item, qty: 1 });
            }
            this.cartOpen = true;
        },

        changeQty(id, delta) {
            const item = this.cart.find(i => i.id === id);
            if (!item) return;
            item.qty += delta;
            if (item.qty <= 0) {
                this.cart = this.cart.filter(i => i.id !== id);
            }
        },

        async submitOrder() {
            this.loading = true;
            try {
                const response = await fetch('{{ route('guest.dining.order') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        items: this.cart,
                        notes: this.notes
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showToast(data.message);
                    this.cart = [];
                    this.notes = '';
                    this.cartOpen = false;
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (e) {
                showToast('Something went wrong', 'error');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
@endsection
