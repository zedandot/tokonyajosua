@extends('layouts.app')

@section('title', 'Edit Produk')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Edit Data Produk</h1>
        <a href="{{ route('products.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden p-6 md:p-8">
        <form action="{{ route('products.update', $product->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Nama Produk</label>
                    <input type="text" name="name" value="{{ $product->name }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                    <select name="category_id" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Stok</label>
                    <input type="number" name="stock" value="{{ $product->stock }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Modal (Rp)</label>
                    <input type="number" name="purchase_price" value="{{ $product->purchase_price }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Harga Jual (Rp)</label>
                    <input type="number" name="selling_price" value="{{ $product->selling_price }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none">
                </div>

                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Batas Minimal Stok (Peringatan)</label>
                    <input type="number" name="min_stock" value="{{ $product->min_stock }}" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors">
                    Perbarui Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection