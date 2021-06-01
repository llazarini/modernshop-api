<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OrderUpdatedEmail;
use App\Models\Order;
use App\Models\PaymentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $data = Order::with([
            'payment_type',
            'payment_status',
            'order_products' => function($with) {
                $with->with([
                    'product' => function($with) {
                        $with->withTrashed();
                    },
                    'options' => function($with) {
                        $with->withTrashed();
                    }
                ]);
            }])
            ->orderBy('id', 'desc')
            ->paginate(10);
        return response()->json($data, 200);
    }

    public function get(Request $request, $id)
    {
        $user = $request->user();
        $data = Order::whereCompanyId($user->company_id)
            ->find($id);
        if(!$data) {
            return response()->json([
                'message' => __("Erro ao tentar retornar registro."),
            ], 400);
        }
        return response()->json($data, 200);
    }

    public function dataprovider(Request $request)
    {
        $payment_statuses = PaymentStatus::get();
        return response()->json(compact('payment_statuses'), 200);
    }

    public function status(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:orders,id'],
            'status' => ['required', 'exists:payment_statuses,slug']
        ]);
        $order = Order::find($request->get('id'));
        if ($order->payment_status_id === PaymentStatus::slug($request->get('status'))) {
            return response()->json([
                'message' => __('O pedido já se encontra nesse status de atualização.', 400)
            ]);
        }
        $order->payment_status_id = PaymentStatus::slug($request->get('status'));
        if(!$order->save()) {
            return response()->json([
                'message' => __('Erro ao atualizar status do pedido.', 400)
            ]);
        }
        Mail::send(new OrderUpdatedEmail($order));
        return response()->json([
            'message' => __('O status foi atualizado com sucesso.')
        ]);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $data = Order::whereCompanyId($user->company_id)
            ->find($id);
        if(!$data->delete()) {
            return response()->json([
                'message' => __("Erro ao tentar remover."),
            ], 400);
        }
        return response()->json([
            'message' => __('Categoria removida com sucesso.'),
        ]);
    }
}
