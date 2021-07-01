<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountOption;
use App\Models\Option;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class DiscountsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $data = Discount::paginate(10);
        return response()->json($data, 200);
    }

    public function get(Request $request, $id)
    {
        $user = $request->user();
        $data = Discount::whereCompanyId($user->company_id)
            ->with('discount_options')
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
        $user = $request->user();
        $options = Option::get();
        return response()->json(compact('options'), 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
            'type' => ['required', 'in:value,percentage,programmatic'],
            'code' => ['nullable', 'min:2'],
            'value' => ['nullable', 'numeric'],
            'discount_options' => ['requiredif:type,programmatic', 'nullable'],
            'discount_options.*.id' => ['nullable', 'exists:discount_options,id'],
            'discount_options.*.min_products' => ['required', 'numeric'],
            'discount_options.*.max_products' => ['required', 'numeric'],
            'discount_options.*.value' => ['required', 'numeric'],
        ]);
        $user = $request->user();
        $discount = Discount::whereCompanyId($user->company_id)->find($id);
        $discount->fill($request->all());

        if(!$discount->save()) {
            return response()->json([
                'message' => __("Ocorreu um erro ao tentar salvar o cupom de desconto."),
            ], 400);
        }
        foreach($request->input('discount_options') as $item) {
            if (isset($item['id'])) {
                $discountOption = DiscountOption::find($item['id']);
            } else {
                $discountOption = new DiscountOption();
            }
            $discountOption->fill($item);
            $discountOption->discount_id = $discount->id;
            $discountOption->save();
        }
        return response()->json([
            'data' => $discount,
            'message' => __('Cupom de desconto atualizado com sucesso.'),
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => ['required'],
            'type' => ['required', 'in:value,percentage,programmatic'],
            'code' => ['nullable', 'min:2'],
            'value' => ['nullable', 'numeric'],
            'discount_options' => ['requiredif:type,programmatic', 'nullable'],
            'discount_options.*.id' => ['nullable', 'exists:discount_options,id'],
            'discount_options.*.min_products' => ['required', 'numeric'],
            'discount_options.*.max_products' => ['required', 'numeric'],
            'discount_options.*.value' => ['required', 'numeric'],
        ]);
        $discount = new Discount();
        $discount->company_id = $user->company_id;
        $discount->fill($request->all());
        if(!$discount->save()) {
            return response()->json([
                'message' => __("Erro ao tentar cadastrar."),
            ], 400);
        }

        foreach($request->input('discount_options') as $item) {
            $discountOption = new DiscountOption();
            $discountOption->fill($item);
            $discountOption->discount_id = $discount->id;
            $discountOption->save();
        }

        return response()->json([
            'message' => __('Cupom de desconto criado com sucesso.'),
        ], 200);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $data = Discount::whereCompanyId($user->company_id)
            ->find($id);
        if(!$data->delete()) {
            return response()->json([
                'message' => __("Erro ao tentar remover."),
            ], 400);
        }
        return response()->json([
            'message' => __('Cupom de desconto removido com sucesso.'),
        ]);
    }
}
