<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Get all
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $data = Product::paginate(10);
        return response()->json($data, 200);
    }

    /**
     * Get
     */
    public function get(Request $request, $id)
    {
        $user = $request->user();
        $data = Product::whereCompanyId($user->company_id)
            ->find($id);
        if(!$data) {
            return response()->json([
                'message' => __("Erro ao tentar retornar registro."),
            ], 400);
        }
        return response()->json($data, 200);
    }

    /**
     * Dataprovider
     */
    public function dataprovider(Request $request)
    {
        $user = $request->user();
        return response()->json([], 200);
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $user = $request->user();
        $data = Product::whereCompanyId($user->company_id)
            ->find($id);
        $data->fill($request->all());
        if(!$data->save()) {
            return response()->json([
                'message' => __("Ocorreu um erro ao tentar salvar o produto."),
            ], 400);
        }
        return response()->json([
            'data' => $data,
            'message' => __('Produto atualizado com sucesso.'),
        ], 200);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => ['required'],
        ]);
        $data = new Product();
        $data->company_id = $user->company_id;
        $data->fill($request->all());
        if(!$data->save()) {
            return response()->json([
                'message' => __("Erro ao tentar cadastrar."),
            ], 400);
        }
        return response()->json([
            'message' => __('Produto criado com sucesso.'),
        ], 200);
    }

    /**
     * Delete registry
     */
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
