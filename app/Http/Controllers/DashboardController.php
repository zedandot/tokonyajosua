<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Inventory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Tambahkan DB untuk operasi agregasi MongoDB

class DashboardController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role;

        if ($role === 'owner') {
            return $this->ownerDashboard();
        } elseif ($role === 'kasir') {
            return view('dashboard.cashier');
        } elseif ($role === 'gudang') {
            // 🚀 KODE DIPERBARUI: Ambil data gudang, lalu saring (buang) data yang produk induknya sudah hilang
            $inventories = Inventory::with('product')->get()->filter(function ($inv) {
                return $inv->product !== null;
            });
            
            return view('dashboard.warehouse', compact('inventories'));
        }

        return redirect()->route('login');
    }

    private function ownerDashboard()
    {
        // 1. Hitung Total Penjualan & Keuntungan Hari Ini
        $today = Carbon::today();
        $salesToday = Sale::with('saleItems.product')->whereDate('created_at', $today)->get();
        
        $revenueToday = 0;
        $costToday = 0;
        foreach ($salesToday as $sale) {
            $revenueToday += $sale->grand_total ?? $sale->total_amount ?? 0;
            foreach ($sale->saleItems as $item) {
                if ($item->product) {
                    $costToday += ($item->product->purchase_price * $item->quantity);
                }
            }
        }
        $profitToday = $revenueToday - $costToday;

        // 2. Hitung Penjualan Kemarin (Untuk Persentase Naik/Turun)
        $yesterday = Carbon::yesterday();
        $salesYesterday = Sale::whereDate('created_at', $yesterday)->get();
        $revenueYesterday = $salesYesterday->sum(fn($s) => $s->grand_total ?? $s->total_amount ?? 0);
        
        $percentageChange = 0;
        if ($revenueYesterday > 0) {
            $percentageChange = (($revenueToday - $revenueYesterday) / $revenueYesterday) * 100;
        } elseif ($revenueToday > 0) {
            $percentageChange = 100; // Jika kemarin 0, tapi hari ini ada, anggap naik 100%
        }

        // 3. Hitung Modal Belanja (Total HPP seluruh barang di gudang)
        // 🚀 KODE DIPERBARUI: Filter juga dipasang di sini agar hitungan Owner tidak meleset
        $inventories = Inventory::with('product')->get()->filter(function ($inv) {
            return $inv->product !== null;
        });
        
        $totalCapital = 0;
        $lowStockCount = 0;
        $lowStockItems = [];

        foreach ($inventories as $inv) {
            if ($inv->product) {
                $totalCapital += ($inv->current_stock * $inv->product->purchase_price);
            }
            if ($inv->current_stock <= $inv->minimum_stock) {
                $lowStockCount++;
                $lowStockItems[] = $inv;
            }
        }

        // 4. Hitung Total Produk Aktif
        $totalProducts = Product::where('is_active', true)->count();

        // 5. Riwayat 5 Transaksi Terakhir
        $recentTransactions = Sale::latest()->take(5)->get();

        // 6. Grafik Penjualan Mingguan (7 Hari Terakhir)
        $chartData = [];
        $maxChartValue = 0;
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dailyTotal = Sale::whereDate('created_at', $date->format('Y-m-d'))
                              ->get()
                              ->sum(fn($s) => $s->grand_total ?? $s->total_amount ?? 0);
            
            if ($dailyTotal > $maxChartValue) $maxChartValue = $dailyTotal;
            
            $chartData[] = [
                'day'   => $i == 0 ? 'Today' : $date->format('D'),
                'total' => $dailyTotal
            ];
        }
        // Hitung tinggi grafik
        foreach ($chartData as &$data) {
            $data['height'] = $maxChartValue > 0 ? round(($data['total'] / $maxChartValue) * 100) : 0;
        }

        // 7. Top 5 Barang Paling Sering Dibeli (Agregasi)
        // Karena MongoDB tidak mendukung "groupBy" seperti MySQL, kita hitung manual via PHP
        $allSaleItems = SaleItem::with('product')->get();
        $productSalesCount = [];
        
        foreach ($allSaleItems as $item) {
            if ($item->product) {
                $name = $item->product->name;
                if (!isset($productSalesCount[$name])) {
                    $productSalesCount[$name] = 0;
                }
                $productSalesCount[$name] += $item->quantity;
            }
        }
        arsort($productSalesCount); // Urutkan dari yang terbanyak
        $topProducts = array_slice($productSalesCount, 0, 5, true);

        return view('dashboard.index', compact(
            'revenueToday', 'percentageChange', 'totalCapital', 
            'totalProducts', 'lowStockCount', 'lowStockItems', 
            'recentTransactions', 'chartData', 'topProducts',
            'costToday', 'profitToday'
        ));
    }
}