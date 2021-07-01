<?php

namespace App\Store\Payment;

use App\Models\Discount;
use App\Models\DiscountOption;
use App\Models\Option;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductOption;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

trait PaymentTrait
{
    private $total, $products, $request, $shipping, $totalWithoutDiscount, $cupom, $cupomValue, $user, $discounts, $discount;
    private $installments = 1;

    function __construct() {}

    public function process(Request $request, $shipping)
    {
        $this->request = $request;
        $this->shipping = $shipping;
        $this->user = User::with(['main_address.city.state'])->find($request->user()->id);
        $this->total = 0;
        $products = collect();
        foreach($request->input('products') as $itemProduct) {
            $product = Product::find($itemProduct['id']);
            $price = $product->price;
            $product->options = collect();
            foreach($itemProduct['options'] as $optionId) {
                $option = Option::find($optionId);
                $price = $price + ($option->type ? $option->price : -$option->price);
                $product->options->push($option);
            }
            $product->total_price = $price;
            $product->quantity = $itemProduct['quantity'];
            $products->push($product);
            $this->total = $this->total + ((float) $price * (int) $product->quantity);
        }
        $this->products = $products;
        $this->totalWithoutDiscount = $this->total;
        $this->cupom($request);
        $this->discounts = $this->progressiveDiscounts($this->options(), $this->total);
        $this->subTotal = $this->total;
        $this->total = $this->total + $this->shipping->price;
    }

    public function cupom(Request $request)
    {
        $this->cupomValue = 0;
        $this->cupom = Discount::whereCode($request->get('discount'))
            ->whereCompanyId($request->get('company_id'))
            ->first();
        if ($this->cupom) {
            $this->cupomValue = $this->total - ($this->cupom->type == 'percentage' ? $this->total * (1 - ($this->cupom->value / 100)) : $this->total - $this->cupom->value);
            $this->total = $this->total - $this->cupomValue;
        }
        $this->discount += $this->cupomValue;
    }

    private function orderProducts(Order $order)
    {
        if (!$this->products) {
            throw new \Exception('Não existem produtos na compra.');
        }
        foreach($this->products as $product) {
            $orderProduct = new OrderProduct();
            $orderProduct->order_id = $order->id;
            $orderProduct->product_id = $product->id;
            $orderProduct->quantity = $product->quantity;
            $orderProduct->price = $product->total_price;
            $orderProduct->amount = $product->total_price * $product->quantity;
            $orderProduct->save();
            foreach($product->options as $productOption) {
                $option = new OrderProductOption();
                $option->order_product_id = $orderProduct->id;
                $option->option_id = $productOption->id;
                $option->save();
            }
        }
    }

    private function options() {
        $options = [];
        foreach($this->products as $product) {
            foreach($product->options as $option) {
                if (!isset($options[$option->id])) {
                    $options[$option->id] = (object) ['product_id' => $product->id, 'quantity' => $product->quantity];
                    continue;
                }
                $options[$option->id]->quantity += $product->quantity;
            }
        }
        return $options;
    }

    private function progressiveDiscounts($options, float &$total = 0) {
        $discounts = collect();
        foreach($options as $option => $item) {
            $discount = DiscountOption::select('discounts.name', 'discount_options.value as value', 'options.price', 'options.type')
                ->whereOptionId($option)
                ->join('discounts', function($join) {
                    $join
                        ->on('discounts.id', 'discount_options.discount_id')
                        ->whereNull('discounts.deleted_at');
                })
                ->join('options', function($join) {
                    $join
                        ->on('options.id', 'discount_options.option_id')
                        ->whereNull('options.deleted_at');
                })
                ->where('min_products', '<=', $item->quantity)
                ->where('max_products', '>=', $item->quantity)
                ->first();
            if ($discount) {
                $product = Product::find($item->product_id);
                $price = ($product->price + ($discount->type ? +$discount->price : -$discount->price)) * $item->quantity;
                $discount->value = ($discount->value / 100) * $price;
                $discounts->push($discount);
                $this->discount += $discount->value;
                $total = $total - $discount->value;
            }
        }
        return $discounts;
    }

    private function order() {
        $order = new Order();
        return $order->fill([
            'company_id' => $this->request->get('company_id'),
            'user_id' => $this->user->id,
            'user_address_id' => $this->user->main_address->id,
            'discount' => $this->discount,
            'amount_without_shipment' => $this->total - $this->shipping->price,
            'amount_without_discount' => $this->totalWithoutDiscount,
            'amount' => $this->total,
            'shipment' => $this->shipping->price,
            'installments' => $this->installments,
            'shipping_option_id' => $this->shipping->id
        ]);
    }
}
