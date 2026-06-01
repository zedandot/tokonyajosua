@extends('layouts.app')

@section('title', 'Payment Checkout')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="paymentApp()">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Checkout Pembayaran</h1>
            <p class="text-slate-500 text-sm mt-1">Selesaikan transaksi pelanggan</p>
        </div>
        <a href="{{ route('sales.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm transition-colors">
            &larr; Kembali ke POS
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden flex flex-col md:flex-row">
        {{-- Kolom Kiri: Ringkasan Pesanan --}}
        <div class="md:w-1/2 bg-slate-50 p-6 border-b md:border-b-0 md:border-r border-slate-100 flex flex-col">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                Ringkasan Pesanan
            </h3>
            <div class="space-y-3 flex-1 overflow-y-auto pr-2 mb-6">
                @foreach($cart as $item)
                <div class="flex justify-between text-sm items-center p-2 rounded hover:bg-slate-100 transition-colors">
                    <span class="text-slate-700 font-medium">{{ $item['name'] }} <span class="text-slate-400 ml-1">x{{ $item['qty'] }}</span></span>
                    <span class="font-medium text-slate-800">Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="pt-4 border-t border-slate-200 mt-auto">
                <div class="flex justify-between items-center text-lg">
                    <span class="font-medium text-slate-700">Total Tagihan</span>
                    <span class="font-bold text-sky-600 text-2xl">Rp <span x-text="formatNumber(total)"></span></span>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Form Pembayaran --}}
        <div class="md:w-1/2 p-6 md:p-8 flex flex-col">
            <h3 class="font-semibold text-slate-800 mb-4">Metode Pembayaran</h3>

            {{-- Pilihan Metode Pembayaran (Tunai / QRIS) --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <label class="cursor-pointer">
                    <input type="radio" x-model="paymentMethod" value="tunai" class="peer sr-only">
                    <div class="rounded-xl border-2 border-slate-200 p-4 text-center peer-checked:border-sky-500 peer-checked:bg-sky-50 hover:bg-slate-50 transition-all flex flex-col items-center justify-center min-h-[100px]">
                        {{-- Ikon Uang Tunai Klasik / Familiar --}}
                        <svg class="w-10 h-10 text-emerald-600 mb-2" fill="currentColor" viewBox="0 0 576 512">
                            <path d="M64 64C28.7 64 0 92.7 0 128V384c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H64zm64 320H64V320c35.3 0 64 28.7 64 64zM64 192V128h64c0 35.3-28.7 64-64 64zM448 384c0-35.3 28.7-64 64-64v64H448zm64-192c-35.3 0-64-28.7-64-64h64v64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/>
                        </svg>
                        <div class="font-semibold text-slate-700">Tunai</div>
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" x-model="paymentMethod" value="qris" class="peer sr-only">
                    <div class="rounded-xl border-2 border-slate-200 p-4 text-center peer-checked:border-sky-500 peer-checked:bg-sky-50 hover:bg-slate-50 transition-all flex flex-col items-center justify-center min-h-[100px]">
                        {{-- Logo Resmi QRIS --}}
                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg" alt="QRIS" class="h-8 mb-2">
                        <div class="font-semibold text-slate-700">QRIS</div>
                    </div>
                </label>
            </div>

            <div class="space-y-6 flex-1">
                
                {{-- TAMPILAN JIKA TUNAI --}}
                <div x-show="paymentMethod === 'tunai'" x-transition>
                    {{-- Input Uang Tunai --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Uang Tunai (Cash)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-semibold text-lg">Rp</span>
                            <input type="text" x-model="cashInput" @input="formatCash" :autofocus="paymentMethod === 'tunai'"
                                class="w-full pl-14 pr-4 py-4 text-2xl font-bold text-slate-800 rounded-xl border-2 border-slate-200 focus:ring-4 focus:ring-sky-100 focus:border-sky-500 transition-all shadow-sm"
                                placeholder="0">
                        </div>
                    </div>

                    {{-- Indikator Kembalian --}}
                    <div class="p-5 rounded-xl transition-colors duration-300" 
                         :class="change >= 0 ? 'bg-emerald-50 border border-emerald-100' : 'bg-red-50 border border-red-100'">
                        <span class="block text-sm font-medium mb-1" :class="change >= 0 ? 'text-emerald-700' : 'text-red-700'">Kembalian</span>
                        <span class="block text-3xl font-bold tracking-tight" :class="change >= 0 ? 'text-emerald-600' : 'text-red-600'">
                            Rp <span x-text="formatNumber(Math.abs(change))"></span>
                        </span>
                        <p x-show="change < 0" class="text-sm text-red-500 mt-2 font-medium flex items-center gap-1" style="display: none;">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            Uang pelanggan kurang!
                        </p>
                    </div>
                </div>

                {{-- TAMPILAN JIKA QRIS --}}
                <div x-show="paymentMethod === 'qris'" style="display: none;" x-transition class="text-center py-4">
                    <div class="inline-block p-4 bg-white border-2 border-dashed border-slate-300 rounded-2xl mb-4">
                        {{-- Ikon Placeholder QRIS --}}
                        <svg class="w-24 h-24 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <p class="text-slate-600 text-sm font-medium">Arahkan pelanggan untuk scan kode QRIS.</p>
                    <p class="text-slate-500 text-xs mt-1">Pastikan nominal transfer sesuai tagihan.</p>
                </div>

            </div>

            {{-- Tombol Eksekusi --}}
            <div class="pt-6 mt-auto">
                <button @click="processPayment()" :disabled="!isReadyToPay"
                    class="w-full py-4 rounded-xl bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white font-bold text-lg transition-all shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none flex items-center justify-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Selesaikan Pembayaran
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function paymentApp() {
    return {
        total: {{ $total }},
        cart: @json($cart),
        paymentMethod: 'tunai', // Default aktif
        cashInput: '',
        cashRaw: 0,

        get change() {
            if (this.cashRaw === 0) return 0;
            return this.cashRaw - this.total;
        },

        get isReadyToPay() {
            if (this.paymentMethod === 'qris') return true; // QRIS selalu siap ditekan
            if (this.paymentMethod === 'tunai') return this.change >= 0 && this.cashRaw > 0;
            return false;
        },

        formatCash() {
            let val = this.cashInput.replace(/[^0-9]/g, '');
            this.cashRaw = parseInt(val) || 0;
            this.cashInput = this.formatNumber(this.cashRaw);
        },

        formatNumber(n) {
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        async processPayment() {
            if (!this.isReadyToPay) return;

            const btn = document.querySelector('button[disabled]') || document.querySelector('button');
            const originalText = btn.innerHTML;

            Swal.fire({
                title: 'Mencatat Transaksi...',
                text: 'Menghubungkan ke database gudang...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
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
                        items: this.cart,
                        payment_method: this.paymentMethod, 
                        paid: this.paymentMethod === 'tunai' ? this.cashRaw : this.total
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    let htmlDetail = '';
                    if (this.paymentMethod === 'tunai') {
                        htmlDetail = `<div class="text-left mt-4 text-sm">
                                        <p class="mb-1">Metode : <b>Tunai</b></p>
                                        <p class="mb-1">Total Tagihan : <b>Rp ${this.formatNumber(this.total)}</b></p>
                                        <p class="mb-1">Uang Tunai : <b>Rp ${this.formatNumber(this.cashRaw)}</b></p>
                                        <hr class="my-2 border-slate-200">
                                        <p class="text-lg text-emerald-600">Kembalian : <b>Rp ${this.formatNumber(this.change)}</b></p>
                                       </div>`;
                    } else {
                        htmlDetail = `<div class="text-left mt-4 text-sm">
                                        <p class="mb-1">Metode : <b>QRIS</b></p>
                                        <p class="mb-1">Total Tagihan : <b>Rp ${this.formatNumber(this.total)}</b></p>
                                        <hr class="my-2 border-slate-200">
                                        <p class="text-lg text-emerald-600">Status : <b>Lunas</b></p>
                                       </div>`;
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Transaksi Berhasil!',
                        html: htmlDetail,
                        confirmButtonText: 'Tutup & Transaksi Baru',
                        confirmButtonColor: '#0ea5e9'
                    }).then(() => {
                        window.location.href = '{{ route('sales.index') }}';
                    });
                } else {
                    Swal.fire('Error', result.message || 'Gagal menyimpan transaksi.', 'error');
                    btn.disabled = false;
                }
            } catch (error) {
                Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
                btn.disabled = false;
            }
        }
    }
}
</script>
@endpush
@endsection