<?php

namespace App\Store\Payment;

use App\Models\Discount;
use App\Models\Order;
use App\Models\User;

class Pix implements Payment
{
    public static function payment($card, User $user, $products, $shipping, ?Discount $discount)
    {
        $user = User::with([
                'main_address.city.state'
            ])
            ->find($user->id);

    }

    public static function refund(Order $order)
    {
        return true;
    }
}
