<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $table = 'order_product';

    public static $searchFields = ['name'];

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'price', 'amount',
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function order_product_options() {
        return $this->hasMany(OrderProductOption::class);
    }
}
