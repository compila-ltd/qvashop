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

class LightningController extends Controller
{
    private $app_key;
    private $base_url;
    private $app_secret;

    public function __construct()
    {
        $this->base_url = config('lightning.base_url');
        $this->app_key = config('lightning.key');
        $this->app_secret = config('lightning.secret');
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
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $wallet = $this->create_invoice($combined_order);

                // Return the view with the wallet data and QR code
                return $wallet;

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

    /**
     * Create invoice with the amount from Bitcoin Price * $combined_order->grand_total
     */
    public function create_invoice($combined_order)
    {
        $amount = floatval($combined_order->grand_total) * 1.015;
        $amount = $this->amount($amount);

        $params = [
            'externalReference' => "qvashop_" . $combined_order->id,
            'amount' => (float) $amount,
            'description' => "QvaShop transaction: " . $combined_order->id,
            'expirationSeconds' => 180,
        ];

        $response = Http::withHeaders([
            'X-Api-Key' => $this->app_secret
        ])->post($this->base_url . "/invoices", $params);

        // Get Wallet data
        $wallet = json_decode((string) $response->getBody());

        // Check for correcta wallet validation
        if (isset($wallet->paymentAddress)) {
            return [
                'value' => number_format(ceil($amount * 100000000), 0, '.', ''),
                'wallet' => $wallet->paymentAddress
            ];
        }

        return false;
    }

    /**
     * Calculate the amount of Bitcoin by USD value
     */
    public function amount($amount)
    {
        $url = "https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd";
        $response = Http::get($url);
        $price = json_decode((string) $response->getBody());
        $price = $price->bitcoin->usd;
        $amount = $amount / $price;
        return $amount;
    }

    // WebHook from LNCPayments
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
}
