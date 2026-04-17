<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'reference_id',
        'reference_type',
        'type',
        'quantity',
        'stock_before',
        'stock_after',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'     => 'integer',
            'stock_before' => 'integer',
            'stock_after'  => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }
}
