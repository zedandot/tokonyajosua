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
        <button class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-700 text-white font-medium transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Record Incoming
        </button>
    </div>

    {{-- Inventory Stock Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100">Current Stock</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-6 py-4 text-left font-semibold">Product</th>
                        <th class="px-6 py-4 text-left font-semibold">SKU</th>
                        <th class="px-6 py-4 text-right font-semibold">Stock</th>
                        <th class="px-6 py-4 text-right font-semibold">Min. Stock</th>
                        <th class="px-6 py-4 text-center font-semibold">Update</th>
                    </tr>
                </thead>
                <tbody>
                    @php $items = [
                        ['name' => 'Kopi Sachet Premium', 'sku' => 'KOP-001', 'stock' => 120, 'min' => 50],
                        ['name' => 'Susu UHT 1L', 'sku' => 'SUS-002', 'stock' => 45, 'min' => 30],
                        ['name' => 'Teh Botol 350ml', 'sku' => 'TEH-003', 'stock' => 5, 'min' => 20],
                        ['name' => 'Mie Instan', 'sku' => 'MIE-004', 'stock' => 200, 'min' => 100],
                        ['name' => 'Roti Tawar', 'sku' => 'ROT-005', 'stock' => 3, 'min' => 15],
                    ]; @endphp
                    @foreach($items as $i)
                    <tr class="border-t border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-medium text-slate-800">{{ $i['name'] }}</td>
                        <td class="px-6 py-4 text-slate-600 font-mono">{{ $i['sku'] }}</td>
                        <td class="px-6 py-4 text-right">
                            <span class="{{ $i['stock'] <= $i['min'] ? 'text-amber-600 font-semibold' : 'text-slate-700' }}">
                                {{ $i['stock'] }} pcs
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-slate-600">{{ $i['min'] }} pcs</td>
                        <td class="px-6 py-4 text-center">
                            <button class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium">Update</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Stock Movement History --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <h3 class="px-6 py-4 font-semibold text-slate-800 border-b border-slate-100">Stock Movement History</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-slate-600">
                        <th class="px-6 py-4 text-left font-semibold">Date</th>
                        <th class="px-6 py-4 text-left font-semibold">Product</th>
                        <th class="px-6 py-4 text-left font-semibold">Type</th>
                        <th class="px-6 py-4 text-right font-semibold">Qty</th>
                        <th class="px-6 py-4 text-left font-semibold">Note</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="border-t border-slate-100"><td class="px-6 py-4">10 Mar 2025 14:30</td><td class="px-6 py-4">Susu UHT 1L</td><td class="px-6 py-4"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs">Incoming</span></td><td class="px-6 py-4 text-right">+50</td><td class="px-6 py-4 text-slate-500">Restock</td></tr>
                    <tr class="border-t border-slate-100"><td class="px-6 py-4">10 Mar 2025 12:15</td><td class="px-6 py-4">Kopi Sachet</td><td class="px-6 py-4"><span class="px-2 py-0.5 rounded bg-amber-100 text-amber-700 text-xs">Outgoing</span></td><td class="px-6 py-4 text-right">-3</td><td class="px-6 py-4 text-slate-500">Sale</td></tr>
                    <tr class="border-t border-slate-100"><td class="px-6 py-4">10 Mar 2025 09:00</td><td class="px-6 py-4">Mie Instan</td><td class="px-6 py-4"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 text-xs">Incoming</span></td><td class="px-6 py-4 text-right">+100</td><td class="px-6 py-4 text-slate-500">Purchase order</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
