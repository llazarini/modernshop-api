<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Mail\OrderSuccessEmail;
use App\Models\Discount;
use App\Models\User;
use App\Rules\ValidCardDate;
use App\Rules\ValidCpf;
use App\Store\Payment\PagarmeCreditCard;
use App\Store\Payment\PaymentTrait;
use App\Store\Payment\Pix;
use App\Store\Shipping\MelhorEnvio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
    use PaymentTrait;

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
            'postal_code' => ['nullable'],
            'products' => ['required', 'array'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.options' => ['required', 'array'],
            'products.*.options.*' => ['required', 'exists:options,id'],
            'products.*.quantity' => ['required', 'numeric'],
        ]);
        $products = collect($request->get('products'));
        $discounts = $this->progressiveDiscounts($this->options($products));
        $shippings = [];
        if ($request->filled('postal_code')) {
            $shippings = MelhorEnvio::calculate($request->input('postal_code'), $request->input('products'));
            if (!$shippings) {
                return response()->json([
                    'message' => __('Ocorreu um erro ao tentar calcular o frete. Mas sem problemas, você ainda pode concluir sua compra!')
                ], 400);
            }
        }
        return response()->json(compact('shippings', 'discounts'));
    }

    private function options($products) {
        $options = [];
        foreach($products as $product) {
            foreach($product['options'] as $option) {
                if (!isset($options[$option])) {
                    $options[$option] = (object) ['product_id' => $product['id'], 'quantity' => $product['quantity']];
                    continue;
                }
                $options[$option]->quantity += $product['quantity'];
            }
        }
        return $options;
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
        $shipping = MelhorEnvio::shipping(
            $user->main_address->zip_code,
            $request->get('products'),
            $request->get('shipping_option_id')
        );
        if (!$shipping) {
            return response()->json([
                'message' => __('Erro ao tentar trazer o envio, por favor tente mais tarde.')
            ], 400);
        }
        try {
            $payment = new PagarmeCreditCard();
            $order = $payment->payment($request, $shipping);
        } catch (\Exception $exception) {
            Log::emergency($exception->getTraceAsString());
            Log::emergency($exception->getMessage());
            return response()->json([
                'message' => __('Puxa :/ Ocorreu um erro no processamento do seu pagamento. Mas tente mais
                tarde! Os desenvolvedores já foram avisados.')
            ], 400);
        }

        if (!in_array($order->payment_status->slug, ['processing', 'authorized', 'paid', 'waiting_payment'])) {
            return response()->json([
                'message' => [
                    'title' => __('Aconteceu algum problema com seu pagamento', [
                        'status' => $order->payment_status->status->name
                    ]),
                    'message' => __('Ocorreu algum erro com o seu pagamento, o seu pagamento foi :status. Por favor, revise os dados do seu cartão de crédito.', [
                        'status' => $order->payment_status->status->name
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
        if (!$shipping) {
            return response()->json([
                'message' => __('Erro ao tentar trazer o envio, por favor tente mais tarde.')
            ], 400);
        }
        $payment = new Pix();
        $order = $payment->payment($request, $shipping);
        if (!$order) {
            return response()->json([
                'message' => __('Erro ao processar ordem de pagamento.')
            ], 400);
        }
        Mail::send(new OrderSuccessEmail($order));
        return response()->json([
            'data' => $order,
            'message' => __('Pagamento aprovado')
        ], 200);
    }
}
