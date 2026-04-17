<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

/**
 * CheckoutService
 *
 * Mengurus seluruh proses checkout dari cart → transaksi:
 *  1. Validasi stok semua item
 *  2. Buat record Sale (header)
 *  3. Buat record SaleItems (detail)
 *  4. Kurangi stok di Inventory
 *  5. Catat pergerakan stok di StockMovements
 *  6. Bersihkan Cart
 *
 * Semua operasi dalam satu DB transaction — jika salah satu gagal,
 * semua di-rollback sehingga data selalu konsisten.
 */
class CheckoutService
{
    /**
     * Proses checkout cart menjadi transaksi penjualan.
     *
     * @param  Cart   $cart       Cart yang akan di-checkout
     * @param  float  $amountPaid Uang yang dibayarkan pelanggan
     * @return Sale               Record transaksi yang berhasil dibuat
     *
     * @throws \RuntimeException  Jika cart kosong atau stok tidak cukup
     * @throws \Throwable         Jika terjadi error DB
     */
    public function process(Cart $cart, float $amountPaid): Sale
    {
        // Eager load semua relasi yang dibutuhkan sekaligus
        $cart->load(['items.product.inventory', 'user', 'customer']);

        // ── 1. Validasi dasar ──────────────────────────────────────────────────
        if ($cart->items->isEmpty()) {
            throw new \RuntimeException('Keranjang belanja masih kosong.');
        }

        foreach ($cart->items as $item) {
            $stock = $item->product->inventory?->current_stock ?? 0;

            if ($stock < $item->quantity) {
                throw new \RuntimeException(
                    "Stok {$item->product->name} tidak mencukupi. " .
                    "Tersedia: {$stock}, diminta: {$item->quantity}."
                );
            }
        }

        // ── 2. Hitung total finansial ──────────────────────────────────────────
        $subtotal       = (float) $cart->items->sum('subtotal');
        $discountAmount = (float) $cart->discount_amount;
        $taxAmount      = 0; // Bisa disesuaikan jika ada PPN
        $totalAmount    = max(0, $subtotal - $discountAmount + $taxAmount);
        $changeAmount   = max(0, $amountPaid - $totalAmount);

        if ($amountPaid < $totalAmount) {
            throw new \RuntimeException(
                "Pembayaran kurang. Total: Rp " . number_format($totalAmount, 0, ',', '.') .
                ", dibayar: Rp " . number_format($amountPaid, 0, ',', '.')
            );
        }

        // ── 3. Jalankan semua operasi dalam satu DB transaction ───────────────
        return DB::transaction(function () use ($cart, $subtotal, $discountAmount, $taxAmount, $totalAmount, $amountPaid, $changeAmount) {

            // 3a. Buat header transaksi
            $sale = Sale::create([
                'invoice_number'  => Sale::generateInvoiceNumber('TRX'),
                'user_id'         => $cart->user_id,
                'customer_id'     => $cart->customer_id,
                'status'          => 'completed',
                'subtotal'        => $subtotal,
                'discount_amount' => $discountAmount,
                'tax_amount'      => $taxAmount,
                'total_amount'    => $totalAmount,
                'amount_paid'     => $amountPaid,
                'change_amount'   => $changeAmount,
                'notes'           => $cart->notes,
                'sold_at'         => now(),
            ]);

            // 3b. Buat detail item & potong stok
            foreach ($cart->items as $cartItem) {
                $product   = $cartItem->product;
                $inventory = $product->inventory;

                // Simpan detail item transaksi dengan snapshot harga
                SaleItem::create([
                    'sale_id'         => $sale->id,
                    'product_id'      => $product->id,
                    'quantity'        => $cartItem->quantity,
                    'unit_price'      => $cartItem->unit_price,      // snapshot harga jual
                    'purchase_price'  => $product->purchase_price,   // snapshot harga modal
                    'discount_amount' => $cartItem->discount_amount,
                    'subtotal'        => $cartItem->subtotal,
                ]);

                // Kurangi stok
                $stockBefore = $inventory->current_stock;
                $stockAfter  = $stockBefore - $cartItem->quantity;

                $inventory->decrement('current_stock', $cartItem->quantity);

                // Catat pergerakan stok
                StockMovement::create([
                    'product_id'     => $product->id,
                    'user_id'        => $cart->user_id,
                    'reference_id'   => $sale->id,
                    'reference_type' => 'sale',
                    'type'           => 'out',
                    'quantity'       => $cartItem->quantity,
                    'stock_before'   => $stockBefore,
                    'stock_after'    => $stockAfter,
                    'notes'          => "Penjualan #{$sale->invoice_number}",
                ]);
            }

            // 3c. Bersihkan cart setelah checkout berhasil
            $cart->clearItems();

            return $sale->load('items.product');
        });
    }
}
