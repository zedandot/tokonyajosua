@extends('layouts.app')

@section('title', 'Payment Checkout')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="paymentApp()">

    {{-- ── Header ─────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Checkout Pembayaran</h1>
            <p class="text-slate-500 text-sm mt-1">Selesaikan transaksi pelanggan</p>
        </div>
        <a href="{{ route('sales.index') }}"
           class="text-slate-500 hover:text-slate-700 font-medium text-sm transition-colors">
            &larr; Kembali ke POS
        </a>
    </div>

    {{-- ── Card Utama ──────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden flex flex-col md:flex-row">

        {{-- ═══════════════════════════════════════
             Kolom Kiri: Ringkasan Pesanan
        ════════════════════════════════════════ --}}
        <div class="md:w-1/2 bg-slate-50 p-6 border-b md:border-b-0 md:border-r border-slate-100 flex flex-col">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
                Ringkasan Pesanan
            </h3>

            {{-- Daftar Item --}}
            <div class="space-y-2 flex-1 overflow-y-auto pr-1 mb-6 max-h-64">
                @foreach($cart as $item)
                <div class="flex justify-between text-sm items-center p-2 rounded-lg hover:bg-slate-100 transition-colors">
                    <span class="text-slate-700 font-medium">
                        {{ $item['name'] }}
                        <span class="text-slate-400 ml-1">×{{ $item['qty'] }}</span>
                    </span>
                    <span class="font-semibold text-slate-800">
                        Rp {{ number_format($item['price'] * $item['qty'], 0, ',', '.') }}
                    </span>
                </div>
                @endforeach
            </div>

            {{-- Total --}}
            <div class="pt-4 border-t border-slate-200 mt-auto">
                <div class="flex justify-between items-center">
                    <span class="font-medium text-slate-600">Total Tagihan</span>
                    <span class="font-extrabold text-sky-600 text-2xl tracking-tight">
                        Rp <span x-text="formatNumber(total)"></span>
                    </span>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════
             Kolom Kanan: Form Pembayaran
        ════════════════════════════════════════ --}}
        <div class="md:w-1/2 p-6 md:p-8 flex flex-col">
            <h3 class="font-semibold text-slate-800 mb-4">Metode Pembayaran</h3>

            {{-- Pilihan Metode --}}
            <div class="grid grid-cols-2 gap-3 mb-6">

                {{-- Tunai --}}
                <label class="cursor-pointer">
                    <input type="radio" x-model="paymentMethod" value="cash" class="peer sr-only">
                    <div class="rounded-xl border-2 border-slate-200 p-4 text-center
                                peer-checked:border-sky-500 peer-checked:bg-sky-50
                                hover:bg-slate-50 transition-all
                                flex flex-col items-center justify-center min-h-[90px]">
                        <svg class="w-9 h-9 text-emerald-600 mb-1.5" fill="currentColor" viewBox="0 0 576 512">
                            <path d="M64 64C28.7 64 0 92.7 0 128V384c0 35.3 28.7 64 64 64H512c35.3 0 64-28.7 64-64V128
                                     c0-35.3-28.7-64-64-64H64zm64 320H64V320c35.3 0 64 28.7 64 64zM64 192V128h64
                                     c0 35.3-28.7 64-64 64zM448 384c0-35.3 28.7-64 64-64v64H448zm64-192
                                     c-35.3 0-64-28.7-64-64h64v64zM288 160a96 96 0 1 1 0 192 96 96 0 1 1 0-192z"/>
                        </svg>
                        <div class="font-semibold text-slate-700 text-sm">Tunai</div>
                    </div>
                </label>

                {{-- QRIS --}}
                <label class="cursor-pointer">
                    <input type="radio" x-model="paymentMethod" value="qris" class="peer sr-only">
                    <div class="rounded-xl border-2 border-slate-200 p-4 text-center
                                peer-checked:border-sky-500 peer-checked:bg-sky-50
                                hover:bg-slate-50 transition-all
                                flex flex-col items-center justify-center min-h-[90px]">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a2/Logo_QRIS.svg"
                             alt="QRIS" class="h-8 mb-1.5">
                        <div class="font-semibold text-slate-700 text-sm">QRIS</div>
                    </div>
                </label>
            </div>

            {{-- ─────────────────────────────────────────
                 Panel TUNAI (Cash)
            ────────────────────────────────────────── --}}
            <div x-show="paymentMethod === 'cash'" x-transition class="space-y-4 flex-1">

                {{-- Input Uang Tunai --}}
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Uang Diterima
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 font-semibold text-lg">Rp</span>
                        <input type="text"
                               id="cash-input"
                               x-model="cashInput"
                               @input="formatCash"
                               :autofocus="paymentMethod === 'cash'"
                               class="w-full pl-14 pr-4 py-4 text-2xl font-bold text-slate-800 rounded-xl
                                      border-2 border-slate-200 focus:ring-4 focus:ring-sky-100
                                      focus:border-sky-500 transition-all shadow-sm"
                               placeholder="0">
                    </div>
                </div>

                {{-- Indikator Kembalian --}}
                <div class="p-5 rounded-xl transition-colors duration-300"
                     :class="change >= 0 ? 'bg-emerald-50 border border-emerald-100'
                                         : 'bg-red-50 border border-red-100'">
                    <span class="block text-sm font-medium mb-1"
                          :class="change >= 0 ? 'text-emerald-700' : 'text-red-700'">
                        Kembalian
                    </span>
                    <span class="block text-3xl font-bold tracking-tight"
                          :class="change >= 0 ? 'text-emerald-600' : 'text-red-600'">
                        Rp <span x-text="formatNumber(Math.abs(change))"></span>
                    </span>
                    <p x-show="change < 0"
                       class="text-sm text-red-500 mt-2 font-medium flex items-center gap-1"
                       style="display:none;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667
                                     1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34
                                     16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Uang pelanggan kurang!
                    </p>
                </div>
            </div>

            {{-- ─────────────────────────────────────────
                 Panel QRIS (Statis / Manual)
            ────────────────────────────────────────── --}}
            <div x-show="paymentMethod === 'qris'" x-transition class="flex-1 flex flex-col gap-4"
                 style="display:none;">

                {{-- Nominal Tagihan --}}
                <div class="bg-sky-50 border border-sky-200 rounded-xl px-5 py-4 flex items-center justify-between">
                    <span class="text-sm font-semibold text-sky-700">Total yang harus dibayar:</span>
                    <span class="text-xl font-extrabold text-sky-700 tracking-tight">
                        Rp <span x-text="formatNumber(total)"></span>
                    </span>
                </div>

                {{-- QRIS Info Banner --}}
                <div class="border border-slate-100 bg-slate-50/50 rounded-xl p-5 flex flex-col items-center text-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-sky-100 flex items-center justify-center text-sky-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01
                                     M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5
                                     a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2
                                     a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5
                                     a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-700 text-sm">Metode Pembayaran QRIS</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-[280px] leading-relaxed">
                            Tekan tombol di bawah untuk menampilkan QR code statis toko kepada pelanggan.
                        </p>
                    </div>
                </div>

                {{-- Panduan Singkat --}}
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-xs space-y-1.5">
                    <p class="font-semibold text-amber-800 flex items-center gap-1.5">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Panduan Kasir
                    </p>
                    <ul class="list-disc list-inside text-amber-700 space-y-1 pl-1">
                        <li>Tampilkan QRIS di layar agar di-scan pelanggan.</li>
                        <li>Minta pelanggan memasukkan nominal yang tepat.</li>
                        <li>Verifikasi mutasi masuk secara manual sebelum menyelesaikan transaksi.</li>
                    </ul>
                </div>
            </div>

            {{-- ─────────────────────────────────────────
                 Tombol Pembayaran (Selesaikan / Tampilkan QRIS)
            ────────────────────────────────────────── --}}
            <div class="pt-6 mt-auto">
                <button id="btn-pay"
                        x-show="paymentMethod === 'cash'"
                        @click="processPayment()"
                        :disabled="!isReadyToPay"
                        class="w-full py-4 rounded-xl font-bold text-lg transition-all shadow-md
                               flex items-center justify-center gap-2
                               bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white
                               hover:shadow-lg
                               disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:shadow-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Selesaikan Pembayaran</span>
                </button>

                <button id="btn-show-qris"
                        x-show="paymentMethod === 'qris'"
                        @click="showQrisModal = true"
                        class="w-full py-4 rounded-xl font-bold text-lg transition-all shadow-md
                               flex items-center justify-center gap-2
                               bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white
                               hover:shadow-lg"
                        style="display: none;">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01
                                 M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5
                                 a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2
                                 a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5
                                 a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span>Tampilkan QRIS</span>
                </button>
            </div>
        </div>
    </div>

    <div id="qrisModal"
         x-show="showQrisModal"
         :class="showQrisModal ? 'flex' : 'hidden'"
         class="fixed inset-0 z-50 items-center justify-center bg-black/50 px-4"
         x-cloak
         style="display: none;">
        
        <div class="fixed inset-0 bg-transparent transition-opacity" @click="showQrisModal = false"></div>

        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl z-10 flex flex-col items-center">
             
             <h3 class="text-lg font-bold text-slate-800 mb-2">Scan QRIS Hub SIMTOKO</h3>
             
             <div class="bg-sky-50 text-sky-700 font-extrabold text-xl py-2 px-4 rounded-lg mb-4 inline-block">
                 Total: Rp <span x-text="formatNumber(total)"></span>
             </div>

             <div class="mb-5 w-full flex justify-center">
                 <img src="{{ asset('images/qris.jpg') }}"
                      class="mx-auto w-full max-w-[260px] max-h-[360px] object-contain rounded-xl border-2 border-slate-200 shadow-sm"
                      alt="QRIS Nabil Grocery">
             </div>

             <p class="text-xs text-slate-500 mb-4 flex items-center justify-center gap-1.5 animate-pulse">
                 <svg class="animate-spin h-3.5 w-3.5 text-sky-500" fill="none" viewBox="0 0 24 24">
                     <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                     <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                 </svg>
                 Menunggu pembayaran dikonfirmasi kasir...
             </p>

             <div class="mt-5 flex items-center gap-3 w-full">
                 <button type="button"
                         @click="showQrisModal = false"
                         class="flex-1 py-3 px-4 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 transition-colors">
                     Batal
                 </button>
                 <button type="button"
                         @click="showQrisModal = false; processPayment()"
                         class="flex-1 py-3 px-4 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold shadow-sm transition-colors">
                     Pembayaran Selesai
                 </button>
             </div>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
function paymentApp() {
    return {
        total: {{ $total }},
        cart: @json($cart),
        paymentMethod: 'cash',
        cashInput: '',
        cashRaw: 0,
        showQrisModal: false,

        // ── Kembalian (hanya relevan untuk Cash) ──
        get change() {
            if (this.paymentMethod !== 'cash') return 0;
            if (this.cashRaw === 0) return 0;
            return this.cashRaw - this.total;
        },

        // ── Kondisi siap bayar ─────────────────────
        get isReadyToPay() {
            if (this.paymentMethod === 'cash') {
                return this.cashRaw > 0 && this.change >= 0;
            }
            if (this.paymentMethod === 'qris') {
                return true;
            }
            return false;
        },

        // ── Format input Cash ──────────────────────
        formatCash() {
            let val = this.cashInput.replace(/[^0-9]/g, '');
            this.cashRaw = parseInt(val) || 0;
            this.cashInput = this.cashRaw > 0 ? this.formatNumber(this.cashRaw) : '';
        },

        // ── Format angka ribuan ────────────────────
        formatNumber(n) {
            return Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        },

        // ── Kirim transaksi ke server ──────────────
        async processPayment() {
            if (!this.isReadyToPay) return;

            const btn = document.getElementById('btn-pay');
            btn.disabled = true;

            Swal.fire({
                title: 'Mencatat Transaksi...',
                text: 'Mohon tunggu sebentar...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                const payload = {
                    total: this.total,
                    items: this.cart,
                    payment_method: this.paymentMethod,
                    money_received: this.paymentMethod === 'cash' ? this.cashRaw : 0,
                };

                const response = await fetch('{{ route('sales.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    // ── Sukses: tampilkan struk ringkas ──
                    let htmlDetail = '';

                    if (this.paymentMethod === 'cash') {
                        htmlDetail = `
                            <div class="text-left mt-4 text-sm space-y-1">
                                <p>Metode &nbsp;&nbsp;&nbsp;: <b>Tunai</b></p>
                                <p>Total &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b>Rp ${this.formatNumber(this.total)}</b></p>
                                <p>Diterima &nbsp;: <b>Rp ${this.formatNumber(this.cashRaw)}</b></p>
                                <hr class="my-2 border-slate-200">
                                <p class="text-base text-emerald-600">
                                    Kembalian : <b>Rp ${this.formatNumber(result.change_amount ?? this.change)}</b>
                                </p>
                            </div>`;
                    } else {
                        htmlDetail = `
                            <div class="text-left mt-4 text-sm space-y-1">
                                <p>Metode &nbsp;&nbsp;&nbsp;: <b>QRIS</b></p>
                                <p>Total &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b>Rp ${this.formatNumber(this.total)}</b></p>
                                <p>Kembalian : <b>—</b></p>
                                <hr class="my-2 border-slate-200">
                                <p class="text-base text-emerald-600">Status : <b>Lunas ✓</b></p>
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
                    Swal.fire('Gagal', result.message || 'Transaksi tidak berhasil disimpan.', 'error');
                    btn.disabled = false;
                }

            } catch (err) {
                Swal.fire('Error', 'Gagal terhubung ke server. Periksa koneksi.', 'error');
                btn.disabled = false;
            }
        }
    };
}
</script>
@endpush