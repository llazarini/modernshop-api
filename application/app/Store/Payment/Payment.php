<?php

namespace App\Store\Payment;

interface Payment
{
    public static function payment($payment, $user, $products, $shipping);
}
