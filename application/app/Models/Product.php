<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends BaseModel
{
    use SoftDeletes, FileTrait;

    public static $searchFields = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sku', 'name', 'description', 'meta_name', 'meta_description', 'meta_keys', 'stock', 'price', 'price_cost'
    ];

}
