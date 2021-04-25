<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\Request;

class StatesController extends Controller
{
    public function getCities(Request $request)
    {
        $data = City::whereStateId($request->get('state_id'))
            ->get();
        if(!$data) {
            return response()->json([
                'message' => __("Mensagem"),
            ], 400);
        }
        return response()->json($data, 200);
    }

    public function index(Request $request)
    {
        $states = State::get();
        return response()->json($states);
    }
}
