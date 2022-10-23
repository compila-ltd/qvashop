<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;

class CashOnDeliveryController extends Controller
{
    public function pay()
    {
        return redirect()->route('order_confirmed')->with('success', translate('Your order has been placed successfully'));
    }
}
