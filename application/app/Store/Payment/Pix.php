<?php

namespace App\Store\Payment;

use App\Models\Option;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class Pix implements Payment
{
    public static function payment($data, $user, $products, $shipping)
    {
        $user = User::with([
                'main_address.city.state'
            ])
            ->find($user->id);

    }
}
