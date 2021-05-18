<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function dataprovider(Request $request)
    {
        $categories = Category::whereHas('products')
            ->get();
        return response()->json(compact('categories'));
    }

    public function index(Request $request)
    {
        $products = Product::with(['files'])
            ->paginate();
        return response()->json($products);
    }

    public function category(Request $request)
    {
        $request->validate([
            'category' => ['required', 'exists:categories,slug']
        ]);
        $category = Category::whereSlug($request->get('category'))
            ->first();
        $data = Product::with(['files'])
            ->select('products.*')
            ->join('product_category', 'product_category.product_id', 'products.id')
            ->groupBy('products.id')
            ->where('product_category.category_id', $category->id)
            ->paginate(4);
        return response()->json($data);
    }

    public function show(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:products,id']
        ]);
        $products = Product::with(['files', 'options'])
            ->find($request->get('id'));
        return response()->json($products);
    }
}