<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // ─── Helper: Role Checks ───────────────────────────────────────────────────

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    public function isGudang(): bool
    {
        return $this->role === 'gudang';
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    /** Transaksi penjualan yang diproses oleh user ini (kasir). */
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    /** Pergerakan stok yang dicatat oleh user ini. */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
