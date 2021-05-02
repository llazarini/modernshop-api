<?php

namespace App\Store\Payment;

use App\Models\Option;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PagarmeCreditCard implements Payment
{
    public static function payment($card, $user, $products, $shipping)
    {
        $card = (object) $card;
        $user = User::with([
                'main_address.city.state'
            ])
            ->find($user->id);
        $address = [
            'country' => 'br',
            'street' => $user->main_address->street_name,
            'street_number' => $user->main_address->street_number,
            'state' => $user->main_address->city->state->code,
            'city' => $user->main_address->city->name,
            'neighborhood' => $user->main_address->neighborhood,
            'zipcode' => $user->main_address->zip_code
        ];
        $items = collect();
        $total = 0;
        foreach($products as $itemProduct) {
            $product = Product::find($itemProduct['id']);
            $option = Option::find($itemProduct['option_id']);
            $price = round(((float) $product->price + ($option->type ? (float) $option->price : (float)-$option->price)) * 100, 0);
            $items->push([
                'id' => (string) $product->id,
                'title' => $product->name,
                'unit_price' => $price,
                'quantity' => $itemProduct['quantity'],
                'tangible' => true
            ]);
            $total = $total + ($price * (int) $itemProduct['quantity']);
        }
        $shippingPrice = round($shipping->price * 100, 0);
        $total = $total + $shippingPrice;
        $pagarme = new \PagarMe\Client(env('PAGARME_SANDBOX_KEY'));
        $transaction = $pagarme->transactions()->create([
            'amount' => $total,
            'payment_method' => 'credit_card',
            'card_holder_name' => $card->name,
            'card_cvv' => $card->cvc,
            'card_number' => $card->number,
            'card_expiration_date' => $card->date,
            'customer' => [
                'external_id' => (string) $user->id,
                'name' => $user->name,
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                    [
                        'type' => 'cpf',
                        'number' => $card->cpf
                    ]
                ],
                'phone_numbers' => ["+55{$user->phone}"],
                'email' => $user->email
            ],
            'billing' => [
                'name' => $user->name,
                'address' => $address,
            ],
            'shipping' => [
                'name' => $user->name,
                'fee' => $shippingPrice,
                'delivery_date' => date('Y-m-d'),
                'expedited' => false,
                'address' => $address
            ],
            'items' => $items->toArray()
        ]);
        return $transaction;
    }
}
