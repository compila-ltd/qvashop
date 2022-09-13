<?php

/*
|--------------------------------------------------------------------------
| B2B Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\WholesaleProductController;

//Admin

Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){   
    Route::controller(WholesaleProductController::class)->group(function () {
        Route::get('/wholesale/all-products', 'all_wholesale_products')->name('wholesale_products.all');
        Route::get('/wholesale/inhouse-products', 'in_house_wholesale_products')->name('wholesale_products.in_house');
        Route::get('/wholesale/seller-products', 'seller_wholesale_products')->name('wholesale_products.seller');

        Route::get('/wholesale-product/create', 'product_create_admin')->name('wholesale_product_create.admin');
        Route::post('/wholesale-product/store', 'product_store_admin')->name('wholesale_product_store.admin');
        Route::get('/wholesale-product/{id}/edit', 'product_edit_admin')->name('wholesale_product_edit.admin');
        Route::post('/wholesale-product/update/{id}', 'product_update_admin')->name('wholesale_product_update.admin');
        Route::get('/wholesale-product/destroy/{id}', 'product_destroy_admin')->name('wholesale_product_destroy.admin');
    });
});

Route::group(['prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user']], function() {
    Route::controller(WholesaleProductController::class)->group(function () {
        Route::get('/wholesale-products', 'wholesale_products_list_seller')->name('seller.wholesale_products_list');

        Route::get('/wholesale-product/create', 'product_create_seller')->name('wholesale_product_create.seller');
        Route::post('/wholesale-product/store', 'product_store_seller')->name('wholesale_product_store.seller');
        Route::get('/wholesale-products/{id}/edit', 'product_edit_seller')->name('wholesale_product_edit.seller');
        Route::post('/wholesale-product/update/{id}', 'product_update_seller')->name('wholesale_product_update.seller');
        Route::get('/wholesale-product/destroy/{id}', 'product_destroy_seller')->name('wholesale_product_destroy.seller');
    });
});