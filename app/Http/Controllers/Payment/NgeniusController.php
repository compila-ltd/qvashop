<?php
namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CustomerPackage;
use App\Models\CombinedOrder;
use App\Utility\NgeniusUtility;
use Session;

class NgeniusController extends Controller
{
    public function pay()
    {
        if (Session::get('payment_type') == 'cart_payment') {
            $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
            $amount = round($combined_order->grand_total * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.cart_payment_callback'),"cart_payment",$amount);
        } elseif (Session::get('payment_type') == 'wallet_payment') {
            $amount = round(Session::get('payment_data')['amount'] * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.wallet_payment_callback'),"wallet_payment",$amount);
        } elseif (Session::get('payment_type') == 'customer_package_payment') {
            $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
            $amount = round($customer_package->amount * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.customer_package_payment_callback'),"customer_package_payment",$amount);
        } elseif (Session::get('payment_type') == 'seller_package_payment') {
            $seller_package = \App\Models\SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
            $amount = round($seller_package->amount * 100);
            //will be redirected
            NgeniusUtility::make_payment(route('ngenius.seller_package_payment_callback'),"seller_package_payment",$amount);
        }


        $seller_package_id = Session::get('payment_data')['seller_package_id'];
        $seller_package  = \App\Models\SellerPackage::findOrFail($seller_package_id);

    }

    public function cart_payment_callback()
    {
        if (request()->has('ref')) {
           return NgeniusUtility::check_callback(request()->get('ref'),"cart_payment");
        }
    }
    public function wallet_payment_callback()
    {
        if (request()->has('ref')) {
            return NgeniusUtility::check_callback(request()->get('ref'),"wallet_payment");
        }
    }

    public function customer_package_payment_callback()
    {
        if (request()->has('ref')) {
            return NgeniusUtility::check_callback(request()->get('ref'),"customer_package_payment");
        }
    }

    public function seller_package_payment_callback()
    {
        if (request()->has('ref')) {
            return NgeniusUtility::check_callback(request()->get('ref'),"seller_package_payment");
        }
    }
}
