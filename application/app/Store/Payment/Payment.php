<?php

namespace App\Store\Payment;

use App\Models\Discount;
use App\Models\Order;
use App\Models\User;

interface Payment
{
    public static function payment($payment, User $user, $products, $shipping, ?Discount $discount);
    public static function refund(Order $order);
}
