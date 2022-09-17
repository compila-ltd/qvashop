<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QvaPayController extends Controller
{
    private $base_url = "https://qvapay.com/api/v1/create_invoice";
    private $app_key;
    private $app_secret;

    public function __construct()
    {
        // Initialize vars
        
    }

    public function pay()
    {
        // Get the best payment way
        // Create a QvaPay invoice
        // Redirecto to QvaPay paymenta page


    }

    // WebHook from QvaPay
    // /qvapay/payment/pay-success
    public function success(Request $request)
    {

    }
}
