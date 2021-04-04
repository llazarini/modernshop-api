<?php

namespace App\Http\Controllers;

use App\Models\AccountingPlan;
use App\Models\City;
use App\Models\Client;
use App\Models\ClientAddress;
use App\Models\ClientContact;
use App\Models\CostCenter;
use App\Models\Activity;
use App\Models\SecondaryActivity;
use App\Models\State;
use App\Models\TaxRegime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function postalCode(Request $request) {
        $request->validate([
           'postal_code' => ['required', 'digits:8', 'numeric']
        ]);

        $response = json_decode(file_get_contents('https://viacep.com.br/ws/'.$request->get('postal_code').'/json/'));

        $state = State::whereCode($response->uf)
            ->first();
        $city = City::whereIso($response->ibge)
            ->first();
        $object['state_id'] = $state->id;
        $object['city_id'] = $city->id;
        $object['address'] = $response->logradouro;
        $object['neighborhood'] = $response->bairro;

        return response()->json($object);
    }
}
