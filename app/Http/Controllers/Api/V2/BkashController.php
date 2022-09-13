<?php


namespace App\Http\Controllers\Api\V2;

use App\Models\CombinedOrder;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;

class BkashController extends Controller
{
    private $base_url;
    public function __construct()
    {
        if(get_setting('bkash_sandbox', 1)){
            $this->base_url = "https://checkout.sandbox.bka.sh/v1.2.0-beta/";
        }
        else {
            $this->base_url = "https://checkout.pay.bka.sh/v1.2.0-beta/";
        }
    }

    public function begin(Request $request)
    {

        $payment_type = $request->payment_type;
        $combined_order_id = $request->combined_order_id;
        $amount = $request->amount;
        $user_id = $request->user_id;

        try {
            $request_data = array('app_key' => env('BKASH_CHECKOUT_APP_KEY'), 'app_secret' => env('BKASH_CHECKOUT_APP_SECRET'));

            $url = curl_init($this->base_url.'checkout/token/grant');
            $request_data_json = json_encode($request_data);

            $header = array(
                'Content-Type:application/json',
                'username:' . env('BKASH_CHECKOUT_USER_NAME'),
                'password:' . env('BKASH_CHECKOUT_PASSWORD')
            );
            curl_setopt($url, CURLOPT_HTTPHEADER, $header);
            curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
            curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            $resultdata = curl_exec($url);
            curl_close($url);
            $token = json_decode($resultdata)->id_token;

            if($payment_type == 'cart_payment'){
                $combined_order = CombinedOrder::find($combined_order_id);
                $amount = $combined_order->grand_total;
            }

            if($payment_type == 'wallet_payment'){
            
                $amount = $request->amount;
            }
            if($payment_type == 'seller_package_payment'){
                $amount = $request->amount;
            }


            return response()->json([
                'token' => $token,
                'result' => true,
                'url' => route('api.bkash.webpage', ["token" => $token, "amount" => $amount]),
                'message' => translate('Payment page is found')
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'token' => '',
                'result' => false,
                'url' => '',
                'message' => $exception->getMessage()
            ]);
        }


    }

    public function webpage($token, $amount)
    {
        return view('frontend.payment.bkash_app', compact('token', 'amount'));
    }

    public function checkout($token,$amount)
    {
        $auth = $token;

        $callbackURL = route('home');

        $requestbody = array(
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale'
        );
        $url = curl_init($this->base_url.'checkout/payment/create');
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }

    public function execute($token, Request $request)
    {
        $paymentID = $request->paymentID;
        $auth = $token;

        $url = curl_init($this->base_url.'checkout/payment/execute/' . $paymentID);
        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }

    public function process(Request $request)
    {
        try {

            $payment_type = $request->payment_type;

            if ($payment_type == 'cart_payment') {

                checkout_done($request->combined_order_id, $request->payment_details);
            }

            if ($payment_type == 'wallet_payment') {

                wallet_payment_done($request->user_id, $request->amount, 'Bkash', $request->payment_details);
            }

            if ($payment_type == 'seller_package_payment') {

                seller_purchase_payment_done($request->user_id, $request->package_id, $request->amount, 'Bkash', $request->payment_details);
            }

            return response()->json(['result' => true, 'message' => translate("Payment is successful")]);


        } catch (\Exception $e) {
            return response()->json(['result' => false, 'message' => $e->getMessage()]);
        }
    }

    public function success(Request $request)
    {
        return response()->json([
            'result' => true,
            'message' => translate('Payment Success'),
            'payment_details' => $request->payment_details
        ]);

    }

    public function fail(Request $request)
    {
        return response()->json([
            'result' => false,
            'message' => translate('Payment Failed'),
            'payment_details' => $request->payment_details
        ]);
    }

}

