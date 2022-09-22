<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CheckoutController;

class QvapayController extends Controller
{
    private $base_url = "https://qvapay.com/api/v1/create_invoice";
    private $app_key;
    private $app_secret;

    public function __construct()
    {
        // Initialize vars
        $this->app_key = config('qvapay.key');
        $this->app_secret = config('qvapay.secret');
    }

    public function pay()
    {

        // Get the data from the request
        if (Session::has('payment_type')) {

            if (Session::get('payment_type') == 'cart_payment') {

                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));

                // Create a QvaPay invoice
                $qvapay_invoice = $this->create_invoice($combined_order);

                // Redirect to to QvaPay payment page
                return redirect($qvapay_invoice);
            }

            /*
            elseif (Session::get('payment_type') == 'wallet_payment') {
                return view('frontend.razor_wallet.wallet_payment_Razorpay');
            } elseif (Session::get('payment_type') == 'customer_package_payment') {
                return view('frontend.razor_wallet.customer_package_payment_Razorpay');
            } elseif (Session::get('payment_type') == 'seller_package_payment') {
                return view('frontend.razor_wallet.seller_package_payment_Razorpay');
            }
            */
        }
    }

    // WebHook from QvaPay
    // /qvapay/payment/pay-success
    public function success(Request $request)
    {
        
        if ($request->has('remote_id') && $request->has('id') && $request->has('uuid')) {

            //$combined_order = CombinedOrder::findOrFail($request->remote_id);

            $input = $request->all();
            $payment_details = json_encode(array('id' => $request['id'], 'method' => 'QvaPay', 'amount' => "", 'currency' => 'USD'));

            return (new CheckoutController)->checkout_done($input['remote_id'], $payment_details);

        } else {
            return redirect()->route('home');
        }

        /*
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
                $payment_detalis = json_encode(array('id' => $response['id'], 'method' => $response['method'], 'amount' => $response['amount'], 'currency' => $response['currency']));
            } catch (\Exception $e) {
                return  $e->getMessage();
                Session::put('error', $e->getMessage());
                return redirect()->back();
            }

            // Do something here for store payment details in database...
            if (Session::has('payment_type')) {
                if (Session::get('payment_type') == 'cart_payment') {
                    return (new CheckoutController)->checkout_done(Session::get('combined_order_id'), $payment_detalis);
                } elseif (Session::get('payment_type') == 'wallet_payment') {
                    return (new WalletController)->wallet_payment_done(Session::get('payment_data'), $payment_detalis);
                } elseif (Session::get('payment_type') == 'customer_package_payment') {
                    return (new CustomerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment_detalis);
                } elseif (Session::get('payment_type') == 'seller_package_payment') {
                    return (new SellerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment_detalis);
                }
            }
        }
        */
    }

    /**
     * Get an invoice from QvaPay
     *
     *    "id" => 6
     *    "user_id" => 10
     *    "shipping_address" => "{"name":"Buyer","email":"neosoft2014@gmail.com","address":"General Lee","country":"Cuba","state":"Ciudad de la Habana","city":"10 de octubre","postal_code":"107 ▶"
     *    "grand_total" => 11.0
     *    "created_at" => "2022-09-20 18:35:19"
     *    "updated_at" => "2022-09-20 18:35:19"
     */
    private function create_invoice($combined_order)
    {
        // get an invoice using Guzzle
        $data = [
            "app_id" => $this->app_key,
            "app_secret" => $this->app_secret,
            "amount" => $combined_order['grand_total'],
            "description" => "QvaShop order " . $combined_order['id'],
            "remote_id" => $combined_order['id'],
            "signed" => 1
        ];

        //Getting a new Wallet
        $response = Http::withHeaders([])->post($this->base_url, $data);

        // Check if the response is successful
        if ($response->successful()) {
            // Get the response body
            $response_body = $response->json();

            // Check if the response body is successful
            if (isset($response_body['signedUrl'])) {
                // Return the invoice
                return $response_body['signedUrl'];
            }
        }
    }
}
