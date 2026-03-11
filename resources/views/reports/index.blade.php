@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Reports</h1>
            <p class="text-slate-500 text-sm mt-1">Business analytics and monitoring</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <input type="date" value="{{ date('Y-m-d') }}" class="flex-1 min-w-[130px] px-3 py-2 rounded-lg border border-slate-200 text-sm">
            <input type="date" value="{{ date('Y-m-d') }}" class="flex-1 min-w-[130px] px-3 py-2 rounded-lg border border-slate-200 text-sm">
            <button class="w-full sm:w-auto px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium transition-colors">Filter</button>
        </div>
    </div>

    {{-- Profit Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <p class="text-sm font-medium text-slate-500">Total Revenue</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">Rp 12.450.000</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <p class="text-sm font-medium text-slate-500">Total Cost</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">Rp 9.200.000</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <p class="text-sm font-medium text-slate-500">Net Profit</p>
            <p class="text-2xl font-bold text-emerald-600 mt-1">Rp 3.250.000</p>
        </div>
    </div>

    {{-- Sales Report Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Sales Report</h3>
        <div class="h-64 flex items-end gap-3">
            @foreach([45, 65, 55, 80, 70, 90, 75, 85, 95, 88] as $h)
            <div class="flex-1 bg-sky-100 rounded-t" style="height: {{ $h }}%"></div>
            @endforeach
        </div>
        <p class="text-xs text-slate-400 mt-2">Last 10 days</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Inventory Report --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Inventory Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Total Products</span>
                    <span class="font-semibold text-slate-800">248</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Low Stock Items</span>
                    <span class="font-semibold text-amber-600">12</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Total Stock Value</span>
                    <span class="font-semibold text-slate-800">Rp 28.500.000</span>
                </div>
            </div>
        </div>

        {{-- Transaction History --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 overflow-hidden">
            <h3 class="font-semibold text-slate-800 mb-4">Transaction History</h3>
            <div class="overflow-x-auto max-h-64 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-slate-500 border-b border-slate-100">
                            <th class="pb-2 text-left font-medium">ID</th>
                            <th class="pb-2 text-left font-medium">Date</th>
                            <th class="pb-2 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(range(1, 8) as $i)
                        <tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="py-2 font-mono text-slate-700">TRX-{{ 2840 + $i }}</td>
                            <td class="py-2 text-slate-600">{{ now()->subHours($i * 2)->format('d/m H:i') }}</td>
                            <td class="py-2 text-right font-medium text-slate-800">Rp {{ number_format(35000 + $i * 5000, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
