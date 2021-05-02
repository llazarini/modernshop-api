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
        $orders = Order::whereUserId($user->id)
            ->paginate();
        return response()->json($orders);
    }

    public function show(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:orders,id']
        ]);
        $user = $request->user();
        $products = Order::whereUserId($user->id)
            ->find($request->get('id'));
        return response()->json($products);
    }
}
