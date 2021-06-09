<?php

namespace App\Store\Payment;

use App\Models\Order;
use App\Models\User;

interface Payment
{
    public static function payment($payment, User $user, $products, $shipping);
    public static function refund(Order $order);
}
