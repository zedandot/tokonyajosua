<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'purchase_price',
        'discount_amount',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'quantity'        => 'integer',
            'unit_price'      => 'decimal:2',
            'purchase_price'  => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'subtotal'        => 'decimal:2',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Laba kotor per item ini. */
    public function getProfitAttribute(): float
    {
        return (float) ($this->subtotal - ($this->purchase_price * $this->quantity));
    }
}
