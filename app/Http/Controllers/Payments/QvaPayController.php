<?php

namespace App\Http\Controllers\Payments;

use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Models\SellerPackage;
use App\Models\CustomerPackage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CheckoutController;

class QvaPayController extends Controller
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
                return redirect($this->create_invoice(CombinedOrder::findOrFail(Session::get('combined_order_id'))));
            } elseif (Session::get('payment_type') == 'wallet_payment') {
                $amount = round(Session::get('payment_data')['amount']);
            } elseif (Session::get('payment_type') == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
                $amount = round($customer_package->amount);
            } elseif (Session::get('payment_type') == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
                $amount = round($seller_package->amount);
            }
        }
    }

    // WebHook from QvaPay
    public function success(Request $request)
    {
        if ($request->has('remote_id') && $request->has('id') && $request->has('uuid')) {

            $input = $request->all();
            $payment_details = json_encode(array('id' => $request['id'], 'method' => 'QvaPay', 'amount' => "", 'currency' => 'USD'));

            $payment_type = 'cart_payment';

            // Always process this data
            if ($payment_type == 'cart_payment')
                return (new CheckoutController)->checkout_done($input['remote_id'], $payment_details);

            /*
            if ($payment_type == 'wallet_payment') {
                return (new WalletController)->wallet_payment_done(json_decode($request->opt_c), json_encode($request->all()));
            }
            if ($payment_type == 'customer_package_payment') {
                return (new CustomerPackageController)->purchase_payment_done(json_decode($request->opt_c), json_encode($request->all()));
            }
            if ($payment_type == 'seller_package_payment') {
                return (new SellerPackageController)->purchase_payment_done(json_decode($request->opt_c), json_encode($request->all()));
            }
            */
        }

        return redirect()->route('home');
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
            "amount" => $combined_order->grand_total,
            "description" => "QvaShop order " . $combined_order->id,
            "remote_id" => $combined_order->id,
            "signed" => 1
        ];

        //Getting a new Wallet
        $response = Http::withHeaders([])->post($this->base_url, $data);

        // Check if the response is successful
        if ($response->successful()) {
            // Get the response body
            $response_body = $response->json();
            // Check if the response body is successful
            if (isset($response_body['signedUrl']))
                return $response_body['signedUrl'];
        }
    }
}
