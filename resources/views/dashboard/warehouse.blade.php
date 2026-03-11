@extends('layouts.app')

@section('title', 'Dashboard Petugas Gudang')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Dashboard Petugas Gudang</h1>
        <p class="text-slate-500 mt-1">Mencatat barang masuk, memperbarui stok, dan memastikan ketersediaan barang</p>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 md:gap-6">
        <a href="{{ route('inventory.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-md hover:border-sky-200 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Barang Masuk</p>
                    <p class="text-lg font-bold text-slate-800 mt-1 group-hover:text-sky-600">Catat Barang Masuk</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center group-hover:bg-sky-100">
                    <svg class="w-6 h-6 text-emerald-600 group-hover:text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                </div>
            </div>
        </a>
        <a href="{{ route('inventory.index') }}" class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-md hover:border-sky-200 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Update Stok</p>
                    <p class="text-lg font-bold text-slate-800 mt-1 group-hover:text-sky-600">Perbarui Jumlah Stok</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
        </a>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Stok Rendah</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">12</p>
                    <p class="text-xs text-amber-600 mt-1">Perlu restock</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Stok di Gudang --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100">Kondisi Stok di Gudang</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-6 py-4 text-left font-semibold">Produk</th>
                        <th class="px-6 py-4 text-left font-semibold">SKU</th>
                        <th class="px-6 py-4 text-right font-semibold">Stok</th>
                        <th class="px-6 py-4 text-right font-semibold">Min. Stok</th>
                        <th class="px-6 py-4 text-center font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @php $items = [['name' => 'Kopi Sachet Premium', 'sku' => 'KOP-001', 'stock' => 120, 'min' => 50], ['name' => 'Susu UHT 1L', 'sku' => 'SUS-002', 'stock' => 45, 'min' => 30], ['name' => 'Teh Botol 350ml', 'sku' => 'TEH-003', 'stock' => 5, 'min' => 20], ['name' => 'Mie Instan', 'sku' => 'MIE-004', 'stock' => 200, 'min' => 100], ['name' => 'Roti Tawar', 'sku' => 'ROT-005', 'stock' => 3, 'min' => 15]]; @endphp
                    @foreach($items as $i)
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $i['name'] }}</td>
                        <td class="px-6 py-4 text-slate-600 font-mono">{{ $i['sku'] }}</td>
                        <td class="px-6 py-4 text-right {{ $i['stock'] <= $i['min'] ? 'text-amber-600 font-semibold' : 'text-slate-700' }}">{{ $i['stock'] }} pcs</td>
                        <td class="px-6 py-4 text-right text-slate-600">{{ $i['min'] }} pcs</td>
                        <td class="px-6 py-4 text-center">
                            @if($i['stock'] <= $i['min'])
                            <span class="px-2 py-1 rounded bg-amber-100 text-amber-700 text-xs font-medium">Perlu Restock</span>
                            @else
                            <span class="px-2 py-1 rounded bg-emerald-100 text-emerald-700 text-xs font-medium">Aman</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100">
            <a href="{{ route('inventory.index') }}" class="text-sm text-sky-600 hover:underline font-medium">Kelola inventori lengkap →</a>
        </div>
    </div>

    {{-- Riwayat Barang Masuk --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100">Riwayat Barang Masuk</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-6 py-4 text-left font-semibold">Tanggal</th>
                        <th class="px-6 py-4 text-left font-semibold">Produk</th>
                        <th class="px-6 py-4 text-left font-semibold">Tipe</th>
                        <th class="px-6 py-4 text-right font-semibold">Qty</th>
                        <th class="px-6 py-4 text-left font-semibold">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-100"><td class="px-6 py-4">10 Mar 2025 14:30</td><td class="px-6 py-4">Susu UHT 1L</td><td class="px-6 py-4"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs">Masuk</span></td><td class="px-6 py-4 text-right">+50</td><td class="px-6 py-4 text-slate-500">Restock</td></tr>
                    <tr class="border-t border-slate-100"><td class="px-6 py-4">10 Mar 2025 09:00</td><td class="px-6 py-4">Mie Instan</td><td class="px-6 py-4"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs">Masuk</span></td><td class="px-6 py-4 text-right">+100</td><td class="px-6 py-4 text-slate-500">Pembelian</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
