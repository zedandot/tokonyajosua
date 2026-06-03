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
                    {{-- Menggunakan grand_total atau total_amount --}}
                    <p class="text-2xl font-bold text-slate-800 mt-1">
                        Rp {{ number_format(\App\Models\Sale::whereDate('created_at', \Carbon\Carbon::today())->get()->sum(fn($s) => $s->grand_total ?? $s->total_amount ?? 0), 0, ',', '.') }}
                    </p>
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
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ \App\Models\Sale::whereDate('created_at', \Carbon\Carbon::today())->count() }}</p>
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
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ \App\Models\Product::count() }}</p>
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
                <p class="text-sky-100 mt-2">Buka POS untuk mencatat barang yang dibeli pelanggan.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-white text-sky-600 font-semibold hover:bg-sky-50 transition-colors shrink-0">
                Buka POS
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Produk dengan Stok Tersedia</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($products as $product)
                <div class="flex justify-between items-center p-2 rounded-lg hover:bg-slate-50">
                    <span class="font-medium text-slate-700 text-sm">{{ $product->name }}</span>
                    <span class="text-sm {{ $product->inventory->current_stock <= 5 ? 'text-amber-600 font-semibold' : 'text-slate-500' }}">
                        {{ $product->inventory->current_stock }} pcs
                    </span>
                </div>
                @empty
                    <p class="text-sm text-slate-500">Tidak ada produk tersedia.</p>
                @endforelse
            </div>
            <a href="{{ route('products.index') }}" class="block mt-4 text-sm text-sky-600 hover:underline">Lihat semua produk →</a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Transaksi Terakhir</h3>
            <div class="space-y-2">
                @forelse(\App\Models\Sale::latest()->take(5)->get() as $t)
                <div class="flex justify-between items-center p-2 rounded-lg hover:bg-slate-50">
                    {{-- Menampilkan 6 karakter terakhir dari ID MongoDB agar rapi --}}
                    <span class="font-mono text-slate-700 text-sm">TRX-{{ substr($t->id, -6) }}</span>
                    {{-- Mencari kolom grand_total, jika kosong cari total_amount, jika kosong tampilkan 0 --}}
                    <span class="font-medium text-slate-800">
                        Rp {{ number_format($t->grand_total ?? $t->total_amount ?? 0, 0, ',', '.') }}
                    </span>
                </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection