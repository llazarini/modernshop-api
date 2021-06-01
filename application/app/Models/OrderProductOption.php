<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class OrderProductOption extends BaseModel
{
    use SoftDeletes, FileTrait;

    protected $table = 'order_product_option';

    public static $searchFields = ['name'];

    protected $fillable = [
        'order_product', 'option_id',
    ];

    public function order_product() {
        return $this->belongsTo(OrderProduct::class);
    }

    public function option() {
        return $this->belongsTo(Option::class);
    }
}
