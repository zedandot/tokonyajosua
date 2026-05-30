<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Inventory; // 🟢 TAMBAHAN: Panggil model Inventory
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil rentang tanggal
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        // 2. Tarik data transaksi utama
        $sales = Sale::with('saleItems.product')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->get();

        // 3. Hitung Pendapatan & Modal
        $totalRevenue = 0;
        $totalCost    = 0;

        foreach ($sales as $sale) {
            $totalRevenue += $sale->grand_total ?? $sale->total_amount ?? 0;
            foreach ($sale->saleItems as $item) {
                if ($item->product) {
                    $totalCost += ($item->product->purchase_price * $item->quantity);
                }
            }
        }
        $netProfit = $totalRevenue - $totalCost;
        
        // 4. Hitung Data Inventori (Total Produk, Stok Rendah, Total Nilai Aset)
        $totalProducts = Product::where('is_active', true)->count();
        $inventories = Inventory::with('product')->get();
        
        $lowStockCount = 0;
        $totalStockValue = 0;
        
        foreach ($inventories as $inv) {
            // Hitung barang yang stoknya mau habis
            if ($inv->current_stock <= $inv->minimum_stock) {
                $lowStockCount++;
            }
            // Hitung nilai uang dari seluruh barang di gudang
            if ($inv->product) {
                $totalStockValue += ($inv->current_stock * $inv->product->purchase_price);
            }
        }

        // 5. MESIN GRAFIK: Hitung Pendapatan 10 Hari Terakhir
        $chartData = [];
        $maxChartValue = 0;
        
        // Looping dari 9 hari lalu sampai hari ini
        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $dailySales = Sale::whereDate('created_at', $date)->get();
            
            $dailyTotal = 0;
            foreach ($dailySales as $ds) {
                $dailyTotal += $ds->grand_total ?? $ds->total_amount ?? 0;
            }
            
            // Cari nilai tertinggi untuk menentukan batas atas (100%) grafik
            if ($dailyTotal > $maxChartValue) {
                $maxChartValue = $dailyTotal;
            }
            
            $chartData[] = [
                'label' => Carbon::parse($date)->format('d/m'),
                'total' => $dailyTotal
            ];
        }
        
        // Hitung persentase tinggi (height) untuk CSS Blade
        foreach ($chartData as $key => $data) {
            $chartData[$key]['height'] = $maxChartValue > 0 ? round(($data['total'] / $maxChartValue) * 100) : 0;
        }

        // 6. Kirim seluruh amunisi ke View
        return view('reports.index', compact(
            'startDate', 
            'endDate', 
            'totalRevenue', 
            'totalCost', 
            'netProfit',
            'totalProducts',
            'lowStockCount',
            'totalStockValue',
            'chartData',
            'sales'
        ));
    }
}