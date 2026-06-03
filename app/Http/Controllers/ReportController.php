<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil rentang tanggal
        // 🟢 PERBAIKAN: Default ditarik ke 9 hari ke belakang (Total 10 hari dengan hari ini)
        // Agar ringkasan angka tidak "Rp 0" saat pertama kali halaman dibuka.
        $startDate = $request->input('start_date', Carbon::today()->subDays(9)->format('Y-m-d'));
        $endDate   = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        // 2. Tarik data transaksi utama
        $sales = Sale::with('saleItems.product')
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->latest() // 🟢 PERBAIKAN: Urutkan transaksi dari yang paling baru
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
        
        // 4. Hitung Data Inventori
        $totalProducts = Product::where('is_active', true)->count();
        $inventories = Inventory::with('product')->get();
        
        $lowStockCount = 0;
        $totalStockValue = 0;
        
        foreach ($inventories as $inv) {
            if ($inv->current_stock <= $inv->minimum_stock) {
                $lowStockCount++;
            }
            if ($inv->product) {
                $totalStockValue += ($inv->current_stock * $inv->product->purchase_price);
            }
        }

        // 5. MESIN GRAFIK: Hitung Pendapatan 10 Hari Terakhir
        $chartData = [];
        
        for ($i = 9; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i)->format('Y-m-d');
            $dailySales = Sale::whereDate('created_at', $date)->get();
            
            $dailyTotal = 0;
            foreach ($dailySales as $ds) {
                $dailyTotal += $ds->grand_total ?? $ds->total_amount ?? 0;
            }
            
            $chartData[] = [
                'label' => Carbon::parse($date)->format('d/m'),
                'total' => $dailyTotal
            ];
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