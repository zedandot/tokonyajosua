<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\SoftDeletes; // 🟢 UBAH KE JALUR MONGODB

class Product extends Model
{
    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'products';

    protected $fillable = [
        'category_id', 
        'name', 
        'sku', 
        'purchase_price', 
        'selling_price', 
        'unit', 
        'is_active'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
}