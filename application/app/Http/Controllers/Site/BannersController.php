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
        $category = BannerCategory::whereCompanyId($request->get('company_id'))
            ->whereSlug($request->get('category'))
            ->first();
        $banners = Banner::whereCompanyId($request->get('company_id'))
            ->whereBannerCategoryId($category->id)
            ->get();
        return response()->json($banners);
    }
}
