@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Dashboard Kasir</h1>
        <p class="text-slate-500 mt-1">Input transaksi penjualan dan pencatatan barang yang dibeli pelanggan</p>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Penjualan Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp 2.450.000</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Transaksi Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">47</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Produk Tersedia</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">248</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- CTA ke POS --}}
    <div class="bg-gradient-to-r from-sky-500 to-sky-600 rounded-xl shadow-lg p-8 text-white">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <h2 class="text-xl font-bold">Mulai Transaksi Baru</h2>
                <p class="text-sky-100 mt-2">Buka POS untuk mencatat barang yang dibeli pelanggan dan menyelesaikan pembayaran.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-white text-sky-600 font-semibold hover:bg-sky-50 transition-colors shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Buka POS
            </a>
        </div>
    </div>

    {{-- Lihat Data Barang & Stok --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Produk dengan Stok Tersedia</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @php $items = [['name' => 'Kopi Sachet Premium', 'stock' => 120], ['name' => 'Susu UHT 1L', 'stock' => 45], ['name' => 'Mie Instan', 'stock' => 200], ['name' => 'Snack Keripik', 'stock' => 85], ['name' => 'Aqua Gelas', 'stock' => 500], ['name' => 'Teh Botol 350ml', 'stock' => 5], ['name' => 'Roti Tawar', 'stock' => 3]]; @endphp
                @foreach($items as $i)
                <div class="flex justify-between items-center p-2 rounded-lg hover:bg-slate-50">
                    <span class="font-medium text-slate-700 text-sm">{{ $i['name'] }}</span>
                    <span class="text-sm {{ $i['stock'] <= 10 ? 'text-amber-600 font-semibold' : 'text-slate-500' }}">{{ $i['stock'] }} pcs</span>
                </div>
                @endforeach
            </div>
            <a href="{{ route('products.index') }}" class="block mt-4 text-sm text-sky-600 hover:underline">Lihat semua produk →</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Transaksi Terakhir</h3>
            <div class="space-y-2">
                @foreach([['id' => 'TRX-2847', 'total' => 97500], ['id' => 'TRX-2846', 'total' => 45000], ['id' => 'TRX-2845', 'total' => 125000]] as $t)
                <div class="flex justify-between items-center p-2 rounded-lg hover:bg-slate-50">
                    <span class="font-mono text-slate-700 text-sm">{{ $t['id'] }}</span>
                    <span class="font-medium text-slate-800">Rp {{ number_format($t['total'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
