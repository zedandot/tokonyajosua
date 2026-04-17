<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'description',
        'purchase_price',
        'selling_price',
        'unit',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price'  => 'decimal:2',
            'is_active'      => 'boolean',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    /** Hanya produk aktif. */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /** Relasi 1:1 ke inventori. */
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /** Margin keuntungan dalam persen. */
    public function getMarginPercentAttribute(): float
    {
        if ($this->purchase_price == 0) return 0;
        return round((($this->selling_price - $this->purchase_price) / $this->purchase_price) * 100, 2);
    }

    /** Stok saat ini dari relasi inventori. */
    public function getCurrentStockAttribute(): int
    {
        return $this->inventory?->current_stock ?? 0;
    }

    /** True jika stok <= minimum stok. */
    public function getIsLowStockAttribute(): bool
    {
        return ($this->inventory?->current_stock ?? 0) <= ($this->inventory?->minimum_stock ?? 0);
    }
}
