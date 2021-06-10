<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Option;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $data = Product::with(['categories', 'options', 'files'])
            ->paginate(10);
        return response()->json($data, 200);
    }

    public function get(Request $request, $id)
    {
        $user = $request->user();
        $data = Product::whereCompanyId($user->company_id)
            ->with(['categories', 'options', 'files'])
            ->find($id);
        if(!$data) {
            return response()->json([
                'message' => __("Erro ao tentar retornar registro."),
            ], 400);
        }
        return response()->json($data, 200);
    }

    public function similar(Request $request, $id)
    {
        $user = $request->user();
        $similar = Product::whereCompanyId($user->company_id)
            ->join('product_category', function ($join) {
                $join
                    ->on('product_category.product_id', 'products.id')
                    ->whereNull('product_category.deleted_at');
            })
            ->limit(5)
            ->get();
        if(!$similar) {
            return response()->json([
                'message' => __("Erro ao tentar retornar registro."),
            ], 400);
        }
        return response()->json($similar, 200);
    }

    public function dataprovider(Request $request)
    {
        $categories = Category::get();
        $options = Option::get();
        return response()->json(compact('categories', 'options'), 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
            'options' => ['required', 'array'],
            'options.*' => ['required', 'exists:options,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['required', 'exists:categories,id'],
        ]);
        $user = $request->user();
        $product = Product::whereCompanyId($user->company_id)
            ->find($id);
        $product->fill($request->all());
        if(!$product->save()) {
            return response()->json([
                'message' => __("Ocorreu um erro ao tentar salvar o produto."),
            ], 400);
        }
        $product->categories()->sync($request->get('categories'));
        $product->options()->sync($request->get('options'));
        return response()->json([
            'data' => $product,
            'message' => __('Produto atualizado com sucesso.'),
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => ['required'],
            'options' => ['required', 'array'],
            'options.*' => ['required', 'exists:options,id'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['required', 'exists:categories,id'],
        ]);
        $product = new Product();
        $product->company_id = $user->company_id;
        $product->fill($request->all());
        if(!$product->save()) {
            return response()->json([
                'message' => __("Erro ao tentar cadastrar."),
            ], 400);
        }
        $product->categories()->sync($request->get('categories'));
        $product->options()->sync($request->get('options'));
        return response()->json([
            'message' => __('Produto criado com sucesso.'),
        ], 200);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $data = Product::whereCompanyId($user->company_id)
            ->find($id);
        if(!$data->delete()) {
            return response()->json([
                'message' => __("Erro ao tentar remover."),
            ], 400);
        }
        return response()->json([
            'message' => __('Produto removido com sucesso.'),
        ]);
    }
}
