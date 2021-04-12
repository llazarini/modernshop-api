<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $data = Category::paginate(10);
        return response()->json($data, 200);
    }

    public function get(Request $request, $id)
    {
        $user = $request->user();
        $data = Category::whereCompanyId($user->company_id)
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
        return response()->json([], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
        ]);
        $user = $request->user();
        $data = Category::whereCompanyId($user->company_id)
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

    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => ['required'],
        ]);
        $data = new Category();
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

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $data = Category::whereCompanyId($user->company_id)
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
