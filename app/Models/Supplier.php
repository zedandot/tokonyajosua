<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes; // 🟢 UBAH KE JALUR MONGODB

class Supplier extends Model
{
    use SoftDeletes;

    // 🟢 KUNCI MUTLAK MONGODB
    protected $connection = 'mongodb';
    protected $collection = 'suppliers';

    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'phone',
        'email',
        'address',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    /** Semua dokumen penerimaan barang dari supplier ini. */
    public function stockReceivings()
    {
        return $this->hasMany(StockReceiving::class);
    }
}