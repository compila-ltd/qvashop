<?php

namespace App\Http\Controllers\Payments;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\CheckoutController;
use stdClass;

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

    public function pay(Request $request)
    {
        $combined_order = new stdClass();
        $combined_order->grand_total = 100;

        dd($this->create_invoice($combined_order));

        // [id, address]
    }

    // WebHook from Provider
    public function success(Request $request)
    {
    }

    /**
     * Get an invoice from FF
     */
    public function create_invoice($combined_order): array
    {
        // Determine price based on combined_order totals
        $data = ['fromCurrency' => 'USDT', 'fromQty' => $combined_order->grand_total, 'toCurrency' => 'BTCLN', 'type' => 'float'];
        $totalParams = http_build_query($data);
        $signature = hash_hmac('sha256', $totalParams, $this->secret);

        // request Price data
        $response = Http::withHeaders([
            'X-API-KEY' => $this->key,
            'X-API-SIGN' => $signature
        ])->asForm()->post($this->base_url . '/getPrice', $data);

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
            $address = $response->json()['data']['from']['address'];
            return ['id' => $id, 'address' => $address];
        }

        return [];
    }
}
