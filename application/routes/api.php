<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\BannerCategoriesController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\StatesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

Route::post('login', ['as' => 'login', 'uses' => 'UsersController@login']);

Route::group(['middleware' => 'auth:sanctum'], function() {

    Route::group(['prefix' => '/address'], function () {
        Route::get('/postal_code', [AddressController::class]);
    });

    Route::group(['prefix' => '/states'], function () {
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

    Route::group(['prefix' => '/files'], function () {
        Route::post('/store', [FileController::class, 'store']);
        Route::delete('/delete/{id}', [FileController::class, 'delete']);
        Route::get('/images', [FileController::class, 'images']);
    });
});
