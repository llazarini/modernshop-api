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

    public function payment_type() {
        return $this->belongsTo(PaymentType::class);
    }

    public function payment_status() {
        return $this->belongsTo(PaymentStatus::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function user_address() {
        return $this->belongsTo(UserAddress::class);
    }

    public function order_products() {
        return $this->hasMany(OrderProduct::class);
    }
}
