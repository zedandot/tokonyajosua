<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReceivingItem extends Model
{
    protected $fillable = [
        'stock_receiving_id',
        'product_id',
        'quantity_ordered',
        'quantity_received',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered'  => 'integer',
            'quantity_received' => 'integer',
            'unit_cost'         => 'decimal:2',
            // subtotal adalah stored computed column — tidak di-set dari PHP
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function stockReceiving()
    {
        return $this->belongsTo(StockReceiving::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Selisih antara yang dipesan dan yang diterima. */
    public function getQuantityShortageAttribute(): int
    {
        return max(0, $this->quantity_ordered - $this->quantity_received);
    }

    /** True jika penerimaan penuh (tidak ada kekurangan). */
    public function getIsCompleteAttribute(): bool
    {
        return $this->quantity_ordered === 0
            || $this->quantity_received >= $this->quantity_ordered;
    }
}
