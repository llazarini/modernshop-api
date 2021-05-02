<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use SoftDeletes, FileTrait;

    public static $searchFields = ['name'];

    protected $fillable = [
        'sku', 'name', 'description', 'meta_name', 'meta_description', 'meta_keys', 'stock', 'price', 'price_cost'
    ];

    public function categories() {
        return $this->belongsToMany(Category::class, 'product_category');
    }

    public function options() {
        return $this->belongsToMany(Option::class, 'product_option');
    }
}
