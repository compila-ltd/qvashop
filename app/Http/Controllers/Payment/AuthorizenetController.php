<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CheckoutController;
use Auth;
use Session;

class AuthorizenetController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth'); // later enable it when needed user login while payment
    }

    // start page form after start
    public function pay() {
        return view('frontend/authorize_net/pay');
    }

    public function handleonlinepay(Request $request) {
        $input = $request->input();
        $user = Auth::user();
        $invoiceNumber = '';
        $lastName = '';
        $address = '';
        $city = '';
        $zip = '';
        $country = '';

		if(Session::get('payment_type') == 'cart_payment'){
            $database_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
            $first_order = $database_order->orders->first();

            $invoiceNumber = time().$database_order->id;
            $lastName = json_decode($first_order->shipping_address)->name;
            $address = json_decode($first_order->shipping_address)->address;
			$amount = $database_order->orders->sum('grand_total');
            $city = json_decode($first_order->shipping_address)->city;
            $zip = json_decode($first_order->shipping_address)->postal_code;
            $country = json_decode($first_order->shipping_address)->country;
		}
		
		elseif (Session::get('payment_type') == 'wallet_payment') {
            $invoiceNumber = rand(10000,99999);
			$amount= Session::get('payment_data')['amount'];
            $lastName = $user->name;
		}
		
		elseif (Session::get('payment_type') == 'customer_package_payment') {
            $invoiceNumber = rand(10000,99999);
			$customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
			$amount = $customer_package->amount;
            $lastName = $user->name;
		}
		
		elseif (Session::get('payment_type') == 'seller_package_payment') {
            $invoiceNumber = rand(10000,99999);
			$seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
			$amount = $seller_package->amount;
            $lastName = $user->name;
		}
        
        /* Create a merchantAuthenticationType object with authentication details
          retrieved from the constants file */
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(env('MERCHANT_LOGIN_ID'));
        $merchantAuthentication->setTransactionKey(env('MERCHANT_TRANSACTION_KEY'));

        // Set the transaction's refId
        $refId = 'ref' . time();
        $cardNumber = preg_replace('/\s+/', '', $input['cardNumber']);
        
        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($input['expiration-year'] . "-" .$input['expiration-month']);
        $creditCard->setCardCode($input['cvv']);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);
        
        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($invoiceNumber);
//        $order->setDescription("Golf Shirts");

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName("");
        $customerAddress->setLastName($lastName);
        $customerAddress->setAddress($address);
        $customerAddress->setCity($city);
        $customerAddress->setZip($zip);
        $customerAddress->setCountry($country);

        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setId($user->id);
        $customerData->setEmail($user->email);

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);

        // Assemble the complete transaction request
        $requests = new AnetAPI\CreateTransactionRequest();
        $requests->setMerchantAuthentication($merchantAuthentication);
        $requests->setRefId($refId);
        $requests->setTransactionRequest($transactionRequestType);

        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($requests);
        if (get_setting('authorizenet_sandbox') == 1) {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
        } else {
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        }
        
        // dd($response);
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {
                    // echo " Successfully created transaction with Transaction ID: " . $tresponse->getTransId() . "\n";
                    // echo " Transaction Response Code: " . $tresponse->getResponseCode() . "\n";
                    // echo " Message Code: " . $tresponse->getMessages()[0]->getCode() . "\n";
                    // echo " Auth Code: " . $tresponse->getAuthCode() . "\n";
                    // echo " Description: " . $tresponse->getMessages()[0]->getDescription() . "\n";
                    $payment_detalis = json_encode(
                            array(
                                'transId' => $tresponse->getTransId(),
                                'authCode' => $tresponse->getAuthCode(),
                                'accountType' => $tresponse->getAccountType(),
                                'accountNumber' => $tresponse->getAccountNumber(),
                                'refId' => $response->getRefId(),
                            )
                    );
                    $message_text = $tresponse->getMessages()[0]->getDescription().", Transaction ID: " . $tresponse->getTransId();
                    $msg_type = "success_msg";    
                    
					if(Session::get('payment_type') == 'cart_payment'){
						return (new CheckoutController)->checkout_done(Session::get('combined_order_id'), $payment_detalis);
					}
					elseif (Session::get('payment_type') == 'wallet_payment') {
						return (new WalletController)->wallet_payment_done(Session::get('payment_data'), $payment_detalis);
					}
					elseif (Session::get('payment_type') == 'customer_package_payment') {
						return (new CustomerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment_detalis);
					}
					elseif (Session::get('payment_type') == 'seller_package_payment') {
						return (new SellerPackageController)->purchase_payment_done(Session::get('payment_data'), $payment_detalis);
					}
                    
                } else {
                    $message_text = 'There were some issue with the payment. Please try again later.';
                    $msg_type = "error_msg";                                    

                    if ($tresponse->getErrors() != null) {
                        $message_text = $tresponse->getErrors()[0]->getErrorText();
                        $msg_type = "error_msg";                                    
                    }
                }
                // Or, print errors if the API request wasn't successful
            } else {
                $message_text = 'There were some issue with the payment. Please try again later.';
                $msg_type = "error_msg";                                    

                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    $message_text = $tresponse->getErrors()[0]->getErrorText();
                    $msg_type = "error_msg";                    
                } else {
                    $message_text = $response->getMessages()->getMessage()[0]->getText();
                    $msg_type = "error_msg";
                }                
            }
        } else {
            $message_text = "No response returned";
            $msg_type = "error_msg";
        }
        
        Session::forget('combined_order_id');
        flash(translate($message_text))->success();
        return redirect()->route('home');
    }
}
