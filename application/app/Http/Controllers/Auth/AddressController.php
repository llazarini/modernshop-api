<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function postalCode(Request $request)
    {
        $request->validate([
           'zip_code' => ['required', 'digits:8', 'numeric']
        ]);
        $response = json_decode(file_get_contents('https://viacep.com.br/ws/'.$request->get('zip_code').'/json/'));
        if (!isset($response->uf) || !isset($response->ibge)) {
            return response()->json([
                'message' => __("Não foi possível encontrar o endereço para este CEP.")
            ], 400);
        }
        $state = State::whereCode($response->uf)
            ->first();
        $city = City::whereIso($response->ibge)
            ->first();
        $object['state_id'] = $state->id;
        $object['city_id'] = $city->id;
        $object['street_name'] = $response->logradouro;
        $object['neighborhood'] = $response->bairro;
        return response()->json($object);
    }
}
