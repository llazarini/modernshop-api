<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentStatus extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'slug'
    ];

    public static function getStatus($description) {
        $paymentStatus = PaymentStatus::whereSlug($description)
            ->first();
        return $paymentStatus ? $paymentStatus->id : null;
    }
}
