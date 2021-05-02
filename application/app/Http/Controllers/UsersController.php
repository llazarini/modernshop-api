<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPasswordEmail;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UsersController extends Controller
{
    public function me(Request $request)
    {
        $user = User::with('main_address.city.state')
            ->find($request->user()->id);
        return response()->json($user);
    }

    public function forgot(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);
        Mail::send(new ForgotPasswordEmail($request->get('email')));
        return response()->json(true);
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => __("E-mail ou senha inválidos.")
            ], 400);
        }
        $user = User::where('email', $request->email)
            ->with('main_address')
            ->first();
        if (!Hash::check($request->password, $user->password, [])) {
            return response()->json([
                'message' => __("Erro ao verificar a senha.")
            ], 400);
        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $tokenResult,
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'name' => ['required', 'max:200', 'min:10'],
            'password' => ['required', 'min:6', 'max:200', 'same:password'],
            'password_confirm' => ['required', 'min:6', 'max:200'],
        ]);
        $user = new User();
        $user->company_id = 1;
        $user->user_type_id = UserType::getId('client');
        $user->fill($request->all());
        if (!$user->save()) {
            return response()->json([
                'message' => __('Erro ao criar usuário')
            ]);
        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        return response()->json([
            'user' => $user,
            'token' => $tokenResult,
        ]);
    }

    public function address(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'zip_code' => ['required', 'min:8', 'max:8'],
            'street_name' => ['required', 'min:3'],
            'street_number' => ['required', 'numeric'],
            'complement' => ['nullable'],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
        ]);
        UserAddress::whereUserId($user->id)
            ->update(['main' => false]);
        $address = new UserAddress();
        $address->fill($request->all());
        $address->user_id = $user->id;
        if (!$address->save()) {
            return response()->json([
                'message' => __('O endereço não foi cadastrado.')
            ], 400);
        }
        return response()->json([
            'message' => __('Sucesso ao criar endereço.')
        ]);
    }
}
