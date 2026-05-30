@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Product Management</h1>
            <p class="text-slate-500 text-sm mt-1">Manage your product catalog</p>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Product
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-4">
        <input type="search" placeholder="Search products..." class="flex-1 px-4 py-2.5 rounded-lg border border-slate-200">
        <select class="px-4 py-2.5 rounded-lg border border-slate-200 min-w-[140px]">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-6 py-4 text-left font-semibold">Product Name</th>
                        <th class="px-6 py-4 text-left font-semibold">Category</th>
                        <th class="px-6 py-4 text-right font-semibold">Current Stock</th>
                        <th class="px-6 py-4 text-right font-semibold">Purchase Price</th>
                        <th class="px-6 py-4 text-right font-semibold">Selling Price</th>
                        <th class="px-6 py-4 text-center font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $item)
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $item->name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $item->category->name ?? '-' }}</td>
                        
                        <td class="px-6 py-4 text-right @if($item->stock <= $item->min_stock) text-amber-600 font-semibold @else text-slate-700 @endif">
                            {{ $item->stock }} pcs
                        </td>
                        
                        <td class="px-6 py-4 text-right text-slate-600">Rp {{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right font-medium text-slate-800">Rp {{ number_format($item->selling_price, 0, ',', '.') }}</td>
                        
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('products.edit', $item->id) }}" class="p-2 rounded-lg text-sky-600 hover:bg-sky-50 inline-block">Edit</a>
                            <form action="{{ route('products.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk {{ $item->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 rounded-lg text-red-600 hover:bg-red-50">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    
                    @if($products->isEmpty())
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                            Belum ada data produk.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection