<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $orders = Order::whereCompanyId($request->get('company_id'))
            ->with([
                'payment_status',
                'payment_type',
                'order_products' => function($with) {
                    $with->with([
                        'product' => function($with) {
                            $with->withTrashed();
                        },
                        'order_product_options.option' => function($with) {
                            $with
                                ->withTrashed();
                        }
                    ]);
                },
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
        $products = Order::whereCompanyId($request->get('company_id'))
            ->with(['payment_status', 'payment_type'])
            ->whereUserId($user->id)
            ->find($request->get('id'));
        return response()->json($products);
    }
}
