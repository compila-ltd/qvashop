<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\User;
use App\Models\CombinedOrder;
use Illuminate\Http\Request;
use PaytmWallet;

class PaytmController extends Controller
{

    public function pay(Request $request)
    {
        $payment_type = $request->payment_type;
        $combined_order_id = $request->combined_order_id;
        $amount = $request->amount;
        $user_id = $request->user_id;
        $package_id = 0;

        if(isset($request->package_id)){
            $package_id = $request->package_id;
        }

        
        $user = User::find($request->user_id);

        if ($payment_type == 'cart_payment') {
            $combined_order = CombinedOrder::find($combined_order_id);
            $amount = floatval($combined_order->grand_total) ;

            $payment = PaytmWallet::with('receive');
            $payment->prepare([
                'order' => rand(10000, 99999),
                'user' => $user->id,
                'mobile_number' => $user->phone,
                'email' => $user->email,
                'amount' => $amount,
                'callback_url' => route('api.paytm.callback', 
                [
                    "payment_type" => $payment_type, 
                    "combined_order_id" => $combined_order_id, 
                    "amount" => $amount, 
                    "user_id" => $user_id
                ])
                
            ]);
   
            return $payment->receive();
        } elseif ($payment_type == 'wallet_payment') {
            $amount = $amount;
            $payment = PaytmWallet::with('receive');
            $payment->prepare([
                'order' => rand(10000, 99999),
                'user' => $user->id,
                'mobile_number' => $user->phone,
                'email' => $user->email,
                'amount' => $amount,
                'callback_url' => route('api.paytm.callback', 
                [
                    "payment_type" => $payment_type, 
                    "combined_order_id" => $combined_order_id, 
                    "amount" => $amount, 
                    "user_id" => $user_id
                ])
            ]);
            return $payment->receive();
        } elseif ($payment_type == 'seller_package_payment') {
            $amount = $amount;
            $payment = PaytmWallet::with('receive');
            $payment->prepare([
                'order' => rand(10000, 99999),
                'user' => $user->id,
                'mobile_number' => $user->phone,
                'email' => $user->email,
                'amount' => $amount,
                'callback_url' => route('api.paytm.callback', 
                [
                    "payment_type" => $payment_type, 
                    "combined_order_id" => $combined_order_id, 
                    "amount" => $amount, 
                    "user_id" => $user_id,
                    "package_id" => $package_id,
                ])
            ]);
            return $payment->receive();
        }
    }

    public function callback(Request $request)
    {
        $transaction = PaytmWallet::with('receive');

        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm

        if ($transaction->isSuccessful()) {

            if ($request->payment_type == 'cart_payment') {
                checkout_done($request->combined_order_id, json_encode($response));
            }

            if ($request->payment_type == 'wallet_payment') {
                wallet_payment_done($request->user_id, $request->amount, 'Flutterwave', json_encode($response));
            }
            if ($request->payment_type == 'seller_package_payment') {
                seller_purchase_payment_done($request->user_id, $request->package_id, $request->amount, 'Flutterwave', json_encode($response));
            }

            return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
        }
    }
}
