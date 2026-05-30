@extends('layouts.app')

@section('title', 'Tambah Produk')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Tambah Produk Baru</h1>
        <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden p-6 md:p-8">
        <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Produk</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none" placeholder="Masukkan nama produk">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                    <select name="category_id" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Stok Awal</label>
                    <input type="number" name="stock" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="0">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Modal (Rp)</label>
                    <input type="number" name="purchase_price" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="Contoh: 10000">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Jual (Rp)</label>
                    <input type="number" name="selling_price" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="Contoh: 15000">
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Batas Minimal Stok (Peringatan)</label>
                    <input type="number" name="min_stock" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="Contoh: 5">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection