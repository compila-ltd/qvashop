<?php

/*
|--------------------------------------------------------------------------
| Refund System Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Admin Panel

use App\Http\Controllers\RefundRequestController;

Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::controller(RefundRequestController::class)->group(function () {
        Route::get('/refund-request-all', 'admin_index')->name('refund_requests_all');
        Route::get('/refund-request-config', 'refund_config')->name('refund_time_config');
        Route::get('/paid-refund', 'paid_index')->name('paid_refund');
        Route::get('/rejected-refund', 'rejected_index')->name('rejected_refund');
        Route::post('/refund-request-pay', 'refund_pay')->name('refund_request_money_by_admin');
        Route::post('/refund-request-time-store', 'refund_time_update')->name('refund_request_time_config');
        Route::post('/refund-request-sticker-store', 'refund_sticker_update')->name('refund_sticker_config');
    });
});

//FrontEnd User panel
Route::group(['middleware' => ['user', 'verified']], function(){
    Route::controller(RefundRequestController::class)->group(function () {
        Route::post('refund-request-send/{id}', 'request_store')->name('refund_request_send');
        Route::get('refund-request', 'vendor_index')->name('vendor_refund_request');
        Route::get('sent-refund-request', 'customer_index')->name('customer_refund_request');
        Route::post('refund-reuest-vendor-approval', 'request_approval_vendor')->name('vendor_refund_approval');
        Route::get('refund-request/{id}', 'refund_request_send_page')->name('refund_request_send_page');
    });
});

Route::group(['middleware' => ['auth']], function(){
    Route::controller(RefundRequestController::class)->group(function () {
        Route::Post('/reject-refund-request','reject_refund_request')->name('reject_refund_request');

        Route::get('refund-request-reason/{id}', 'reason_view')->name('reason_show');
        Route::get('refund-request-reject-reason/{id}', 'reject_reason_view')->name('reject_reason_show');
    });
});
