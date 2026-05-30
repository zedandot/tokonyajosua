@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Dashboard Owner</h1>
        <p class="text-slate-500 text-sm mt-1">Pemantauan dan evaluasi usaha — pengambilan keputusan bisnis</p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Pendapatan Hari Ini</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($revenueToday, 0, ',', '.') }}</p>
                    <p class="text-xs {{ $percentageChange >= 0 ? 'text-emerald-600' : 'text-red-600' }} mt-1 flex items-center gap-1">
                        <span>{{ $percentageChange >= 0 ? '↑' : '↓' }} {{ number_format(abs($percentageChange), 1) }}%</span> vs yesterday
                    </p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Nilai Aset Gudang</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalCapital, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">Total modal stok saat ini</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-sky-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Products</p>
                    <p class="text-2xl font-bold text-slate-800 mt-1">{{ $totalProducts }}</p>
                    <p class="text-xs text-slate-400 mt-1">Active in catalog</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-violet-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Low Stock Items</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ $lowStockCount }}</p>
                    <p class="text-xs text-amber-600 mt-1">Needs restock</p>
                </div>
                <div class="w-12 h-12 rounded-lg bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Sales Analytics Chart (7 Hari Terakhir) --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Sales Analytics (7 Days)</h3>
            <div class="h-64 flex items-end gap-2">
                @foreach($chartData as $chart)
                <div class="flex-1 flex flex-col justify-end items-center group relative h-full">
                    <div class="absolute bottom-full mb-2 bg-slate-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 pointer-events-none">
                        Rp {{ number_format($chart['total'], 0, ',', '.') }}
                    </div>
                    <div class="w-full {{ $chart['day'] === 'Today' ? 'bg-sky-500' : 'bg-sky-200' }} hover:bg-sky-400 transition-all rounded-t cursor-pointer" style="height: {{ $chart['height'] }}%; min-height: 4px;"></div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between mt-2 text-xs text-slate-400">
                @foreach($chartData as $chart)
                <span>{{ $chart['day'] }}</span>
                @endforeach
            </div>
        </div>

        {{-- Laporan Selisih Keuntungan (Hari Ini) --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Laporan Keuntungan (Hari Ini)</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center p-3 rounded-lg bg-slate-50">
                    <span class="text-slate-600">Pendapatan</span>
                    <span class="font-semibold text-slate-800">Rp {{ number_format($revenueToday, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-lg bg-slate-50">
                    <span class="text-slate-600">Modal (HPP)</span>
                    <span class="font-semibold text-slate-800">Rp {{ number_format($costToday, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                    <span class="font-medium text-emerald-700">Keuntungan Bersih</span>
                    <span class="font-bold text-emerald-600">Rp {{ number_format($profitToday, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Barang Paling Sering Dibeli --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 lg:col-span-2">
            <h3 class="font-semibold text-slate-800 mb-4">Barang Paling Sering Dibeli (Sepanjang Waktu)</h3>
            <div class="space-y-3">
                @forelse($topProducts as $name => $qty)
                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50">
                    <span class="font-medium text-slate-700">{{ $name }}</span>
                    <span class="text-sm text-slate-500">{{ $qty }} sold</span>
                </div>
                @empty
                <p class="text-sm text-slate-500 text-center py-4">Belum ada data penjualan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Riwayat Transaksi Penjualan --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">5 Transaksi Terakhir</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-slate-500 border-b border-slate-100">
                            <th class="pb-3 font-medium">ID</th>
                            <th class="pb-3 font-medium">Time</th>
                            <th class="pb-3 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $sale)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="py-3 font-mono text-slate-700">TRX-{{ $sale->id }}</td>
                            <td class="py-3 text-slate-600">{{ $sale->created_at->format('d/m H:i') }}</td>
                            <td class="py-3 font-medium text-slate-800">Rp {{ number_format($sale->grand_total ?? $sale->total_amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-slate-500">Belum ada transaksi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Stok Perlu Ditambahkan --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Stok Menipis
            </h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($lowStockItems as $inv)
                <div class="p-2 rounded-lg bg-amber-50 border border-amber-100">
                    <p class="font-medium text-slate-700 text-sm">{{ $inv->product->name ?? 'Produk Dihapus' }}</p>
                    <p class="text-xs text-amber-700">Sisa Stok: {{ $inv->current_stock }} pcs</p>
                </div>
                @empty
                <p class="text-sm text-slate-500 text-center py-4">Semua stok aman terkendali.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection