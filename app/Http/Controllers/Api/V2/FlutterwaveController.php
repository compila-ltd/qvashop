<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\CombinedOrder;
use App\Models\User;
use Exception;
use Rave as Flutterwave;
use Illuminate\Http\Request;


class FlutterwaveController extends Controller
{

    public function getUrl(Request $request)
    {
        $payment_type = $request->payment_type;
        $combined_order_id = $request->combined_order_id;
        $amount = $request->amount;
        $user_id = $request->user_id;
        if(isset($request->package_id)) {
            $package_id = $request->package_id;
        }
        

        if ($payment_type == 'cart_payment') {
            $combined_order = CombinedOrder::find($combined_order_id);      
            return $this->initialize($payment_type, $combined_order_id, $combined_order->grand_total, $user_id);
        } elseif ($payment_type == 'wallet_payment') {
            return $this->initialize($payment_type, $combined_order_id, $amount, $user_id);
        } elseif ($payment_type == 'seller_package_payment') {
            return $this->initialize($payment_type, $combined_order_id, $amount, $user_id, $package_id);
        }
    }

    public function initialize($payment_type, $combined_order_id, $amount, $user_id, $package_id=0)
    {
        $user = User::find($user_id);
        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $amount,
            'email' => $user->email,
            'tx_ref' => $reference,
            'currency' => env('FLW_PAYMENT_CURRENCY_CODE'),
            'redirect_url' => route('api.flutterwave.callback', 
                [
                    "payment_type" => $payment_type, 
                    "combined_order_id" => $combined_order_id, 
                    "amount" => $amount, 
                    "user_id" => $user_id, 
                    'package_id' => $package_id
                ]
            ),
            'customer' => [
                'email' => $user->email,
                "phone_number" => $user->phone,
                "name" => $user->name
            ],

            "customizations" => [
                "title" => 'Payment',
                "description" => ""
            ]
        ];

        $payment = Flutterwave::initializePayment($data);


        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return response()->json(['result' => false, 'url' => '', 'message' => "Could not find redirect url"]);
        }
        return response()->json(['result' => true, 'url' => $payment['data']['link'], 'message' => "Url generated"]);
    }

  

    public function callback(Request $request)
    {
        $status = $request->status;

        //if payment is successful
        if ($status ==  'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);

            try{
                $payment = $data['data'];

                if($payment['status'] == "successful"){
                    if ($request->payment_type == 'cart_payment') {
                        checkout_done($request->combined_order_id, json_encode($payment));
                    }

                    if ($request->payment_type == 'wallet_payment') {
                        wallet_payment_done($request->user_id, $request->amount, 'Flutterwave', json_encode($payment));
                    }

                    if ($request->payment_type == 'seller_package_payment') {
                        seller_purchase_payment_done($request->user_id, $request->package_id, $request->amount, 'Flutterwave', json_encode($payment));
                    }

                    return response()->json(['result' => true, 'message' => translate("Payment is successful")]);
                
                }else{
                    return response()->json(['result' => false, 'message' => translate("Payment is unsuccessful")]);
                }
            }
            catch(Exception $e){
                return response()->json(['result' => false, 'message' => translate("Unsuccessful")]);
            }
        }
        elseif ($status ==  'cancelled'){
            return response()->json(['result' => false, 'message' => translate("Payment Cancelled")]);
        }
        
    }
}
