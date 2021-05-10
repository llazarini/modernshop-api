<?php

use App\Http\Controllers\Auth\AddressController;
use App\Http\Controllers\Auth\BannerCategoriesController;
use App\Http\Controllers\Auth\CategoriesController;
use App\Http\Controllers\Auth\FileController;
use App\Http\Controllers\Auth\OptionsController;
use App\Http\Controllers\Auth\ProductsController;
use App\Http\Controllers\Guest\CheckoutController;
use App\Http\Controllers\Guest\OrdersController;
use App\Http\Controllers\Guest\ProductsController as GuestProductsController;
use App\Http\Controllers\Auth\StatesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::post('auth/login', ['as' => 'login', 'uses' => 'UsersController@login']);

Route::group(['prefix' => '/states'], function () {
    Route::get('/get-cities', [StatesController::class, 'getCities']);
});
Route::group(['prefix' => 'guest'], function() {
    Route::group(['prefix' => '/products'], function () {
        Route::get('/index', [GuestProductsController::class, 'index']);
        Route::get('/category', [GuestProductsController::class, 'category']);
        Route::get('/show', [GuestProductsController::class, 'show']);
        Route::get('/dataprovider', [GuestProductsController::class, 'dataprovider']);
    });

    Route::group(['prefix' => '/banners'], function () {
        Route::get('/category', [\App\Http\Controllers\Guest\BannersController::class, 'category']);
    });

    Route::group(['prefix' => '/checkout'], function () {
        Route::get('/basic', [CheckoutController::class, 'basic']);
        Route::post('/shipment', [CheckoutController::class, 'shipment']);
    });
    Route::post('/users/create', [UsersController::class, 'create']);
    Route::post('/forgot', [UsersController::class, 'forgot']);
    Route::post('/remember_token', [UsersController::class, 'remember_token']);
    Route::post('/password', [UsersController::class, 'password']);
    Route::group(['middleware' => 'auth:sanctum'], function() {
        Route::get('auth/me', [UsersController::class, 'me']);
        Route::group(['prefix' => '/users'], function () {
            Route::post('/address', [UsersController::class, 'address']);
        });
        Route::group(['prefix' => '/checkout'], function () {
            Route::post('/payment', [CheckoutController::class, 'payment']);
        });
        Route::group(['prefix' => '/orders'], function () {
            Route::get('/show', [OrdersController::class, 'show']);
            Route::get('/index', [OrdersController::class, 'index']);
        });
    });
});

Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'auth'], function() {
    Route::group(['prefix' => '/address'], function () {
        Route::get('/zip_code', [AddressController::class, 'postalCode']);
    });

    Route::group(['prefix' => '/states'], function () {
        Route::get('/get-all', [StatesController::class, 'index']);
        Route::get('/get-cities', [StatesController::class, 'getCities']);
    });

    Route::group(['prefix' => '/users'], function () {
        Route::get('/get/{id}', [UsersController::class, 'get']);
        Route::get('/get-all', [UsersController::class, 'index']);
        Route::post('/store', [UsersController::class, 'store']);
        Route::put('/update/{id}', [UsersController::class, 'update']);
        Route::delete('/delete/{id}', [UsersController::class, 'delete']);
    });

    Route::group(['prefix' => '/banners'], function () {
        Route::get('/get/{id}', [BannerCategoriesController::class, 'get']);
        Route::get('/get-all', [BannerCategoriesController::class, 'index']);
        Route::post('/store', [BannerCategoriesController::class, 'store']);
        Route::put('/update/{id}', [BannerCategoriesController::class, 'update']);
        Route::delete('/delete/{id}', [BannerCategoriesController::class, 'delete']);
    });

    Route::group(['prefix' => '/categories'], function () {
        Route::get('/get/{id}', [CategoriesController::class, 'get']);
        Route::get('/get-all', [CategoriesController::class, 'index']);
        Route::get('/dataprovider', [CategoriesController::class, 'dataprovider']);
        Route::post('/store', [CategoriesController::class, 'store']);
        Route::put('/update/{id}', [CategoriesController::class, 'update']);
        Route::delete('/delete/{id}', [CategoriesController::class, 'delete']);
    });

    Route::group(['prefix' => '/products'], function () {
        Route::get('/get/{id}', [ProductsController::class, 'get']);
        Route::get('/get-all', [ProductsController::class, 'index']);
        Route::get('/dataprovider', [ProductsController::class, 'dataprovider']);
        Route::post('/store', [ProductsController::class, 'store']);
        Route::put('/update/{id}', [ProductsController::class, 'update']);
        Route::delete('/delete/{id}', [ProductsController::class, 'delete']);
    });

    Route::group(['prefix' => '/options'], function () {
        Route::get('/get/{id}', [OptionsController::class, 'get']);
        Route::get('/get-all', [OptionsController::class, 'index']);
        Route::post('/store', [OptionsController::class, 'store']);
        Route::put('/update/{id}', [OptionsController::class, 'update']);
        Route::delete('/delete/{id}', [OptionsController::class, 'delete']);
    });

    Route::group(['prefix' => '/files'], function () {
        Route::post('/store', [FileController::class, 'store']);
        Route::delete('/delete/{id}', [FileController::class, 'delete']);
        Route::get('/images', [FileController::class, 'images']);
    });
});
