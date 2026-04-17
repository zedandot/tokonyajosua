<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockReceiving;
use App\Models\StockReceivingItem;
use Illuminate\Support\Facades\DB;

/**
 * InventoryService
 *
 * Service tunggal untuk semua operasi stok gudang:
 *
 *  1. receiveStock()    — Barang masuk dari supplier (RCV dokumen)
 *  2. adjustStock()     — Koreksi stok manual (adjustment)
 *  3. updateMinStock()  — Update batas minimum stok
 *
 * Operasi barang KELUAR (out) sudah dihandle oleh CheckoutService (kasir).
 * InventoryService hanya menangani operasi dari sisi gudang (IN + ADJUSTMENT).
 *
 * Setiap operasi yang mengubah stok selalu:
 *  ① Update inventories.current_stock (atomic decrement/increment)
 *  ② Insert ke stock_movements (audit trail)
 * keduanya dalam satu DB::transaction().
 */
class InventoryService
{
    // ═══════════════════════════════════════════════════════════════════════════
    // 1. BARANG MASUK — Proses dokumen penerimaan resmi
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Konfirmasi penerimaan barang dan update stok.
     *
     * Mengubah status StockReceiving dari 'confirmed' → 'received',
     * menambah stok setiap produk di dalamnya, dan mencatat
     * stock_movement (type='in') per produk.
     *
     * @param  StockReceiving $receiving  Dokumen yang akan dikonfirmasi
     * @param  int            $userId     ID petugas gudang yang mengeksekusi
     * @return StockReceiving             Dokumen yang sudah diupdate
     *
     * @throws \RuntimeException  Jika status bukan 'confirmed' atau 'draft'
     * @throws \Throwable         Jika terjadi error DB
     */
    public function receiveStock(StockReceiving $receiving, int $userId): StockReceiving
    {
        if (! in_array($receiving->status, ['draft', 'confirmed'])) {
            throw new \RuntimeException(
                "Dokumen #{$receiving->receiving_number} tidak dapat diproses. Status: {$receiving->status}."
            );
        }

        $receiving->load('items.product.inventory');

        if ($receiving->items->isEmpty()) {
            throw new \RuntimeException('Dokumen penerimaan tidak memiliki item.');
        }

        return DB::transaction(function () use ($receiving, $userId) {

            foreach ($receiving->items as $item) {
                $inventory = $item->product->inventory;

                if (! $inventory) {
                    // Buat inventori jika belum ada (edge case produk baru)
                    $inventory = Inventory::create([
                        'product_id'    => $item->product_id,
                        'current_stock' => 0,
                        'minimum_stock' => 5,
                    ]);
                }

                $stockBefore = $inventory->current_stock;
                $stockAfter  = $stockBefore + $item->quantity_received;

                // Tambah stok
                $inventory->increment('current_stock', $item->quantity_received);

                // Catat pergerakan
                StockMovement::create([
                    'product_id'     => $item->product_id,
                    'user_id'        => $userId,
                    'reference_id'   => $receiving->id,
                    'reference_type' => 'stock_receiving',
                    'type'           => 'in',
                    'quantity'       => $item->quantity_received,
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $stockAfter,
                    'notes'          => "Penerimaan barang #{$receiving->receiving_number}",
                ]);
            }

            // Update status dan hitung ulang total biaya
            $receiving->load('items');
            $receiving->update([
                'status'        => 'received',
                'user_id'       => $userId,
                'received_date' => $receiving->received_date ?? today(),
                'total_cost'    => $receiving->items->sum('subtotal'),
            ]);

            return $receiving->fresh('items.product');
        });
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 2. KOREKSI STOK MANUAL — Adjustment oleh petugas gudang
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Koreksi stok manual (miscount, kerusakan, susut, dll).
     *
     * @param  Product $product     Produk yang dikoreksi
     * @param  int     $newStock    Jumlah stok yang BENAR (stok aktual hasil opname)
     * @param  int     $userId      ID petugas yang melakukan koreksi
     * @param  string  $notes       Alasan koreksi (wajib diisi)
     * @return StockMovement        Record pergerakan yang dibuat
     *
     * @throws \RuntimeException    Jika stok baru negatif
     */
    public function adjustStock(Product $product, int $newStock, int $userId, string $notes): StockMovement
    {
        if ($newStock < 0) {
            throw new \RuntimeException('Stok tidak boleh bernilai negatif.');
        }

        $inventory = $product->inventory;

        if (! $inventory) {
            throw new \RuntimeException("Data inventori untuk produk '{$product->name}' tidak ditemukan.");
        }

        return DB::transaction(function () use ($product, $inventory, $newStock, $userId, $notes) {
            $stockBefore = $inventory->current_stock;
            $diff        = $newStock - $stockBefore; // positif = tambah, negatif = kurangi

            // Update stok ke nilai yang benar
            $inventory->update(['current_stock' => $newStock]);

            // Catat pergerakan adjustment
            return StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => $userId,
                'reference_id'   => null,
                'reference_type' => 'adjustment',
                'type'           => 'adjustment',
                'quantity'       => abs($diff),      // selalu positif
                'stock_before'   => $stockBefore,
                'stock_after'    => $newStock,
                'notes'          => $notes . ($diff >= 0
                    ? " (+{$diff} unit)"
                    : " ({$diff} unit)"),
            ]);
        });
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 3. UPDATE MINIMUM STOK — Threshold notifikasi stok rendah
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Update batas minimum stok produk.
     * Perubahan ini tidak mempengaruhi stok aktual, hanya threshold notifikasi.
     *
     * @param  Product $product     Produk yang diupdate
     * @param  int     $minStock    Nilai minimum stok baru
     * @param  int|null $maxStock   Nilai maksimum stok (opsional)
     * @return Inventory
     */
    public function updateStockThreshold(Product $product, int $minStock, ?int $maxStock = null): Inventory
    {
        if ($minStock < 0) {
            throw new \RuntimeException('Minimum stok tidak boleh negatif.');
        }

        $inventory = $product->inventory ?? Inventory::create([
            'product_id'    => $product->id,
            'current_stock' => 0,
            'minimum_stock' => $minStock,
        ]);

        $data = ['minimum_stock' => $minStock];
        if ($maxStock !== null) {
            $data['maximum_stock'] = $maxStock > 0 ? $maxStock : null;
        }

        $inventory->update($data);

        return $inventory->fresh();
    }

    // ═══════════════════════════════════════════════════════════════════════════
    // 4. HELPERS — Query untuk Dashboard Gudang
    // ═══════════════════════════════════════════════════════════════════════════

    /**
     * Daftar semua produk dengan status stok (untuk dashboard gudang).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStockSummary()
    {
        return Inventory::with(['product.category'])
            ->select('inventories.*')
            ->join('products', 'products.id', '=', 'inventories.product_id')
            ->where('products.is_active', true)
            ->whereNull('products.deleted_at')
            ->orderByRaw('CASE
                WHEN inventories.current_stock <= 0 THEN 0
                WHEN inventories.current_stock <= inventories.minimum_stock THEN 1
                ELSE 2
            END')
            ->orderBy('products.name')
            ->get();
    }

    /**
     * Daftar produk dengan stok rendah/habis (notifikasi dashboard).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLowStockProducts()
    {
        return Inventory::with(['product.category'])
            ->low()
            ->whereHas('product', fn ($q) => $q->active()->whereNull('deleted_at'))
            ->orderBy('current_stock')
            ->get();
    }
}
