<?php

namespace App\Store\Payment;

use App\Models\Order;
use App\Models\PaymentStatus;
use App\Models\PaymentType;
use Illuminate\Http\Request;

class Pix implements PaymentInterface
{
    use PaymentTrait;

    private function getExternalType()
    {
        return 'pix';
    }

    public function payment(Request $request, $shipping)
    {
        $this->process($request, $shipping);
        return $this->processOrder();
    }

    public function processOrder() {
        $order = $this->order();
        $status = PaymentStatus::whereSlug('waiting_payment')->first();
        $order->fill([
            'external_id' => 0,
            'external_type' => $this->getExternalType(),
            'payment_type_id' => PaymentType::slug('pix'),
            'status' => $status->slug,
            'payment_status_id' => $status->id,
        ]);
        $order->saveOrFail();
        $this->orderProducts($order);
        return $order;
    }

    public function refund(Order $order)
    {
        return true;
    }
}
