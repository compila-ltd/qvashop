<?php


namespace App\Http\Controllers\Api\V2;


use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\WalletController;
use App\Models\CombinedOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class RazorpayController
{

    public function payWithRazorpay(Request $request)
    {
        $payment_type = $request->payment_type;
        $combined_order_id = $request->combined_order_id;
        $amount = $request->amount;
        $user_id = $request->user_id;
        $user = User::find($user_id);

        $package_id = 0;
        if(isset($request->package_id)){
            $package_id = $request->package_id;
        }

        if ($payment_type == 'cart_payment') {
            $combined_order = CombinedOrder::find($combined_order_id);
            $shipping_address = json_decode($combined_order->shipping_address,true);
            return view('frontend.razorpay.order_payment', compact('user','combined_order', 'shipping_address'));
        } elseif ($payment_type == 'wallet_payment') {

            return view('frontend.razorpay.wallet_payment',  compact('user', 'amount'));
        } elseif ($payment_type == 'seller_package_payment') {

            return view('frontend.razorpay.wallet_payment',  compact('user', 'amount','package_id'));
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

        if (count($input) && !empty($input['razorpay_payment_id'])) {
            $payment_detalis = null;
            try {
                $response = $api->payment->fetch($input['razorpay_payment_id'])->capture(array('amount' => $payment['amount']));
                $payment_details = json_encode(array('id' => $response['id'], 'method' => $response['method'], 'amount' => $response['amount'], 'currency' => $response['currency']));

                return response()->json(['result' => true, 'message' => translate("Payment Successful"), 'payment_details' => $payment_details]);
            } catch (\Exception $e) {
                return response()->json(['result' => false, 'message' => $e->getMessage(), 'payment_details' => '']);
            }
        } else {
            return response()->json(['result' => false, 'message' => translate('Payment Failed'), 'payment_details' => '']);
        }
    }

    public function success(Request $request)
    {
        try {

            $payment_type = $request->payment_type;

            if ($payment_type == 'cart_payment') {

                checkout_done($request->combined_order_id, $request->payment_details);
            }

            if ($payment_type == 'wallet_payment') {

                wallet_payment_done($request->user_id, $request->amount, 'Razorpay', $request->payment_details);
            }
            if ($payment_type == 'seller_package_payment') {
                seller_purchase_payment_done($request->user_id, $request->package_id, $request->amount, 'Razorpay', $request->payment_details);
            }

            return response()->json(['result' => true, 'message' => translate("Payment is successful")]);


        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }

}
