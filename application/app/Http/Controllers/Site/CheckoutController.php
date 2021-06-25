<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\OrderSuccessEmail;
use App\Models\Discount;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductOption;
use App\Models\PaymentStatus;
use App\Models\PaymentType;
use App\Models\Product;
use App\Models\User;
use App\Rules\ValidCardDate;
use App\Rules\ValidCpf;
use App\Store\Payment\PagarmeCreditCard;
use App\Store\Shipping\MelhorEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
            'products.*.options' => ['required', 'array'],
            'products.*.options.*' => ['required', 'exists:options,id'],
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

    public function discountCode(Request $request)
    {
        $request->validate([
            'discount_code' => ['required'],
        ]);
        $discount = Discount::whereCode($request->input('discount_code'))
            ->first();
        if (!$discount) {
            return response()->json([
                'message' => __('Não encontramos nenhum código de cupom.')
            ], 400);
        }
        return response()->json($discount);
    }

    public function payment(Request $request)
    {
        $request->validate([
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.options' => ['required'],
            'products.*.options.*' => ['required', 'exists:options,id'],
            'products.*.quantity' => ['required', 'numeric'],
            'shipping_option_id' => ['required', 'numeric'],
            'card.name' => ['required'],
            'card.number' => ['required', 'numeric'],
            'card.cvc' => ['required', 'numeric'],
            'card.date' => ['required', 'numeric', new ValidCardDate],
            'card.cpf' => ['required', new ValidCpf],
            'card.installments' => ['required', 'numeric', 'min:1', 'max:12'],
            'discount' => ['nullable', "exists:discounts,code,company_id,{$request->get('company_id')}"]
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
        $discount = Discount::whereCode($request->get('discount'))
            ->whereCompanyId($request->get('company_id'))
            ->first();
        try {
            $payment = PagarmeCreditCard::payment(
                $request->get('card'),
                $request->user(),
                $request->get('products'),
                $shipping,
                $discount);
        } catch (\Exception $exception) {
            Log::emergency($exception->getTraceAsString());
            Log::emergency($exception->getMessage());
            return response()->json([
                'message' => __('Puxa :/ Ocorreu um erro no processamento do seu pagamento. Mas tente mais
                tarde! Os desenvolvedores já foram avisados.')
            ], 400);
        }
        $order = new Order();
        $order->company_id = $request->get('company_id');
        $order->user_address_id = $user->main_address->id;
        $order->user_id = $user->id;
        $order->payment_type_id = PaymentType::slug('credit_card');
        $order->fill((array) $payment);
        if (!$order->save()) {
            return response()->json([
                'message' => __('Erro ao cadastrar ordem.')
            ], 400);
        }
        $this->saveOrderItems($order, $request);
        if (!in_array($payment->status->slug, ['processing', 'authorized', 'paid', 'waiting_payment'])) {
            return response()->json([
                'message' => [
                    'title' => __('Aconteceu algum problema com seu pagamento', [
                        'status' => $payment->status->name
                    ]),
                    'message' => __('Ocorreu algum erro com o seu pagamento, o seu pagamento foi :status. Por favor, revise os dados do seu cartão de crédito.', [
                        'status' => $payment->status->name
                    ])
                ]
            ], 400);
        }
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
            'products.*.options' => ['required', 'array'],
            'products.*.options.*' => ['required', 'exists:options,id'],
            'products.*.quantity' => ['required', 'numeric'],
            'shipping_option_id' => ['required', 'numeric'],
            'discount' => ['nullable', "exists:discounts,code,company_id,{$request->get('company_id')}"]
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
        $discount = Discount::whereCode($request->get('discount'))
            ->whereCompanyId($request->get('company_id'))
            ->first();
        if (!$shipping) {
            return response()->json([
                'message' => __('Erro ao tentar trazer o envio, por favor tente mais tarde.')
            ], 400);
        }
        $amount = $this->amount($request->get('products'));
        $discountPrice = Discount::applyDiscount($amount, $discount);
        $order = new Order();
        $order->company_id = $request->get('company_id');
        $order->user_address_id = $user->main_address->id;
        $order->user_id = $user->id;
        $order->payment_type_id = PaymentType::slug('pix');
        $order->payment_status_id = PaymentStatus::slug('waiting_payment');
        $order->external_id = 0;
        $order->external_type = 'pix';
        $order->shipment = $shipping->price;
        $order->shipping_option_id = $request->get('shipping_option_id');
        $order->discount = $discountPrice;
        $order->amount_without_discount = $amount + $discountPrice;
        $order->amount_without_shipment = $amount;
        $order->amount = $amount + $shipping->price;
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
            $price = $product->price;
            foreach($itemProduct['options'] as $optionId) {
                $option = Option::find($optionId);
                $price += $option->type ? (float) $option->price : (float)-$option->price;
            }
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
            $price = $product->price;
            foreach($itemProduct['options'] as $optionId) {
                $option = Option::find($optionId);
                $price += ($option->type ? (float) $option->price : (float)-$option->price);
            }
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $product->id;
            $orderProduct->quantity = $itemProduct['quantity'];
            $orderProduct->price = $price;
            $orderProduct->amount = $price * $orderProduct->quantity;
            $orderProduct->save();

            foreach($itemProduct['options'] as $optionId) {
                $option = new OrderProductOption();
                $option->order_product_id = $orderProduct->id;
                $option->option_id = $optionId;
                $option->save();
            }
        }
    }
}
