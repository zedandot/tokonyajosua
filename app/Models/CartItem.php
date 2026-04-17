<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_amount',
    ];

    protected function casts(): array
    {
        return [
            'quantity'        => 'integer',
            'unit_price'      => 'decimal:2',
            'discount_amount' => 'decimal:2',
            // subtotal adalah stored computed column — tidak perlu di-set manual
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Hitung subtotal secara manual (mirror dari computed column di DB).
     * Berguna saat ingin menampilkan nilai sebelum record disimpan.
     */
    public function calculateSubtotal(): float
    {
        return max(0, ((float) $this->unit_price - (float) $this->discount_amount) * $this->quantity);
    }
}
