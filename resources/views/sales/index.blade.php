@extends('layouts.app')

@section('title', 'Sales / POS')

@section('content')
<div class="h-[calc(100vh-4rem)] flex flex-col" x-data="posApp()">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Point of Sale</h1>
            <p class="text-slate-500 text-sm mt-1">Cashier interface — Transaction #TRX-{{ date('His') }}</p>
        </div>
    </div>

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-0">
        {{-- Product Search & Grid --}}
        <div class="lg:col-span-2 flex flex-col bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <input type="search" x-model="search" placeholder="Search products..." 
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div class="flex-1 overflow-auto p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    @php $products = [
                        ['id' => 1, 'name' => 'Kopi Sachet', 'price' => 2500, 'img' => null],
                        ['id' => 2, 'name' => 'Susu UHT 1L', 'price' => 15000, 'img' => null],
                        ['id' => 3, 'name' => 'Mie Instan', 'price' => 3500, 'img' => null],
                        ['id' => 4, 'name' => 'Snack Keripik', 'price' => 5000, 'img' => null],
                        ['id' => 5, 'name' => 'Aqua Gelas', 'price' => 500, 'img' => null],
                        ['id' => 6, 'name' => 'Teh Botol', 'price' => 4000, 'img' => null],
                        ['id' => 7, 'name' => 'Roti Tawar', 'price' => 12000, 'img' => null],
                        ['id' => 8, 'name' => 'Indomie Goreng', 'price' => 3000, 'img' => null],
                        ['id' => 9, 'name' => 'Coca-Cola 330ml', 'price' => 5000, 'img' => null],
                        ['id' => 10, 'name' => 'Chitato', 'price' => 8500, 'img' => null],
                    ]; @endphp
                    @foreach($products as $p)
                    <button @click="addToCart({{ $p['id'] }}, '{{ addslashes($p['name']) }}', {{ $p['price'] }})"
                        class="flex flex-col items-center p-3 rounded-lg border border-slate-200 hover:border-sky-400 hover:bg-sky-50 transition-colors text-left">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-700 truncate w-full">{{ $p['name'] }}</span>
                        <span class="text-xs text-sky-600 font-semibold">Rp {{ number_format($p['price'], 0, ',', '.') }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Cart Panel --}}
        <div class="flex flex-col bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50">
                <h3 class="font-semibold text-slate-800">Shopping Cart</h3>
            </div>
            <div class="flex-1 overflow-auto p-4 min-h-[200px]">
                <template x-if="cart.length === 0">
                    <div class="flex flex-col items-center justify-center h-48 text-slate-400">
                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="text-sm">Cart is empty</p>
                        <p class="text-xs mt-1">Click products to add</p>
                    </div>
                </template>
                <div class="space-y-2" x-show="cart.length > 0">
                    <template x-for="(item, i) in cart" :key="item.id + '-' + i">
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                            <div>
                                <p class="font-medium text-slate-700 text-sm" x-text="item.name"></p>
                                <p class="text-xs text-slate-500">Rp <span x-text="formatNumber(item.price)"></span> × <span x-text="item.qty"></span></p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button @click="updateQty(i, -1)" class="w-7 h-7 rounded bg-slate-200 hover:bg-slate-300 flex items-center justify-center text-slate-600 font-bold">−</button>
                                <span class="w-8 text-center font-medium text-slate-700" x-text="item.qty"></span>
                                <button @click="updateQty(i, 1)" class="w-7 h-7 rounded bg-slate-200 hover:bg-slate-300 flex items-center justify-center text-slate-600 font-bold">+</button>
                                <button @click="removeItem(i)" class="w-7 h-7 rounded bg-red-100 hover:bg-red-200 text-red-600 flex items-center justify-center ml-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100 space-y-4">
                <div class="flex justify-between items-center text-lg">
                    <span class="font-medium text-slate-700">Total</span>
                    <span class="font-bold text-slate-800">Rp <span x-text="formatNumber(total)"></span></span>
                </div>
                <button @click="checkout()"
                    class="w-full py-3 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-semibold transition-colors disabled:opacity-50"
                    :disabled="cart.length === 0">
                    Pay
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function posApp() {
    return {
        search: '',
        cart: [],
        addToCart(id, name, price) {
            const idx = this.cart.findIndex(c => c.id === id && c.name === name && c.price === price);
            if (idx >= 0) this.cart[idx].qty++;
            else this.cart.push({ id, name, price, qty: 1 });
        },
        updateQty(i, delta) {
            this.cart[i].qty += delta;
            if (this.cart[i].qty <= 0) this.cart.splice(i, 1);
        },
        removeItem(i) { this.cart.splice(i, 1); },
        get total() {
            return this.cart.reduce((s, c) => s + c.price * c.qty, 0);
        },
        formatNumber(n) { return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },
        checkout() {
            if (this.cart.length === 0) return;
            alert('Transaction complete!\nTotal: Rp ' + this.formatNumber(this.total));
            this.cart = [];
        }
    };
}
</script>
<script defer src="https://unpkg.com/alpinejs@3/dist/cdn.min.js"></script>
@endpush
@endsection
