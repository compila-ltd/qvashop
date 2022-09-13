<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;

class CashOnDeliveryController extends Controller
{
    public function pay(){
        flash(translate("Your order has been placed successfully"))->success();
        return redirect()->route('order_confirmed');
    }
}
