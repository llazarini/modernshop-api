<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::with([
                'payment_status',
                'payment_type',
                'order_products.product',
                'user_address' => function($with) {
                    $with->with(['city', 'state']);
                }
            ])
            ->whereUserId($user->id)
            ->orderBy('id', 'desc')
            ->paginate();
        return response()->json($orders);
    }

    public function show(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:orders,id']
        ]);
        $user = $request->user();
        $products = Order::with('payment_status')
            ->whereUserId($user->id)
            ->find($request->get('id'));
        return response()->json($products);
    }
}