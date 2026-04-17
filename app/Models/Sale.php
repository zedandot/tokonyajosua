<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'user_id',
        'customer_id',
        'status',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'amount_paid',
        'change_amount',
        'notes',
        'sold_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'        => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'tax_amount'      => 'decimal:2',
            'total_amount'    => 'decimal:2',
            'amount_paid'     => 'decimal:2',
            'change_amount'   => 'decimal:2',
            'sold_at'         => 'datetime',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('sold_at', today());
    }

    public function scopeDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('sold_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** Kasir yang memproses transaksi ini. */
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
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Generate invoice number unik.
     *
     * @param string $prefix  'TRX' untuk Kasir POS | 'INV' untuk Owner/manual
     * Format TRX: TRX-20260417-0001
     * Format INV: INV-20260417-0001
     */
    public static function generateInvoiceNumber(string $prefix = 'TRX'): string
    {
        $datePrefix = strtoupper($prefix) . '-' . now()->format('Ymd');
        $last       = static::where('invoice_number', 'like', $datePrefix . '%')
                            ->orderByDesc('invoice_number')
                            ->value('invoice_number');

        $sequence = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $datePrefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // ─── Computed Attributes ──────────────────────────────────────────────────

    /** Total modal (COGS) dari semua item transaksi. */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->purchase_price * $item->quantity);
    }

    /** Laba kotor: total_amount - total_cost. */
    public function getGrossProfitAttribute(): float
    {
        return (float) ($this->total_amount - $this->total_cost);
    }
}
