<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::whereCompanyId($request->get('company_id'))
            ->with('file')
            ->whereHas('file')
            ->orderBy('id', 'DESC')
            ->get();
        return response()->json($categories);
    }
}
