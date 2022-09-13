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

//Admin

use App\Http\Controllers\AffiliateController;

Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::controller(AffiliateController::class)->group(function () {
        Route::get('/affiliate', 'index')->name('affiliate.index');
        Route::post('/affiliate/affiliate_option_store', 'affiliate_option_store')->name('affiliate.store');

        Route::get('/affiliate/configs', 'configs')->name('affiliate.configs');
        Route::post('/affiliate/configs/store', 'config_store')->name('affiliate.configs.store');

        Route::get('/affiliate/users', 'users')->name('affiliate.users');
        Route::get('/affiliate/verification/{id}', 'show_verification_request')->name('affiliate_users.show_verification_request');

        Route::get('/affiliate/approve/{id}', 'approve_user')->name('affiliate_user.approve');
        Route::get('/affiliate/reject/{id}', 'reject_user')->name('affiliate_user.reject');

        Route::post('/affiliate/approved', 'updateApproved')->name('affiliate_user.approved');

        Route::post('/affiliate/payment_modal', 'payment_modal')->name('affiliate_user.payment_modal');
        Route::post('/affiliate/pay/store', 'payment_store')->name('affiliate_user.payment_store');

        Route::get('/affiliate/payments/show/{id}', 'payment_history')->name('affiliate_user.payment_history');
        Route::get('/refferal/users', 'refferal_users')->name('refferals.users');

        // Affiliate Withdraw Request
        Route::get('/affiliate/withdraw_requests', 'affiliate_withdraw_requests')->name('affiliate.withdraw_requests');
        Route::post('/affiliate/affiliate_withdraw_modal', 'affiliate_withdraw_modal')->name('affiliate_withdraw_modal');
        Route::post('/affiliate/withdraw_request/payment_store', 'withdraw_request_payment_store')->name('withdraw_request.payment_store');
        Route::get('/affiliate/withdraw_request/reject/{id}', 'reject_withdraw_request')->name('affiliate.withdraw_request.reject');

        Route::get('/affiliate/logs', 'affiliate_logs_admin')->name('affiliate.logs.admin');

    });
});

//FrontEnd
Route::controller(AffiliateController::class)->group(function () {
    Route::get('/affiliate', 'apply_for_affiliate')->name('affiliate.apply');
    Route::post('/affiliate/store', 'store_affiliate_user')->name('affiliate.store_affiliate_user');
});

Route::group(['middleware' => ['auth']], function(){
    Route::controller(AffiliateController::class)->group(function () {
        Route::get('/affiliate/user', 'user_index')->name('affiliate.user.index');
        Route::get('/affiliate/user/payment_history', 'user_payment_history')->name('affiliate.user.payment_history');
        Route::get('/affiliate/user/withdraw_request_history', 'user_withdraw_request_history')->name('affiliate.user.withdraw_request_history');

        Route::get('/affiliate/payment/settings', 'payment_settings')->name('affiliate.payment_settings');
        Route::post('/affiliate/payment/settings/store', 'payment_settings_store')->name('affiliate.payment_settings_store');

        // Affiliate Withdraw Request
        Route::post('/affiliate/withdraw_request/store', 'withdraw_request_store')->name('affiliate.withdraw_request.store');
    });
});
