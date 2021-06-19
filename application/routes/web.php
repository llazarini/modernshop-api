<?php

use App\Models\Company;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/image', [\App\Http\Controllers\Site\ImageController::class, 'image']);

Route::get('/{company}/sitemap', function($id) {
    $company = Company::find($id);
    $sitemap = App::make('sitemap');
    $sitemap->add("https://{$company->domain}/", now(), '1.0', 'daily');
    $products = \App\Models\Product::whereCompanyId($company->id)
        ->get();
    foreach ($products as $product) {
        $slug = $product->toArray()['slug'];
        $sitemap->add("https://{$company->domain}/product/view/{$product->id}/{$slug}", $product->updated_at, '0.9', 'weekly');
    }
    $sitemap->add("https://{$company->domain}/privacy", now(), '0.8', 'monthly');
    return $sitemap->render('xml');
});
