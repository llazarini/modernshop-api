<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ProductOption extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $fillable = [
        'product_id', 'option_id'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function option() {
        return $this->belongsTo(Option::class);
    }
}
