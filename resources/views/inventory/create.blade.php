@extends('layouts.app')

@section('title', 'Record Incoming Stock')

@section('content')
<div class="space-y-6 max-w-2xl mx-auto">
    <div class="flex items-center justify-between">
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Record Incoming Stock</h1>
        <a href="{{ route('inventory.index') }}" class="text-slate-500 hover:text-slate-700 font-medium text-sm">&larr; Kembali</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden p-6 md:p-8">
        <form action="{{ route('inventory.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Pilih Produk</label>
                <select name="product_id" required class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 focus:ring-1 focus:ring-sky-500 outline-none">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($products as $product)
                        {{-- 🚀 PERBAIKAN: Mengambil data stok dari relasi tabel inventory --}}
                        <option value="{{ $product->id }}">
                            {{ $product->sku }} | {{ $product->name }} (Sisa: {{ $product->inventory->current_stock ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Jumlah Masuk (Qty)</label>
                <input type="number" name="quantity" required min="1" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="Contoh: 50">
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Catatan (Opsional)</label>
                <input type="text" name="notes" class="w-full px-4 py-2.5 rounded-lg border border-slate-200 focus:border-sky-500 outline-none" placeholder="Contoh: Restock dari Supplier A">
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors">
                    Simpan Stok Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection