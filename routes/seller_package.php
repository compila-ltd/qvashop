<?php

/*
|--------------------------------------------------------------------------
| Affiliate Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\SellerPackageController;

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::resource('seller_packages', SellerPackageController::class);
    Route::controller(SellerPackageController::class)->group(function () {
        Route::get('/seller_packages/edit/{id}', 'edit')->name('seller_packages.edit');
        Route::get('/seller_packages/destroy/{id}', 'destroy')->name('seller_packages.destroy');
    });
});

//FrontEnd
Route::group(['middleware' => ['seller']], function(){
    Route::controller(SellerPackageController::class)->group(function () {
        Route::get('/seller/seller-packages', 'seller_packages_list')->name('seller.seller_packages_list');
        Route::get('/seller/packages-payment-list', 'packages_payment_list')->name('seller.packages_payment_list');
        Route::post('/seller_packages/purchase', 'purchase_package')->name('seller_packages.purchase');
    });
});

Route::get('/seller_packages/check_for_invalid', [SellerPackageController::class, 'unpublish_products'])->name('seller_packages.unpublish_products');
