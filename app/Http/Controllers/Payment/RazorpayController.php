<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\SellerPackageController;
use Razorpay\Api\Api;
use Session;


class RazorpayController extends Controller
{
    public function pay()
    {
        if(Session::has('payment_type')){
            if(Session::get('payment_type') == 'cart_payment'){
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                return view('frontend.razor_wallet.order_payment_Razorpay', compact('combined_order'));
            }
            elseif (Session::get('payment_type') == 'wallet_payment') {
                return view('frontend.razor_wallet.wallet_payment_Razorpay');
            }
            elseif (Session::get('payment_type') == 'customer_package_payment') {
                return view('frontend.razor_wallet.customer_package_payment_Razorpay');
            }
            elseif (Session::get('payment_type') == 'seller_package_payment') {
                return view('frontend.razor_wallet.seller_package_payment_Razorpay');
            }
        }
    }

    public function payment(Request $request)
    {
        //Input items of form
        $input = $request->all();
        //get API Configuration
        $api = new Api(env('RAZOR_KEY'), env('RAZOR_SECRET'));

        //Fetch payment information by razorpay_payment_id
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if(count($input)  && !empty($input['razorpay_payment_id'])) {
            $payment_detalis = null;
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount'=>$payment['amount']));
                $payment_detalis = json_encode(array('id' => $response['id'],'method' => $response['method'],'amount' => $response['amount'],'currency' => $response['currency']));
            } catch (\Exception $e) {
                return  $e->getMessage();
                \Session::put('error',$e->getMessage());
                return redirect()->back();
            }

            // Do something here for store payment details in database...
            if(Session::has('payment_type')){
                if(Session::get('payment_type') == 'cart_payment'){
                    return (new CheckoutController)->checkout_done(Session::get('combined_order_id'), $payment_detalis);
                }
                elseif (Session::get('payment_type') == 'wallet_payment') {
                    return (new WalletController)->wallet_payment_done(Session::get('payment_data'), $payment_detalis);
                }
                elseif (Session::get('payment_type') == 'customer_package_payment') {
                    return (new CustomerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment_detalis);
                }
                elseif (Session::get('payment_type') == 'seller_package_payment') {
                    return (new SellerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment_detalis);
                }
            }
        }
    }
}
