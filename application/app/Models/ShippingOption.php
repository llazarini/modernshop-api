<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingOption extends BaseModel
{
    use SoftDeletes;
    protected $fillable = [
        'shipping_company_id',
        'name',
    ];
}
