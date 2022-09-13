<?php

/*
|--------------------------------------------------------------------------
| OTP Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\OTPVerificationController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SmsTemplateController;

//Verofocation phone
Route::controller(OTPVerificationController::class)->group(function () {
    Route::get('/verification', 'verification')->name('verification');
    Route::post('/verification', 'verify_phone')->name('verification.submit');
    Route::get('/verification/phone/code/resend', 'resend_verificcation_code')->name('verification.phone.resend');
    
    //Forgot password phone
    Route::get('/password/phone/reset', 'show_reset_password_form')->name('password.phone.form');
    Route::post('/password/reset/submit', 'reset_password_with_code')->name('password.update.phone');
});

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::controller(OTPController::class)->group(function () {
        Route::get('/otp-configuration', 'configure_index')->name('otp.configconfiguration');
        Route::get('/otp-credentials-configuration', 'credentials_index')->name('otp_credentials.index');
        Route::post('/otp-configuration/update/activation', 'updateActivationSettings')->name('otp_configurations.update.activation');
        Route::post('/otp-credentials-update', 'update_credentials')->name('update_credentials');
    });
    //Messaging
    Route::controller(SmsController::class)->group(function () {
        Route::get('/sms', 'index')->name('sms.index');
        Route::post('/sms-send', 'send')->name('sms.send');
    });

     Route::resource('sms-templates', SmsTemplateController::class);
});
