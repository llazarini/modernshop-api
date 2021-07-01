<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class DiscountOption extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'discount_id', 'option_id', 'min_products', 'max_products', 'value'
    ];
}
