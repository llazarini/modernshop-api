<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends BaseModel
{
    use SoftDeletes, FileTrait;

    public static $searchFields = ['name'];

    protected $fillable = [
        'payment_type_id', 'payment_status_id', 'amount_without_discount', 'amout', 'discount',
    ];
}
