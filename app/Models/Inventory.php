<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'product_id',
        'current_stock',
        'minimum_stock',
        'maximum_stock',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'integer',
            'minimum_stock' => 'integer',
            'maximum_stock' => 'integer',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /** Semua riwayat pergerakan stok produk ini. */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'product_id', 'product_id');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /** Produk dengan stok rendah (current_stock <= minimum_stock). */
    public function scopeLow($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    /** Produk dengan stok aman (current_stock > minimum_stock). */
    public function scopeSafe($query)
    {
        return $query->whereColumn('current_stock', '>', 'minimum_stock');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** True jika stok saat ini di bawah atau sama dengan minimum stok. */
    public function getIsLowAttribute(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /** Label status stok untuk tampilan dashboard. */
    public function getStatusLabelAttribute(): string
    {
        if ($this->current_stock <= 0)                    return 'Habis';
        if ($this->current_stock <= $this->minimum_stock) return 'Rendah';
        return 'Aman';
    }

    /** CSS class untuk badge status (Blade/frontend). */
    public function getStatusColorAttribute(): string
    {
        if ($this->current_stock <= 0)                    return 'danger';
        if ($this->current_stock <= $this->minimum_stock) return 'warning';
        return 'success';
    }

    /** Persentase stok tersisa dari kapasitas maksimum (jika ada). */
    public function getStockPercentageAttribute(): ?float
    {
        if (! $this->maximum_stock || $this->maximum_stock == 0) return null;
        return round(($this->current_stock / $this->maximum_stock) * 100, 1);
    }
}
