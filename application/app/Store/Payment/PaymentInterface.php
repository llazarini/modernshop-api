<?php

namespace App\Store\Payment;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentInterface
{
    public function payment(Request $request, $shipping);
    public function refund(Order $order);
}
