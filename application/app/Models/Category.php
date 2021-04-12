<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends BaseModel
{
    use SoftDeletes, FileTrait;

    public static $searchFields = ['name'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug', 'name', 'description',
    ];

    public function products() {
        return $this->belongsToMany(Product::class, 'product_category');
    }
}
