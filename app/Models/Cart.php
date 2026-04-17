<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'discount_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'discount_amount' => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** Kasir pemilik cart ini. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    // ─── Computed Attributes ──────────────────────────────────────────────────

    /** Total subtotal semua item sebelum diskon cart. */
    public function getItemsTotalAttribute(): float
    {
        return (float) $this->items->sum('subtotal');
    }

    /** Grand total setelah diskon cart-level. */
    public function getGrandTotalAttribute(): float
    {
        return max(0, $this->items_total - (float) $this->discount_amount);
    }

    /** Jumlah total item (qty) dalam cart. */
    public function getTotalQtyAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    /**
     * Ambil cart aktif kasir, atau buat baru jika belum ada.
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return static::firstOrCreate(['user_id' => $userId]);
    }

    /**
     * Tambah produk ke cart, atau update qty jika sudah ada.
     * Otomatis validasi stok tersedia.
     *
     * @throws \RuntimeException jika stok tidak cukup
     */
    public function addItem(Product $product, int $qty = 1, float $discount = 0): CartItem
    {
        $inventory = $product->inventory;

        if (! $inventory || $inventory->current_stock < $qty) {
            throw new \RuntimeException(
                "Stok {$product->name} tidak mencukupi. Tersedia: " . ($inventory?->current_stock ?? 0)
            );
        }

        $existingItem = $this->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $newQty = $existingItem->quantity + $qty;

            if ($inventory->current_stock < $newQty) {
                throw new \RuntimeException(
                    "Stok {$product->name} tidak mencukupi. Tersedia: {$inventory->current_stock}, diminta: {$newQty}"
                );
            }

            $existingItem->update(['quantity' => $newQty]);
            return $existingItem->fresh();
        }

        return $this->items()->create([
            'product_id'      => $product->id,
            'quantity'        => $qty,
            'unit_price'      => $product->selling_price,
            'discount_amount' => $discount,
        ]);
    }

    /**
     * Hapus semua item dari cart (reset/clear).
     */
    public function clearItems(): void
    {
        $this->items()->delete();
        $this->update(['discount_amount' => 0, 'notes' => null, 'customer_id' => null]);
    }
}
