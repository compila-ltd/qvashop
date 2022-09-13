<?php

namespace App\Http\Controllers\Payment;

use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SebaCarrasco93\LaravelPayku\Facades\LaravelPayku;
use SebaCarrasco93\LaravelPayku\Models\PaykuTransaction;
use Session;
use Auth;

class PaykuController
{
    public function pay(Request $request)
    {   
        if($request->session()->has('payment_type')){
            if($request->session()->get('payment_type') == 'cart_payment'){
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $data = [
                    'order' => rand(0000000,11111111).date('is'),
                    'subject' => 'Cart Payment',
                    'amount' => $combined_order->grand_total,
                    'email' => Auth::user()->email
                ];
            }
            elseif ($request->session()->get('payment_type') == 'wallet_payment') {
                $data = [
                    'order' => rand(0000000,11111111).date('is'),
                    'subject' => 'Wallet Payment',
                    'amount' => $request->session()->get('payment_data')['amount'],
                    'email' => Auth::user()->email
                ];
            }
            elseif ($request->session()->get('payment_type') == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
                $data = [
                    'order' => rand(0000000,11111111).date('is'),
                    'subject' => 'CustomerPackage Payment',
                    'amount' => $customer_package->amount,
                    'email' => Auth::user()->email
                ];
            }
            elseif ($request->session()->get('payment_type') == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
                $data = [
                    'order' => rand(0000000,11111111).date('is'),
                    'subject' => 'SellerPackage Payment',
                    'amount' => $seller_package->amount,
                    'email' => Auth::user()->email
                ];
            }
        }

        return LaravelPayku::create($data['order'], $data['subject'], $data['amount'], $data['email']);
    }

    public function return($order)
    {
        $detail = LaravelPayku::return($order);

        return $detail;
    }

    public function notify($order)
    {
        $result = LaravelPayku::notify($order);
        $routeName = config('laravel-payku.route_finish_name');

        $routeExists = Route::has($routeName);
        
        if ($routeExists) {
            return redirect()->route($routeName, $result);
        }

        return view('payku::notify.missing-route', compact('result', 'routeName'));
    }

    public function callback($id){
        $paykuTransaction = PaykuTransaction::find($id);
        
        if($paykuTransaction->status == 'success'){
            $payment_type = Session::get('payment_type');

            if ($payment_type == 'cart_payment') {
                return (new CheckoutController)->checkout_done(session()->get('combined_order_id'), $paykuTransaction->toJson());
            }
            if ($payment_type == 'wallet_payment') {
                return (new WalletController)->wallet_payment_done(session()->get('payment_data'), $paykuTransaction->toJson());
            }
            if ($payment_type == 'customer_package_payment') {
                return (new CustomerPackageController)->purchase_payment_done(session()->get('payment_data'), $paykuTransaction->toJson());
            }
            if($payment_type == 'seller_package_payment') {
                return (new SellerPackageController)->purchase_payment_done(session()->get('payment_data'), $paykuTransaction->toJson());
            }
        }
        else{
            flash(translate('Payment failed'))->error();
    	    return redirect()->route('home');
        }
    }
}
