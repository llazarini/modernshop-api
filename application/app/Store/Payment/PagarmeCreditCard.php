<?php

namespace App\Store\Payment;

use App\Models\Discount;
use App\Models\Option;
use App\Models\Order;
use App\Models\PaymentStatus;
use App\Models\Product;
use App\Models\User;
use PagarMe\Client;

class PagarmeCreditCard implements Payment
{
    private static function client()
    {
        return new Client(env('APP_ENV') === 'production' ? env('PAGARME_LIVE_KEY') : env('PAGARME_SANDBOX_KEY'));
    }

    public static function payment($card, User $user, $products, $shipping, ?Discount $discount)
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
            $price = $product->price;
            foreach($itemProduct['options'] as $optionId) {
                $option = Option::find($optionId);
                $price += $option->type ? $option->price : -$option->price;
            }
            $items->push([
                'id' => (string) $product->id,
                'title' => $product->name,
                'unit_price' => round($price * 100, 0),
                'quantity' => $itemProduct['quantity'],
                'tangible' => true
            ]);
            $total = $total + ((float) $price * (int) $itemProduct['quantity']);
        }
        Discount::applyDiscount($total, $discount);
        $pagarme = PagarmeCreditCard::client();
        $transaction = $pagarme->transactions()->create([
            'amount' => round(($total + $shipping->price) * 100, 0),
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
                'phone_numbers' => [strlen($user->phone) == 11 ? "+55{$user->phone}" : "+5511972855395"],
                'email' => $user->email
            ],
            'billing' => [
                'name' => $user->name,
                'address' => $address,
            ],
            'shipping' => [
                'name' => $user->name,
                'fee' => round($shipping->price * 100, 0),
                'delivery_date' => date('Y-m-d'),
                'expedited' => false,
                'address' => $address
            ],
            'items' => $items->toArray()
        ]);
        $status = PaymentStatus::whereSlug($transaction->status)->first();
        return (object) [
            'external_id' => $transaction->id,
            'external_type' => 'pagarme',
            'discount' => isset($discountValue) ? $discountValue : 0,
            'amount_without_shipment' => ($transaction->paid_amount / 100) - $shipping->price,
            'amount_without_discount' => $total + $discountValue,
            'amount' => $transaction->paid_amount / 100,
            'status' => $status,
            'payment_status_id' => $status->id,
            'shipment' => $shipping->price,
            'shipping_option_id' => $shipping->id,
        ];
    }

    public static function refund(Order $order)
    {
        if ($order->external_type != self::getExternalType()) {
            return false;
        }
        $client = PagarmeCreditCard::client();
        $response = $client->transactions()->refund([
            'id' => $order->external_id,
        ]);
        return $response;
    }

    private static function getExternalType()
    {
        return 'pagarme';
    }
}
