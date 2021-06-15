<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends BaseModel
{
    use SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'code', 'value', 'type'
    ];

    public static function applyDiscount(float &$total, ?Discount $discount) {
        $discountValue = 0;
        if ($discount) {
            $discountValue = $total - ($discount->type == 'percentage' ? $total * (1 - ($discount->value / 100)) : $total - $discount->value);
            $total = $total - $discountValue;
        }
        return $discountValue;
    }
}
