<?php

namespace App\Store\Payment;

use App\Models\Order;
use App\Models\PaymentStatus;
use App\Models\PaymentType;
use Illuminate\Http\Request;
use PagarMe\Client;

class PagarmeCreditCard implements PaymentInterface
{
    use PaymentTrait;

    private static function client()
    {
        return new Client(env('APP_ENV') === 'production' ? env('PAGARME_LIVE_KEY') : env('PAGARME_SANDBOX_KEY'));
    }

    public function payment(Request $request, $shipping)
    {
        $this->process($request, $shipping);
        $card = (object) $request->input('card');
        $this->installments = $card->installments;
        $transaction = $this->pagarmePayment($card);
        return $this->processOrder($transaction);
    }

    public function processOrder($transaction) {
        $status = PaymentStatus::whereSlug($transaction->status)->first();
        $order = $this->order();
        $order->fill([
            'external_id' => $transaction->id,
            'external_type' => $this->getExternalType(),
            'payment_type_id' => PaymentType::slug('credit_card'),
            'status' => $status->slug,
            'payment_status_id' => $status->id,
        ]);
        $order->saveOrFail();
        $this->orderProducts($order);
        return $order;
    }

    public function refund(Order $order)
    {
        if ($order->external_type != $this->getExternalType()) {
            return false;
        }
        $client = PagarmeCreditCard::client();
        $response = $client->transactions()->refund([
            'id' => $order->external_id,
        ]);
        return $response;
    }

    private function getExternalType()
    {
        return 'pagarme';
    }

    private function address()
    {
        return [
            'country' => 'br',
            'street' => $this->user->main_address->street_name,
            'street_number' => $this->user->main_address->street_number,
            'state' => $this->user->main_address->city->state->code,
            'city' => $this->user->main_address->city->name,
            'neighborhood' => $this->user->main_address->neighborhood,
            'zipcode' => $this->user->main_address->zip_code
        ];
    }

    private function items()
    {
        $items = collect();
        foreach($this->products as $product) {
            $items->push([
                'id' => (string) $product->id,
                'title' => $product->name,
                'unit_price' => round($product->total_price * 100, 0),
                'quantity' => $product->quantity,
                'tangible' => true
            ]);
        }
        return $items;
    }

    private function pagarmePayment($card)
    {
        $pagarme = PagarmeCreditCard::client();
        $address = $this->address();
        $items = $this->items();
        return $pagarme->transactions()->create([
            'amount' => round(($this->total) * 100, 0),
            'payment_method' => 'credit_card',
            'card_holder_name' => $card->name,
            'card_cvv' => $card->cvc,
            'card_number' => $card->number,
            'card_expiration_date' => $card->date,
            'installments' => $this->installments,
            'customer' => [
                'external_id' => (string) $this->user->id,
                'name' => $this->user->name,
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                    [
                        'type' => 'cpf',
                        'number' => $card->cpf
                    ]
                ],
                'phone_numbers' => [strlen($this->user->phone) == 11 ? "+55{$this->user->phone}" : "+5511972855395"],
                'email' => $this->user->email
            ],
            'billing' => [
                'name' => $this->user->name,
                'address' => $address,
            ],
            'shipping' => [
                'name' => $this->user->name,
                'fee' => round($this->shipping->price * 100, 0),
                'delivery_date' => date('Y-m-d'),
                'expedited' => false,
                'address' => $address
            ],
            'items' => $items->toArray()
        ]);
    }
}
