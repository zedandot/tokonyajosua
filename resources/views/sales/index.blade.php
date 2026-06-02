@extends('layouts.app')

@section('title', 'Sales / POS')

@section('content')
<div class="flex flex-col gap-6" x-data="posApp()">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Point of Sale</h1>
        <p class="text-slate-500 text-sm mt-1">Cashier interface — Transaction #TRX-{{ date('His') }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- AREA KIRI: Pencarian & Daftar Barang (LIST VIEW) --}}
        <div class="lg:col-span-2 flex flex-col bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            
            {{-- Bagian Atas: Pencarian & Filter Kategori --}}
            <div class="p-4 border-b border-slate-100 bg-white">
                <input type="search" x-model="search" placeholder="Masukkan nama barang atau ID..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-shadow bg-slate-50">
                
                {{-- Baris Tombol Filter Kategori --}}
                <div class="flex gap-2 overflow-x-auto pt-4 pb-1 hide-scrollbar" style="scrollbar-width: none;">
                    <button @click="selectedCategory = 'all'"
                            class="px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-all border"
                            :class="selectedCategory === 'all' ? 'bg-sky-500 text-white border-sky-500 shadow-md' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'">
                        Semua Kategori
                    </button>
                    @foreach($categories as $c)
                    <button @click="selectedCategory = '{{ $c->id }}'"
                            class="px-5 py-2 rounded-full text-sm font-semibold whitespace-nowrap transition-all border"
                            :class="selectedCategory === '{{ $c->id }}' ? 'bg-sky-500 text-white border-sky-500 shadow-md' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'">
                        {{ $c->name }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- 🟢 Header Kolom List --}}
            <div class="grid grid-cols-12 gap-2 px-5 py-3 bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider">
                <div class="col-span-6">Info Produk</div>
                <div class="col-span-3 text-right">Harga</div>
                <div class="col-span-3 text-right">Stok</div>
            </div>

            {{-- 🟢 AREA BAWAH: Daftar Produk (Gaya Mobile List) --}}
            <div class="overflow-y-auto max-h-[600px] divide-y divide-slate-100">
                @foreach($products as $p)
                <button 
                    x-show="showProduct(@js($p->name), @js($p->category_id ?? ''))" 
                    x-transition.opacity.duration.200ms
                    @click="addToCart('{{ $p->id }}', @js($p->name), {{ $p->selling_price }}, {{ $p->inventory->current_stock ?? 0 }})"
                    class="w-full grid grid-cols-12 gap-2 px-5 py-4 items-center hover:bg-sky-50 active:bg-sky-100 transition-colors text-left group bg-white">
                    
                    {{-- Info Produk (Kiri) --}}
                    <div class="col-span-6 flex flex-col pr-2">
                        <span class="text-sm font-bold text-slate-800 group-hover:text-sky-700 transition-colors">{{ $p->name }}</span>
                        <span class="text-[11px] text-slate-400 mt-1">{{ $p->category->name ?? 'Umum' }}</span>
                    </div>

                    {{-- Harga (Tengah) --}}
                    <div class="col-span-3 text-right">
                        <span class="text-sm font-bold text-sky-600">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</span>
                    </div>

                    {{-- Stok (Kanan) --}}
                    <div class="col-span-3 text-right">
                        <span class="text-sm font-bold"
                              :class="getRemainingStock('{{ $p->id }}', {{ $p->inventory->current_stock ?? 0 }}) > 0 ? 'text-emerald-500' : 'text-red-500'"
                              x-text="getRemainingStock('{{ $p->id }}', {{ $p->inventory->current_stock ?? 0 }}) + ' pcs'">
                        </span>
                    </div>
                </button>
                @endforeach

                {{-- Teks jika tidak ada barang --}}
                <div x-show="cart.length === -1" class="p-8 text-center text-slate-400 text-sm hidden">
                    Produk tidak ditemukan.
                </div>
            </div>
        </div>

        {{-- AREA KANAN: Cart Panel --}}
        <div class="flex flex-col bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden lg:sticky lg:top-6 lg:h-[calc(100vh-2rem)]">
            <div class="p-4 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h3 class="font-semibold text-slate-800">Shopping Cart</h3>
                <span class="bg-sky-100 text-sky-700 text-xs font-bold px-2.5 py-1 rounded-full" x-text="cart.length + ' Item'"></span>
            </div>
            <div class="flex-1 p-4 min-h-[180px] max-h-80 lg:max-h-none overflow-y-auto">
                <template x-if="cart.length === 0">
                    <div class="flex flex-col items-center justify-center h-full text-slate-400 py-10">
                        <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="text-sm font-medium">Keranjang masih kosong</p>
                        <p class="text-xs mt-1">Pilih barang dari daftar produk</p>
                    </div>
                </template>
                <div class="space-y-3" x-show="cart.length > 0">
                    <template x-for="(item, i) in cart" :key="item.id + '-' + i">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="min-w-0 mr-3 flex-1">
                                <p class="font-bold text-slate-800 text-sm truncate" x-text="item.name"></p>
                                <p class="text-xs text-sky-600 font-semibold mt-0.5">Rp <span x-text="formatNumber(item.price)"></span></p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0 bg-white border border-slate-200 rounded-lg p-1">
                                <button @click="updateQty(i, -1)" class="w-7 h-7 rounded-md hover:bg-slate-100 flex items-center justify-center text-slate-600 font-bold transition-colors">−</button>
                                <span class="w-5 text-center font-bold text-slate-800 text-sm" x-text="item.qty"></span>
                                <button @click="updateQty(i, 1)" class="w-7 h-7 rounded-md hover:bg-slate-100 flex items-center justify-center text-slate-600 font-bold transition-colors">+</button>
                            </div>
                            <button @click="removeItem(item.id)" class="w-8 h-8 rounded-lg hover:bg-red-100 text-red-500 flex items-center justify-center ml-2 transition-colors shrink-0" title="Hapus Barang">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
            
            {{-- Panel Total & Pay Now --}}
            <div class="p-5 border-t border-slate-100 bg-white">
                <div class="flex justify-between items-end mb-4">
                    <span class="font-medium text-slate-500">Total Pembayaran</span>
                    <span class="font-black text-slate-800 text-2xl tracking-tight">Rp <span x-text="formatNumber(total)"></span></span>
                </div>
                <button @click="checkout()"
                    class="w-full py-4 rounded-xl bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white font-bold text-lg transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none flex justify-center items-center gap-2"
                    :disabled="cart.length === 0">
                    <span>Pay Now</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function posApp() {
    return {
        search: '',
        selectedCategory: 'all',
        cart: [],
        
        showProduct(name, categoryId) {
            const matchCategory = this.selectedCategory === 'all' || this.selectedCategory == categoryId;
            const matchSearch = name.toLowerCase().includes(this.search.toLowerCase());
            return matchCategory && matchSearch;
        },

        getRemainingStock(id, initialStock) {
            const cartItem = this.cart.find(c => c.id === id);
            const qtyInCart = cartItem ? cartItem.qty : 0;
            return initialStock - qtyInCart;
        },

        addToCart(id, name, price, maxStock) {
            const idx = this.cart.findIndex(c => c.id === id);
            
            if (idx >= 0) {
                if (this.cart[idx].qty < maxStock) {
                    this.cart[idx].qty++;
                } else {
                    Swal.fire({ icon: 'warning', title: 'Stok Terbatas', text: `Sisa stok ${name} tidak mencukupi.` });
                }
            } else {
                if (maxStock > 0) {
                    this.cart.push({ id, name, price, qty: 1, maxStock });
                } else {
                    Swal.fire({ icon: 'error', title: 'Stok Kosong', text: `Stok ${name} sedang kosong!` });
                }
            }
        },

        updateQty(i, delta) {
            const newQty = this.cart[i].qty + delta;
            if (newQty > this.cart[i].maxStock) {
                Swal.fire({ icon: 'warning', title: 'Batas Maksimal', text: 'Mencapai batas maksimal stok gudang.' });
                return;
            }
            this.cart[i].qty = newQty;
            if (this.cart[i].qty <= 0) this.cart.splice(i, 1);
        },

        removeItem(id) { 
            this.cart = this.cart.filter(c => c.id !== id);
        },
        get total() {
            return this.cart.reduce((s, c) => s + (c.price * c.qty), 0);
        },
        formatNumber(n) { 
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); 
        },
        
        async checkout() {
            if (this.cart.length === 0) return;
            
            const btn = document.querySelector('button[disabled]') || document.querySelector('button');
            const originalText = btn.innerHTML;
            
            Swal.fire({
                title: 'Menyiapkan Pembayaran...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            btn.disabled = true;

            try {
                const response = await fetch('{{ route('sales.checkout_session') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        total: this.total,
                        items: this.cart
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    window.location.href = result.redirect;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Memproses!',
                        text: result.message || 'Terjadi kesalahan saat membuat sesi pembayaran.',
                        confirmButtonColor: '#ef4444'
                    });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan!',
                    text: 'Gagal terhubung ke server.',
                    confirmButtonColor: '#ef4444'
                });
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }
    };
}
</script>
@endpush
@endsection