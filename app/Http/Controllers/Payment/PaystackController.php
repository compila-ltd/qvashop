<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SellerPackageController;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use Auth;
use Session;
use Paystack;

class PaystackController extends Controller
{
    public function pay(Request $request)
    {
        if (Session::get('payment_type') == 'cart_payment') {
            $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
            $user = Auth::user();
            $request->email = $user->email;
            $request->amount = round($combined_order->grand_total * 100);
            $request->currency = env('PAYSTACK_CURRENCY_CODE', 'NGN');
            $request->reference = Paystack::genTranxRef();
            return Paystack::getAuthorizationUrl()->redirectNow();
        } elseif (Session::get('payment_type') == 'wallet_payment') {
            $user = Auth::user();
            $request->email = $user->email;
            $request->amount = round(Session::get('payment_data')['amount'] * 100);
            $request->currency = env('PAYSTACK_CURRENCY_CODE', 'NGN');
            $request->reference = Paystack::genTranxRef();
            return Paystack::getAuthorizationUrl()->redirectNow();
        } elseif (Session::get('payment_type') == 'customer_package_payment') {
            $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
            $user = Auth::user();
            $request->email = $user->email;
            $request->amount = round($customer_package->amount * 100);
            $request->currency = env('PAYSTACK_CURRENCY_CODE', 'NGN');
            $request->reference = Paystack::genTranxRef();
            return Paystack::getAuthorizationUrl()->redirectNow();
        } elseif (Session::get('payment_type') == 'seller_package_payment') {
            $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
            $user = Auth::user();
            $request->email = $user->email;
            $request->amount = round($seller_package->amount * 100);
            $request->currency = env('PAYSTACK_CURRENCY_CODE', 'NGN');
            $request->reference = Paystack::genTranxRef();
            return Paystack::getAuthorizationUrl()->redirectNow();
        }
    }


    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want
        if (Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment') {
                $payment = Paystack::getPaymentData();
                $payment_detalis = json_encode($payment);
                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    return (new CheckoutController)->checkout_done(Session::get('combined_order_id'), $payment_detalis);
                }
                Session::forget('combined_order_id');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');
            } elseif (Session::get('payment_type') == 'wallet_payment') {
                $payment = Paystack::getPaymentData();
                $payment_detalis = json_encode($payment);
                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    return (new WalletController)->wallet_payment_done(Session::get('payment_data'), $payment_detalis);
                }
                Session::forget('payment_data');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');
            } elseif (Session::get('payment_type') == 'customer_package_payment') {
                $payment = Paystack::getPaymentData();
                $payment_detalis = json_encode($payment);
                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    return (new CustomerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment);
                }
                Session::forget('payment_data');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');
            } elseif (Session::get('payment_type') == 'seller_package_payment') {
                $payment = Paystack::getPaymentData();
                $payment_detalis = json_encode($payment);
                if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                    return (new SellerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment);
                }
                Session::forget('payment_data');
                flash(translate('Payment cancelled'))->success();
                return redirect()->route('home');
            }
        }

        // for mobile app
        if (!Session::has('payment_type')) {
            $payment = Paystack::getPaymentData();
            $payment_details = json_encode($payment);
            if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                return response()->json(['result' => true, 'message' => "Payment is successful", 'payment_details' => $payment_details]);
            } else {
                return response()->json(['result' => false, 'message' => "Payment unsuccessful", 'payment_details' => $payment_details]);
            }

        }
    }
}
