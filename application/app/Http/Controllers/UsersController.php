<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function login(Request $request) {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => __("E-mail ou senha inválidos.")
            ], 400);
        }
        $user = User::where('email', $request->email)->first();
        if (!Hash::check($request->password, $user->password, [])) {
            return response()->json([
                'message' => __("Erro ao verificar a senha.")
            ], 400);
        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $tokenResult,
        ], 200);
    }

    /**
     * Get all
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $data = User::whereCompanyId($user->company_id)
            ->whereUserTypeId(UserType::getId('user'))
            ->paginate(10);
        return response()->json($data, 200);
    }


    /**
     * Get
     */
    public function get(Request $request, $id)
    {
        $user = $request->user();
        $data = User::whereCompanyId($user->company_id)
            ->find($id);
        if(!$data) {
            return response()->json([
                'message' => __("Erro ao tentar retornar registro."),
            ], 400);
        }
        return response()->json($data, 200);
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', sprintf('unique:users,email,%s', $id)],
            'hourly_rate' => ['required', 'numeric'],
        ]);
        $user = $request->user();
        $data = User::whereCompanyId($user->company_id)
            ->whereUserTypeId(UserType::getId('user'))
            ->find($id);
        $data->fill($request->all());
        if(!$data->save()) {
            return response()->json([
                'message' => __("Ocorreu um erro ao tentar salvar o cliente."),
            ], 400);
        }
        return response()->json([
            'data' => $data,
            'message' => __('Usere atualizado com sucesso.'),
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
            'email' => ['required', 'email', 'unique:users,email'],
            'hourly_rate' => ['required', 'numeric'],
        ]);
        $data = new User();
        $data->user_type_id = UserType::getId('user');
        $data->password = 'none';
        $data->company_id = $user->company_id;
        $data->fill($request->all());
        if(!$data->save()) {
            return response()->json([
                'message' => __("Erro ao tentar cadastrar."),
            ], 400);
        }
        return response()->json([
            'message' => __('Colaborador criado com sucesso.'),
        ], 200);
    }

    /**
     * Delete registry
     */
    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $data = User::whereCompanyId($user->company_id)
            ->whereUserTypeId(UserType::getId('user'))
            ->find($id);
        if(!$data->delete()) {
            return response()->json([
                'message' => __("Erro ao tentar remover."),
            ], 400);
        }
        return response()->json([
            'message' => __('Usere removido com sucesso.'),
        ], 200);
    }
}
