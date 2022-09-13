<?php

use App\Http\Controllers\AfricanPaymentGatewayController;
use App\Http\Controllers\Payment\FlutterwaveController;
use App\Http\Controllers\Payment\MpesaController;
use App\Http\Controllers\Payment\PayfastController;

Route::controller(AfricanPaymentGatewayController::class)->group(function () {
  Route::get('/african/configuration', 'configuration')->name('african.configuration');
  Route::get('/african/credentials_index', 'credentials_index')->name('african_credentials.index');
});
//Mpesa

Route::prefix('lnmo')->group(function () {
    Route::controller(MpesaController::class)->group(function () {
        Route::post('mpesa_pay', 'payment_complete')->name('mpesa.pay');
        Route::any('pay', 'mpesa_pay');
        Route::any('validate', 'validation');
        Route::any('confirm', 'confirmation');
        Route::any('results', 'results');
        Route::any('register', 'register');
        Route::any('timeout', 'timeout');
        Route::any('reconcile', 'reconcile');
    });
});

//Mpesa End

// RaveController start
Route::get('/rave/callback', [FlutterwaveController::class, 'callback'])->name('flutterwave.callback');

// RaveController end

//Payfast routes <starts>
Route::controller(PayfastController::class)->group(function () {
  Route::any('/payfast/checkout/notify', 'checkout_notify')->name('payfast.checkout.notify');
  Route::any('/payfast/checkout/return', 'checkout_return')->name('payfast.checkout.return');
  Route::any('/payfast/checkout/cancel', 'checkout_cancel')->name('payfast.checkout.cancel');

  Route::any('/payfast/wallet/notify', 'wallet_notify')->name('payfast.wallet.notify');
  Route::any('/payfast/wallet/return', 'wallet_return')->name('payfast.wallet.return');
  Route::any('/payfast/wallet/cancel', 'wallet_cancel')->name('payfast.wallet.cancel');

  Route::any('/payfast/seller_package_payment/notify', 'seller_package_notify')->name('payfast.seller_package_payment.notify');
  Route::any('/payfast/seller_package_payment/return', 'seller_package_payment_return')->name('payfast.seller_package_payment.return');
  Route::any('/payfast/seller_package_payment/cancel', 'seller_package_payment_cancel')->name('payfast.seller_package_payment.cancel');

  Route::any('/payfast/customer_package_payment/notify', 'customer_package_notify')->name('payfast.customer_package_payment.notify');
  Route::any('/payfast/customer_package_payment/return', 'customer_package_return')->name('payfast.customer_package_payment.return');
  Route::any('/payfast/customer_package_payment/cancel', 'customer_package_cancel')->name('payfast.customer_package_payment.cancel');
});
//Payfast routes <ends>
