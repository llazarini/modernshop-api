<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerCategory;
use App\Models\File;
use Illuminate\Http\Request;

class BannerCategoriesController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $data = BannerCategory::paginate(10);
        return response()->json($data, 200);
    }

    public function get(Request $request, $id)
    {
        $user = $request->user();
        $data = BannerCategory::whereCompanyId($user->company_id)
            ->with(['banners'])
            ->find($id);
        if(!$data) {
            return response()->json([
                'message' => __("Erro ao tentar retornar registro."),
            ], 400);
        }
        return response()->json($data, 200);
    }

    public function dataprovider(Request $request)
    {
        return response()->json([]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => ['required'],
            'slug' => ['required', sprintf('unique:banner_categories,id,%s', $id)],
            'banners' => ['required', 'array'],
            'banners.*.name' => ['required', 'between:3,250'],
            'banners.*.content' => ['nullable', 'max:10000'],
            'banners.*.file_id' => ['required', 'exists:files,id'],
        ]);
        $user = $request->user();
        $bannerCategory = BannerCategory::whereCompanyId($user->company_id)->find($id);
        $bannerCategory->fill($request->all());
        if(!$bannerCategory->save()) {
            return response()->json([
                'message' => __("Ocorreu um erro ao tentar salvar o produto."),
            ], 400);
        }
        $this->saveUpdateBanners($request, $bannerCategory);
        return response()->json([
            'data' => $bannerCategory,
            'message' => __('Produto atualizado com sucesso.'),
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $request->validate([
            'name' => ['required'],
            'slug' => ['required', 'unique:banner_categories'],
            'banners' => ['required', 'array'],
            'banners.*.name' => ['required', 'between:3,250'],
            'banners.*.content' => ['nullable', 'max:10000'],
            'banners.*.file_id' => ['required', 'exists:files,id'],
        ]);
        $bannerCategory = new BannerCategory();
        $bannerCategory->company_id = $user->company_id;
        $bannerCategory->fill($request->all());
        if(!$bannerCategory->save()) {
            return response()->json([
                'message' => __("Erro ao tentar cadastrar."),
            ], 400);
        }
        $this->saveUpdateBanners($request, $bannerCategory);
        return response()->json([
            'message' => __('Banner criado com sucesso.'),
        ]);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user();
        $data = BannerCategory::whereCompanyId($user->company_id)
            ->find($id);
        if(!$data->delete()) {
            return response()->json([
                'message' => __("Erro ao tentar remover."),
            ], 400);
        }
        return response()->json([
            'message' => __('Banner removido com sucesso.'),
        ]);
    }

    private function saveUpdateBanners(Request $request, BannerCategory $bannerCategory)
    {
        $user = request()->user();
        $bannerIds = collect();
        foreach($request->get('banners') as $item) {
            $banner = null;
            if (isset($item['id'])) {
                $banner = Banner::find($item['id']);
            }
            if (!isset($banner)) {
                $banner = new Banner();
            }
            $banner->company_id = $user->company_id;
            $banner->banner_category_id = $bannerCategory->id;
            $banner->fill($item);
            if (!$banner->save()) {
                return response()->json([
                    'message' => __("Erro ao criar banner")
                ], 400);
            }
            $file = File::find($item['file_id']);
            if (!$file) {
                continue;
            }
            $file->type_id = $banner->id;
            $file->save();
            $bannerIds->push($banner->id);
        }
        Banner::whereBannerCategoryId($bannerCategory->id)
            ->whereNotIn('id', $bannerIds->toArray())
            ->delete();
    }
}
