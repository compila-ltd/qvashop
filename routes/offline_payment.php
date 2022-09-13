<?php

use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CustomerPackagePaymentController;
use App\Http\Controllers\ManualPaymentMethodController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\SellerPackagePaymentController;

/*
|--------------------------------------------------------------------------
| Offline Payment Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::resource('manual_payment_methods', ManualPaymentMethodController::class);
    Route::get('/manual_payment_methods/destroy/{id}', [ManualPaymentMethodController::class, 'destroy'])->name('manual_payment_methods.destroy');
    Route::get('/offline-wallet-recharge-requests', [WalletController::class, 'offline_recharge_request'])->name('offline_wallet_recharge_request.index');
    Route::post('/offline-wallet-recharge/approved', [WalletController::class, 'updateApproved'])->name('offline_recharge_request.approved');

    // Seller Package purchase request
    Route::get('/offline-seller-package-payment-requests', [SellerPackagePaymentController::class, 'offline_payment_request'])->name('offline_seller_package_payment_request.index');
    Route::post('/offline-seller-package-payment/approved', [SellerPackagePaymentController::class, 'offline_payment_approval'])->name('offline_seller_package_payment.approved');

    // customer package purchase request
    Route::get('/offline-customer-package-payment-requests', [CustomerPackagePaymentController::class, 'offline_payment_request'])->name('offline_customer_package_payment_request.index');
    Route::post('/offline-customer-package-payment/approved', [CustomerPackagePaymentController::class, 'offline_payment_approval'])->name('offline_customer_package_payment.approved');

});

//FrontEnd
Route::post('/purchase_history/make_payment', [ManualPaymentMethodController::class, 'show_payment_modal'])->name('checkout.make_payment');
Route::post('/purchase_history/make_payment/submit', [ManualPaymentMethodController::class, 'submit_offline_payment'])->name('purchase_history.make_payment');
Route::post('/offline-wallet-recharge-modal', [ManualPaymentMethodController::class, 'offline_recharge_modal'])->name('offline_wallet_recharge_modal');

Route::group(['middleware' => ['user', 'verified']], function(){
	Route::post('/offline-wallet-recharge', [WalletController::class, 'offline_recharge'])->name('wallet_recharge.make_payment');

});

// customer package purchase
Route::post('/offline-customer-package-purchase-modal', [ManualPaymentMethodController::class, 'offline_customer_package_purchase_modal'])->name('offline_customer_package_purchase_modal');
Route::post('/offline-customer-package-paymnet', [CustomerPackageController::class, 'purchase_package_offline'])->name('customer_package.make_offline_payment');


Route::group(['prefix' => 'seller', 'middleware' => ['seller', 'verified', 'user'], 'as' => 'seller.'], function () {
    // Seller Package purchase
    Route::post('/offline-seller-package-purchase-modal', [ManualPaymentMethodController::class, 'offline_seller_package_purchase_modal'])->name('offline_seller_package_purchase_modal');
    Route::post('/offline-seller-package-paymnet',[SellerPackageController::class, 'purchase_package_offline'])->name('make_offline_payment');
});

