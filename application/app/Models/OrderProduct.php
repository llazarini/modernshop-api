<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProduct extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $table = 'order_product';

    public static $searchFields = ['name'];

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'amount',
    ];
}
