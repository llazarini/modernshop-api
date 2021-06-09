<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerCategory;
use Illuminate\Http\Request;

class BannersController extends Controller
{
    public function category(Request $request)
    {
        $request->validate([
            'category' => ['required', 'exists:banner_categories,slug']
        ]);
        $category = BannerCategory::whereSlug($request->get('category'))
            ->first();
        $banners = Banner::whereBannerCategoryId($category->id)
            ->get();
        return response()->json($banners);
    }
}
