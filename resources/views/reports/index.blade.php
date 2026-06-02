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
        
        <form action="{{ route('reports.index') }}" method="GET" class="flex flex-wrap gap-2">
            <input type="date" name="start_date" value="{{ $startDate ?? date('Y-m-d') }}" class="flex-1 min-w-[130px] px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:border-sky-500">
            <input type="date" name="end_date" value="{{ $endDate ?? date('Y-m-d') }}" class="flex-1 min-w-[130px] px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:border-sky-500">
            <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium transition-colors">Filter</button>
        </form>
    </div>

    {{-- Profit Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <p class="text-sm font-medium text-slate-500">Total Revenue</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <p class="text-sm font-medium text-slate-500">Total Cost</p>
            <p class="text-2xl font-bold text-slate-800 mt-1">Rp {{ number_format($totalCost ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <p class="text-sm font-medium text-slate-500">Net Profit</p>
            <p class="text-2xl font-bold {{ ($netProfit ?? 0) >= 0 ? 'text-emerald-600' : 'text-red-500' }} mt-1">
                Rp {{ number_format($netProfit ?? 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Sales Report Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Sales Trend (Last 10 Days)</h3>
        <div class="h-72 relative w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Inventory Report --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Inventory Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b border-slate-50">
                    <span class="text-slate-600">Total Products</span>
                    <span class="font-semibold text-slate-800">{{ $totalProducts ?? 0 }} items</span>
                </div>
                <div class="flex justify-between items-center pb-3 border-b border-slate-50">
                    <span class="text-slate-600">Low Stock Items</span>
                    <span class="font-semibold text-amber-600">{{ $lowStockCount ?? 0 }} items</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-600">Total Stock Value</span>
                    <span class="font-semibold text-slate-800">Rp {{ number_format($totalStockValue ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Transaction History --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 overflow-hidden flex flex-col">
            <h3 class="font-semibold text-slate-800 mb-4">Transaction History</h3>
            <div class="overflow-x-auto max-h-48 overflow-y-auto flex-1 hide-scrollbar">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-white shadow-sm">
                        <tr class="text-slate-500 border-b border-slate-100">
                            <th class="pb-2 pt-1 text-left font-medium">Invoice</th>
                            <th class="pb-2 pt-1 text-left font-medium">Date</th>
                            <th class="pb-2 pt-1 text-right font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales ?? [] as $sale)
                        <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                            <td class="py-2.5 font-mono text-slate-700 text-xs">{{ $sale->invoice_number ?? 'TRX-'.$sale->id }}</td>
                            <td class="py-2.5 text-slate-600 text-xs">{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-2.5 text-right font-bold text-slate-800">Rp {{ number_format($sale->grand_total ?? $sale->total_amount ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="py-6 text-center text-slate-500">Belum ada transaksi pada rentang tanggal ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MESIN GRAFIK DENGAN PENGAMAN TURBOLINKS --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function renderChart() {
    const canvas = document.getElementById('salesChart');
    if (!canvas) return; // Berhenti jika kanvas tidak ditemukan (misal pindah halaman)

    // Hancurkan grafik lama jika ada agar tidak tumpang tindih saat Turbolinks memuat ulang
    if (window.salesChartInstance) {
        window.salesChartInstance.destroy();
    }

    const rawChartData = @json($chartData ?? []);
    const labels = rawChartData.map(item => item.label);
    const dataValues = rawChartData.map(item => item.total);

    const bgColors = dataValues.map((_, index) => {
        return index === dataValues.length - 1 ? '#0ea5e9' : '#bae6fd'; 
    });

    const hoverBgColors = dataValues.map((_, index) => {
        return index === dataValues.length - 1 ? '#0284c7' : '#7dd3fc';
    });

    const ctx = canvas.getContext('2d');
    
    window.salesChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels, 
            datasets: [{
                label: 'Pendapatan (Rp)',
                data: dataValues,
                backgroundColor: bgColors,
                hoverBackgroundColor: hoverBgColors,
                borderRadius: 6, 
                borderSkipped: 'bottom', 
                barPercentage: 0.6,
                categoryPercentage: 0.8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { size: 13, family: "'Plus Jakarta Sans', sans-serif" },
                    bodyFont: { size: 14, weight: 'bold', family: "'Plus Jakarta Sans', sans-serif" },
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            let value = context.raw || 0;
                            return 'Rp ' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: {
                        color: '#64748b',
                        font: { family: "'Plus Jakarta Sans', sans-serif" },
                        callback: function(value) {
                            if (value >= 1000000) return (value / 1000000) + 'M';
                            if (value >= 1000) return (value / 1000) + 'k';
                            return value;
                        }
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { color: '#64748b', font: { family: "'Plus Jakarta Sans', sans-serif" } }
                }
            }
        }
    });
}

// 🟢 PERBAIKAN: Jalankan fungsi saat web pertama kali dimuat ATAU saat pindah halaman via Turbolinks
document.addEventListener('DOMContentLoaded', renderChart);
document.addEventListener('turbolinks:load', renderChart);
</script>
@endpush
@endsection