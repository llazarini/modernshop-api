<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Mail\OrderSuccessEmail;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\PaymentStatus;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\User;
use App\Rules\ValidCardDate;
use App\Rules\ValidCpf;
use App\Store\Payment\PagarmeCreditCard;
use App\Store\Shipping\MelhorEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    public function basic(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);
        $user = User::whereEmail($request->get('email'))
            ->first();
        return response()->json(!!$user);
    }

    public function shipment(Request $request)
    {
        $request->validate([
            'postal_code' => ['required'],
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.option_id' => ['required', 'exists:options,id'],
            'products.*.quantity' => ['required', 'numeric'],
        ]);
        $shippings = MelhorEnvio::calculate($request->get('postal_code'), $request->get('products'));
        if (!$shippings) {
            return response()->json([
                'message' => __('Ocorreu um erro ao tentar calcular o frete. Mas sem problemas, você ainda pode concluir sua compra!')
            ], 400);
        }
        return response()->json($shippings);
    }

    public function payment(Request $request)
    {
        $request->validate([
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.option_id' => ['required', 'exists:options,id'],
            'products.*.quantity' => ['required', 'numeric'],
            'shipping_option_id' => ['required', 'numeric'],
            'card.name' => ['required'],
            'card.number' => ['required', 'numeric'],
            'card.cvc' => ['required', 'numeric'],
            'card.date' => ['required', 'numeric', new ValidCardDate],
            'card.cpf' => ['required', new ValidCpf]
        ]);
        $user = User::with('main_address')
            ->find($request->user()->id);
        if (!$user->main_address) {
            return response()->json([
                'message' => __('Você precisa primeiro cadastrar um endereço.')
            ], 400);
        }
        $shipping = MelhorEnvio::shipping($user->main_address->zip_code,
            $request->get('products'), $request->get('shipping_option_id'));
        if (!$shipping) {
            return response()->json([
                'message' => __('Erro ao tentar trazer o envio, por favor tente mais tarde.')
            ], 400);
        }
        $payment = PagarmeCreditCard::payment($request->get('card'), $request->user(),
            $request->get('products'), $shipping);
        $order = new Order();
        $order->company_id = 1;
        $order->user_address_id = $user->main_address->id;
        $order->user_id = $user->id;
        $order->payment_type_id = PaymentType::slug('credit_card');
        $order->payment_status_id = PaymentStatus::slug($payment->status);
        $order->external_id = $payment->id;
        $order->external_type = 'pagarme';
        $order->shipment = $shipping->price;
        $order->shipment_option = sprintf('%s - %s', $shipping->name, $shipping->company);
        $order->discount = 0;
        $order->amount_without_shipment = ($payment->paid_amount / 100) - $shipping->price;
        $order->amount = $payment->paid_amount / 100;
        if (!$order->save()) {
            return response()->json([
                'message' => __('Erro ao cadastrar ordem.')
            ], 400);
        }
        $this->saveOrderItems($order, $request);
        Mail::send(new OrderSuccessEmail($order));
        return response()->json([
            'data' => $order,
            'message' => __('Pagamento aprovado')
        ], 200);
    }

    public function pix(Request $request)
    {
        $request->validate([
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.option_id' => ['required', 'exists:options,id'],
            'products.*.quantity' => ['required', 'numeric'],
            'shipping_option_id' => ['required', 'numeric'],
        ]);
        $user = User::with('main_address')
            ->find($request->user()->id);
        if (!$user->main_address) {
            return response()->json([
                'message' => __('Você precisa primeiro cadastrar um endereço.')
            ], 400);
        }
        $shipping = MelhorEnvio::shipping($user->main_address->zip_code,
            $request->get('products'), $request->get('shipping_option_id'));
        if (!$shipping) {
            return response()->json([
                'message' => __('Erro ao tentar trazer o envio, por favor tente mais tarde.')
            ], 400);
        }
        $amount = $this->amount($request->get('products'));
        $order = new Order();
        $order->company_id = 1;
        $order->user_address_id = $user->main_address->id;
        $order->user_id = $user->id;
        $order->payment_type_id = PaymentType::slug('pix');
        $order->payment_status_id = PaymentStatus::slug('waiting_payment');
        $order->external_id = 0;
        $order->external_type = 'pix';
        $order->shipment = $shipping->price;
        $order->shipment_option = sprintf('%s - %s', $shipping->name, $shipping->company);
        $order->discount = 0;
        $order->amount_without_shipment = $amount - $shipping->price;
        $order->amount = $amount;
        if (!$order->save()) {
            return response()->json([
                'message' => __('Erro ao cadastrar ordem.')
            ], 400);
        }
        $this->saveOrderItems($order, $request);
        Mail::send(new OrderSuccessEmail($order));
        return response()->json([
            'data' => $order,
            'message' => __('Pagamento aprovado')
        ], 200);
    }

    private function amount($products) {
        $total = 0;
        foreach($products as $itemProduct) {
            $product = Product::find($itemProduct['id']);
            $option = Option::find($itemProduct['option_id']);
            $price = round(((float) $product->price + ($option->type ? (float) $option->price : (float)-$option->price)) * 100, 0);
            $total = $total + ($price * (int) $itemProduct['quantity']);
        }
        return $total;
    }

    private function saveOrderItems(Order $order, Request $request)
    {
        if (!$request->get('products', [])) {
            throw new \Exception('Não existem produtos na compra.');
        }
        foreach($request->get('products', []) as $itemProduct) {
            $product = Product::find($itemProduct['id']);
            $option = Option::find($itemProduct['option_id']);
            $price = round(((float) $product->price + ($option->type ? (float) $option->price : (float)-$option->price)) * 100, 0);
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $product->id;
            $orderProduct->quantity = $itemProduct['quantity'];
            $orderProduct->price = $price;
            $orderProduct->amount = $price * $orderProduct->quantity;
            $orderProduct->save();
        }
    }
}
