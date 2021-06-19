<?php

namespace App\Models;

use App\Store\Payment\PagarmeCreditCard;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends BaseModel
{
    use SoftDeletes, FileTrait;

    public static $searchFields = ['name'];

    protected $fillable = [
        'payment_type_id', 'payment_status_id', 'amount_without_discount', 'amount', 'discount', 'tracking_code',
        'external_id', 'external_type', 'shipping_option_id', 'shipment', 'tracking_code', 'amount_without_shipment',

    ];

    public static function refund($order)
    {
        if ($order->external_type == 'pix') {
            return true;
        } else if ($order->external_type == 'pagarme') {
            return PagarmeCreditCard::refund($order);
        }
        return false;
    }

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
