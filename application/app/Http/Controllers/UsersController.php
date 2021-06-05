<?php

namespace App\Http\Controllers;

use App\Mail\CreateAccountEmail;
use App\Mail\ForgotPasswordEmail;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\UserType;
use App\Rules\ValidCep;
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
            'email' => ['required', 'email', 'exists:users,email']
        ]);
        $user = User::whereEmail($request->get('email'))->first();
        $user->remember_token = md5($user->id . $user->email . time() . rand(1,1000));
        if (!$user->save()) {
            return response()->json(['message' => __("Usuário não pode ser salvo")], 400);
        }
        Mail::send(new ForgotPasswordEmail($user));
        return response()->json(true);
    }

    public function remember_token(Request $request)
    {
        $request->validate([
            'code' => ['required', 'exists:users,remember_token'],
        ]);
        return response()->json([]);
    }

    public function password(Request $request)
    {
        $request->validate([
            'code' => ['required', 'exists:users,remember_token'],
            'password' => ['required', 'min:6', 'max:200', 'same:password_confirm'],
            'password_confirm' => ['required', 'min:6', 'max:200'],
        ]);
        $user = User::whereRememberToken($request->get('code'))->first();
        $user->remember_token = null;
        $user->password = Hash::make($request->get('password'));
        if (!$user->save()) {
            return response()->json([
                'message' => __("Erro ao cadastrada nova senha.")
            ], 400);
        }
        return response()->json([
            'message' => __("Nova senha cadastrada com sucesso.")
        ]);
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
            'password' => ['required', 'min:6', 'max:200', 'same:password_confirm'],
            'password_confirm' => ['required', 'min:6', 'max:200'],
            'phone' => ['required', 'min:6', 'max:200'],
        ]);
        $user = new User();
        $user->fill($request->all());
        $user->company_id = 1;
        $user->user_type_id = UserType::getId('client');
        $user->password = Hash::make($request->get('password'));
        if (!$user->save()) {
            return response()->json([
                'message' => __('Erro ao criar usuário')
            ]);
        }
        $tokenResult = $user->createToken('authToken')->plainTextToken;
        $user = User::find($user->id);
        Mail::send(new CreateAccountEmail($user));
        return response()->json([
            'user' => $user,
            'token' => $tokenResult,
        ]);
    }

    public function address(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'id' => ['nullable', 'exists:user_addresses,id'],
            'zip_code' => ['required', new ValidCep],
            'state_id' => ['required', 'exists:states,id'],
            'city_id' => ['required', 'exists:cities,id'],
            'street_name' => ['required', 'min:3'],
            'street_number' => ['required', 'numeric'],
            'complement' => ['nullable'],
            'neighborhood' => ['required', 'min:3'],
        ]);
        if ($request->get('id')) {
            $address = UserAddress::find($request->get('id'));
        }
        if (isset($address) &&
            $address->zip_code == $request->get('zip_code') &&
            $address->street_name == $request->get('street_name') &&
            $address->street_number == $request->get('street_number') &&
            $address->complement == $request->get('complement') &&
            $address->state_id == $request->get('state_id') &&
            $address->city_id == $request->get('city_id')
        ) {
            return response()->json([
                'message' => __('O seu endereço foi cadastrado com sucesso.')
            ]);
        }
        UserAddress::whereUserId($user->id)->update(['main' => false]);
        $address = new UserAddress();
        $address->fill($request->all());
        $address->main = true;
        $address->user_id = $user->id;
        if (!$address->save()) {
            return response()->json([
                'message' => __('O endereço não foi cadastrado.')
            ], 400);
        }
        return response()->json([
            'message' => __('O seu endereço foi cadastrado com sucesso.')
        ]);
    }
}
