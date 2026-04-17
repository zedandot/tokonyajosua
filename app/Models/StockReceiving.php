<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockReceiving extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'receiving_number',
        'supplier_id',
        'user_id',
        'status',
        'reference_document',
        'received_date',
        'total_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'received_date' => 'date',
            'total_cost'    => 'decimal:2',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeReceived($query)
    {
        return $query->where('status', 'received');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('received_date', today());
    }

    public function scopeDateRange($query, string $from, string $to)
    {
        return $query->whereBetween('received_date', [$from, $to]);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /** Petugas gudang yang menerima. */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockReceivingItem::class);
    }

    // ─── Static Helpers ───────────────────────────────────────────────────────

    /**
     * Generate receiving number unik.
     * Format: RCV-20260417-0001
     */
    public static function generateReceivingNumber(): string
    {
        $prefix = 'RCV-' . now()->format('Ymd');
        $last   = static::where('receiving_number', 'like', $prefix . '%')
                        ->orderByDesc('receiving_number')
                        ->value('receiving_number');

        $sequence = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    // ─── Computed Attributes ──────────────────────────────────────────────────

    /** Total unit yang diterima dari semua item. */
    public function getTotalQtyReceivedAttribute(): int
    {
        return (int) $this->items->sum('quantity_received');
    }

    /** Hitung ulang total_cost dari items. */
    public function recalculateTotalCost(): void
    {
        $this->update(['total_cost' => $this->items->sum('subtotal')]);
    }
}
