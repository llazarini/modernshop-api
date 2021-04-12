<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;

class StatesController extends Controller
{
    /**
     * Get all
     */
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
}
