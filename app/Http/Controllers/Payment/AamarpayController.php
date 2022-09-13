<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Models\CombinedOrder;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CheckoutController;
use Session;
use Auth;

class AamarpayController extends Controller
{
    public function pay(){
        if (Auth::user()->phone == null) {
            flash('Please add phone number to your profile')->warning();
            return redirect()->route('profile');
        }
        
        if (Auth::user()->email == null) {
            $email = 'customer@exmaple.com';
        }
        else{
            $email = Auth::user()->email;
        }

        if (get_setting('aamarpay_sandbox') == 1) {
            $url = 'https://sandbox.aamarpay.com/request.php'; // live url https://secure.aamarpay.com/request.php
        }
        else {
            $url = 'https://secure.aamarpay.com/request.php';
        }

        $amount = 0;
        if(Session::has('payment_type')){
            if(Session::get('payment_type') == 'cart_payment'){
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $amount = round($combined_order->grand_total);
            }
            elseif (Session::get('payment_type') == 'wallet_payment') {
                $amount = round(Session::get('payment_data')['amount']);
            }
            elseif (Session::get('payment_type') == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
                $amount = round($customer_package->amount);
            }
            elseif (Session::get('payment_type') == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
                $amount = round($seller_package->amount);
            }
        }

        $fields = array(
            'store_id' => env('AAMARPAY_STORE_ID'), //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
            'amount' => $amount, //transaction amount
            'payment_type' => 'VISA', //no need to change
            'currency' => 'BDT',  //currenct will be USD/BDT
            'tran_id' => rand(1111111,9999999), //transaction id must be unique from your end
            'cus_name' => Auth::user()->name,  //customer name
            'cus_email' => $email, //customer email address
            'cus_add1' => '',  //customer address
            'cus_add2' => '', //customer address
            'cus_city' => '',  //customer city
            'cus_state' => '',  //state
            'cus_postcode' => '', //postcode or zipcode
            'cus_country' => 'Bangladesh',  //country
            'cus_phone' => Auth::user()->phone, //customer phone number
            'cus_fax' => 'Not¬Applicable',  //fax
            'ship_name' => '', //ship name
            'ship_add1' => '',  //ship address
            'ship_add2' => '',
            'ship_city' => '',
            'ship_state' => '',
            'ship_postcode' => '',
            'ship_country' => 'Bangladesh',
            'desc' => env('APP_NAME').' payment',
            'success_url' => route('aamarpay.success'), //your success route
            'fail_url' => route('aamarpay.fail'), //your fail route
            'cancel_url' => route('cart'), //your cancel url
            'opt_a' => Session::get('payment_type'),  //optional paramter
            'opt_b' => Session::get('combined_order_id'),
            'opt_c' => json_encode(Session::get('payment_data')),
            'opt_d' => '',
            'signature_key' => env('AAMARPAY_SIGNATURE_KEY') //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key
        );

        $fields_string = http_build_query($fields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $url_forward = str_replace('"', '', stripslashes(curl_exec($ch)));
        curl_close($ch);

        $this->redirect_to_merchant($url_forward);
    }

    function redirect_to_merchant($url) {
        if (get_setting('aamarpay_sandbox') == 1) {
            $base_url = 'https://sandbox.aamarpay.com/';
        }
        else {
            $base_url = 'https://secure.aamarpay.com/';
        }

        ?>
        <html xmlns="http://www.w3.org/1999/xhtml">
          <head><script type="text/javascript">
            function closethisasap() { document.forms["redirectpost"].submit(); }
          </script></head>
          <body onLoad="closethisasap();">

            <form name="redirectpost" method="post" action="<?php echo $base_url.$url; ?>"></form>

          </body>
        </html>
        <?php
        exit;
    }


    public function success(Request $request){
        $payment_type = $request->opt_a;

        if ($payment_type == 'cart_payment') {
            return (new CheckoutController)->checkout_done($request->opt_b, json_encode($request->all()));
        }

        if ($payment_type == 'wallet_payment') {
            return (new WalletController)->wallet_payment_done(json_decode($request->opt_c), json_encode($request->all()));
        }

        if ($payment_type == 'customer_package_payment') {
            return (new CustomerPackageController)->purchase_payment_done(json_decode($request->opt_c), json_encode($request->all()));
        }
        if($payment_type == 'seller_package_payment') {
            return (new SellerPackageController)->purchase_payment_done(json_decode($request->opt_c), json_encode($request->all()));
        }
    }

    public function fail(Request $request){
        flash(translate('Payment failed'))->error();
    	return redirect()->route('cart');
    }
}
