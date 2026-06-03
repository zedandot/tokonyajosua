@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Inventory / Warehouse</h1>
            <p class="text-slate-500 text-sm mt-1">Manage stock and incoming goods</p>
        </div>
        
        <a href="{{ route('inventory.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Incoming
        </a>
    </div>

    @if(session('success'))
    <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 border border-green-200">
        {{ session('success') }}
    </div>
    @endif

    {{-- Inventory Stock Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100 bg-slate-50/50">Current Stock</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-100">
                        <th class="px-6 py-4 text-left font-semibold">Product Name</th>
                        <th class="px-6 py-4 text-left font-semibold">SKU / Kode</th>
                        <th class="px-6 py-4 text-right font-semibold">Current Stock</th>
                        <th class="px-6 py-4 text-right font-semibold">Min. Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr class="border-t border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-800">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $product->sku ?? '-' }}</td>
                        
                        {{-- 🟢 PERBAIKAN: Logika Stok Terkoneksi ke Relasi Inventory --}}
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            @php
                                $stock = $product->inventory->current_stock ?? 0;
                                $min = $product->inventory->minimum_stock ?? 0;
                                $isLow = $stock <= $min;
                            @endphp
                            
                            @if($stock === 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                    Habis
                                </span>
                            @else
                                <span class="{{ $isLow ? 'text-amber-600 font-black bg-amber-50 px-2 py-0.5 rounded border border-amber-200' : 'text-slate-800 font-bold' }}">
                                    {{ number_format($stock, 0, ',', '.') }}
                                </span>
                            @endif
                            <span class="text-slate-400 text-xs font-medium ml-1">pcs</span>
                        </td>
                        
                        {{-- Batas Minimum Stok --}}
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <span class="text-slate-600 font-medium">{{ number_format($min, 0, ',', '.') }}</span>
                            <span class="text-slate-400 text-xs ml-1">pcs</span>
                        </td>
                    </tr>
                    @endforeach

                    @if($products->isEmpty())
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate-500">Belum ada produk terdaftar di gudang.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stock Movement History --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100 bg-slate-50/50">Stock Movement History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 border-b border-slate-100">
                        <th class="px-6 py-4 text-left font-semibold">Date & Time</th>
                        <th class="px-6 py-4 text-left font-semibold">Product</th>
                        <th class="px-6 py-4 text-left font-semibold">Type</th>
                        <th class="px-6 py-4 text-right font-semibold">Qty</th>
                        <th class="px-6 py-4 text-left font-semibold">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $movement)
                    <tr class="border-t border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 text-slate-600 text-xs">{{ $movement->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $movement->product->name ?? 'Produk Dihapus' }}</td>
                        <td class="px-6 py-4">
                            @if($movement->type === 'incoming' || $movement->quantity > 0)
                                <span class="px-2.5 py-1 rounded-md bg-emerald-100 text-emerald-700 text-xs font-semibold">Incoming</span>
                            @else
                                <span class="px-2.5 py-1 rounded-md bg-amber-100 text-amber-700 text-xs font-semibold">Outgoing</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right font-bold {{ $movement->quantity > 0 ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-slate-500 text-xs">{{ $movement->notes ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada riwayat pergerakan stok.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection