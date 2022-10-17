<?php

namespace App\Http\Controllers\Payments;

use stdClass;
use Illuminate\Http\Request;
use App\Models\BitcoinWallet;
use App\Models\CombinedOrder;
use App\Models\SellerPackage;
use App\Models\CustomerPackage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\CheckoutController;

class LightningController extends Controller
{
    private $base_url;
    private $key;
    private $secret;
    private $usdt_address;

    public function __construct()
    {
        // Initialize vars
        $this->base_url = config('lightning.base_url');
        $this->key = config('lightning.key');
        $this->secret = config('lightning.secret');
        $this->usdt_address = config('lightning.usdt_address');
    }

    /**
     * Generate wallet and qr code to pay
     */
    public function pay()
    {
        // Get the data from the request
        if (Session::has('payment_type')) {
            if (Session::get('payment_type') == 'cart_payment') {
                // Show invoice, not redirect
                $order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $wallet = $this->create_invoice($order);
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

        return $wallet;
    }

    // WebHook from Provider
    public function success()
    {
    }

    /**
     * Get an invoice from FF
     */
    public function create_invoice($combined_order): array
    {
        // Calculate the amount with env taxes
        $amount = round($combined_order->grand_total * (1 + (config('lightning.tax') / 100)));

        // Determine price based on combined_order totals
        $data = ['fromCurrency' => 'USDTTRC', 'fromQty' => $amount, 'toCurrency' => 'BTCLN', 'type' => 'float'];
        $totalParams = http_build_query($data);
        $signature = hash_hmac('sha256', $totalParams, $this->secret);

        // request Price data
        $response = Http::withHeaders([
            'X-API-KEY' => $this->key,
            'X-API-SIGN' => $signature
        ])->asForm()->post($this->base_url . '/getPrice', $data);

        $btc_amount = $response->json()['data']['to']['amount'];

        // Get an invoice
        $data = [
            "fromCurrency" => "BTCLN",
            "toCurrency" => "USDTTRC",
            "fromQty" => $response->json()['data']['to']['amount'],
            "toAddress" => $this->usdt_address,
            "type" => "fixed",
        ];

        // Create the totalParams as query string of body params
        $totalParams = http_build_query($data);

        // HMAC SHA256 with key and paramenters
        $signature = hash_hmac('sha256', $totalParams, $this->secret);

        //Getting a new Wallet
        $response = Http::withHeaders([
            'X-API-KEY' => $this->key,
            'X-API-SIGN' => $signature
        ])->asForm()->post($this->base_url . '/createOrder', $data);

        // Return ID and Wallet
        if (isset($response->json()['msg']) && $response->json()['msg'] == "OK" && isset($response->json()['data'])) {

            $id = $response->json()['data']['id'];
            $invoice = $response->json()['data']['from']['address'];
            $token = $response->json()['data']['token'];

            // Now save to DB
            $this->save_invoice($combined_order, $id, $invoice, $token, $btc_amount);

            return ['id' => $id, 'invoice' => $invoice, 'token' => $token, 'btc_amount' => $btc_amount];
        }

        return [];
    }

    /**
     * Save this invoice into DB Model
     */
    private function save_invoice($combined_order, $id, $invoice, $token, $btc_amount)
    {
        $data = [
            'combined_order_id' => $combined_order->id,
            'invoice_id' => $id,
            'invoice' => $invoice,
            'token' => $token,
            'btc_amount' =>$btc_amount,
            'status' => 'pending',
        ];

        $stored_wallet = BitcoinWallet::create($data);

        return $stored_wallet;
    }
}
