@extends('layouts.app')

@section('title', 'Sales / POS')

@section('content')
<div class="flex flex-col gap-6" x-data="posApp()">
    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Point of Sale</h1>
        <p class="text-slate-500 text-sm mt-1">Cashier interface — Transaction #TRX-{{ date('His') }}</p>
    </div>

    {{-- POS Grid: stacks on mobile, side-by-side on lg+ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Product Search & Grid --}}
        <div class="lg:col-span-2 flex flex-col bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-4 border-b border-slate-100">
                <input type="search" x-model="search" placeholder="Search products..."
                    class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                   @foreach($products as $p)
                    <button @click="addToCart('{{ $p->id }}', '{{ addslashes($p->name) }}', {{ $p->selling_price }}, {{ $p->inventory->current_stock ?? 0 }})"
                        class="flex flex-col items-center p-3 rounded-lg border border-slate-200 hover:border-sky-400 hover:bg-sky-50 active:scale-95 transition-all text-left">
                        <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center mb-2">
                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="text-xs sm:text-sm font-medium text-slate-700 truncate w-full text-center">{{ $p->name }}</span>
                        <span class="text-xs text-sky-600 font-semibold mt-0.5">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</span>
                        <span class="text-[10px] mt-1 font-medium" 
                              :class="getRemainingStock('{{ $p->id }}', {{ $p->inventory->current_stock ?? 0 }}) === 0 ? 'text-red-500' : 'text-slate-400'"
                              x-text="'Stok: ' + getRemainingStock('{{ $p->id }}', {{ $p->inventory->current_stock ?? 0 }})">
                        </span>
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
            <div class="flex-1 p-4 min-h-[180px] max-h-80 lg:max-h-none overflow-y-auto">
                <template x-if="cart.length === 0">
                    <div class="flex flex-col items-center justify-center h-40 text-slate-400">
                        <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <p class="text-sm">Cart is empty</p>
                        <p class="text-xs mt-1">Click products to add</p>
                    </div>
                </template>
                <div class="space-y-2" x-show="cart.length > 0">
                    <template x-for="(item, i) in cart" :key="item.id + '-' + i">
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50">
                            <div class="min-w-0 mr-2">
                                <p class="font-medium text-slate-700 text-sm truncate" x-text="item.name"></p>
                                <p class="text-xs text-slate-500">Rp <span x-text="formatNumber(item.price)"></span> × <span x-text="item.qty"></span></p>
                            </div>
                            <div class="flex items-center gap-1 shrink-0">
                                <button @click="updateQty(i, -1)" class="w-7 h-7 rounded bg-slate-200 hover:bg-slate-300 flex items-center justify-center text-slate-600 font-bold">−</button>
                                <span class="w-6 text-center font-medium text-slate-700 text-sm" x-text="item.qty"></span>
                                <button @click="updateQty(i, 1)" class="w-7 h-7 rounded bg-slate-200 hover:bg-slate-300 flex items-center justify-center text-slate-600 font-bold">+</button>
                                <button @click="removeItem(item.id)" class="w-7 h-7 rounded bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center ml-1 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100 space-y-3">
                <div class="flex justify-between items-center text-lg">
                    <span class="font-medium text-slate-700">Total</span>
                    <span class="font-bold text-slate-800">Rp <span x-text="formatNumber(total)"></span></span>
                </div>
                <button @click="checkout()"
                    class="w-full py-3 rounded-lg bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white font-semibold transition-colors disabled:opacity-50"
                    :disabled="cart.length === 0">
                    Pay Now
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- Memanggil CDN SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function posApp() {
    return {
        search: '',
        cart: [],
        
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
                    // 🟢 Ubah alert biasa jadi SweetAlert
                    Swal.fire({ icon: 'warning', title: 'Stok Terbatas', text: `Sisa stok ${name} tidak mencukupi.` });
                }
            } else {
                if (maxStock > 0) {
                    this.cart.push({ id, name, price, qty: 1, maxStock });
                } else {
                    // 🟢 Ubah alert biasa jadi SweetAlert
                    Swal.fire({ icon: 'error', title: 'Stok Kosong', text: `Stok ${name} sedang kosong!` });
                }
            }
        },

        updateQty(i, delta) {
            const newQty = this.cart[i].qty + delta;
            if (newQty > this.cart[i].maxStock) {
                // 🟢 Ubah alert biasa jadi SweetAlert
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
            
            // 🟢 Tampilkan Loading SweetAlert
            Swal.fire({
                title: 'Memproses Transaksi...',
                text: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            btn.disabled = true;

            try {
                const response = await fetch('{{ route('sales.store') }}', {
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
                    // 🟢 Transaksi Sukses dengan SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        text: 'Total Pembayaran: Rp ' + this.formatNumber(this.total),
                        confirmButtonText: 'Tutup & Lanjutkan',
                        confirmButtonColor: '#0ea5e9' // sky-500
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.cart = [];
                            window.location.reload(); 
                        }
                    });
                } else {
                    // 🟢 Transaksi Gagal (Validasi Server)
                    Swal.fire({
                        icon: 'error',
                        title: 'Transaksi Gagal!',
                        text: result.message || 'Terjadi kesalahan tidak diketahui.',
                        confirmButtonColor: '#ef4444' // red-500
                    });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            } catch (error) {
                // 🟢 Gagal Koneksi
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Jaringan!',
                    text: 'Gagal terhubung ke server. Periksa koneksi Anda.',
                    confirmButtonColor: '#ef4444' // red-500
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