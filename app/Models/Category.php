<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes; 
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'categories';

    protected $fillable = ['name', 'slug', 'description'];

    // Auto-generate slug dari name
    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}