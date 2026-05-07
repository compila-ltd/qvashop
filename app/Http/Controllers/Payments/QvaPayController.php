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
    private $base_url = "https://qvapay.com/api/v2/create_invoice";
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
                $invoice_url = $this->create_invoice(CombinedOrder::findOrFail(Session::get('combined_order_id')));
                
                if ($invoice_url) {
                    return redirect()->to($invoice_url);
                } else {
                    flash(translate('No se pudo procesar el pago. Por favor, intente con otro método de pago.'))->error();
                    return redirect()->route('checkout.shipping_info');
                }
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
        
        // Si no hay tipo de pago o no se procesó, redirigir al home
        flash(translate('Ha ocurrido un error. Por favor, intente nuevamente.'))->error();
        return redirect()->route('home');
    }

    // WebHook from QvaPay (API v2)
    public function webhook(Request $request)
    {
        try {
            // Verificar firma HMAC
            $signature = $request->header('x-qvapay-signature');
            $rawBody = $request->getContent();
            
            if (!$this->verifyWebhookSignature($rawBody, $signature)) {
                \Log::error('QvaPay Webhook: Firma inválida', [
                    'signature' => $signature
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Parsear el payload
            $payload = $request->all();
            
            \Log::info('QvaPay Webhook recibido', [
                'payload' => $payload
            ]);

            // Verificar que tenga los campos requeridos
            if (!isset($payload['remote_id']) || !isset($payload['status'])) {
                \Log::error('QvaPay Webhook: Datos incompletos', [
                    'payload' => $payload
                ]);
                return response()->json(['error' => 'Missing required fields'], 400);
            }

            // Verificar que el pago esté completado
            if ($payload['status'] !== 'paid') {
                \Log::info('QvaPay Webhook: Estado no pagado', [
                    'status' => $payload['status'],
                    'remote_id' => $payload['remote_id']
                ]);
                return response()->json(['message' => 'Payment not completed'], 200);
            }

            // Procesar el pago
            $payment_details = json_encode([
                'transaction_uuid' => $payload['uuid'] ?? null,
                'method' => 'QvaPay',
                'amount' => $payload['amount'] ?? 0,
                'description' => $payload['description'] ?? '',
                'status' => $payload['status']
            ]);

            // Completar la orden
            $result = (new CheckoutController)->checkout_done($payload['remote_id'], $payment_details);
            
            \Log::info('QvaPay Webhook procesado exitosamente', [
                'remote_id' => $payload['remote_id']
            ]);

            return response()->json(['message' => 'Webhook processed successfully'], 200);
            
        } catch (\Exception $e) {
            \Log::error('QvaPay Webhook Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            \Sentry\captureException($e);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Verificar la firma HMAC del webhook
     */
    private function verifyWebhookSignature($rawBody, $signatureHeader)
    {
        if (!$signatureHeader || !str_starts_with($signatureHeader, 'sha256=')) {
            return false;
        }
        
        $provided = substr($signatureHeader, 7);
        $expected = hash_hmac('sha256', $rawBody, $this->app_secret);
        
        return hash_equals($expected, $provided);
    }

    // Método legacy para retrocompatibilidad (si aún se usa)
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
        try {
            // Preparar datos del request según API v2
            $data = [
                "amount" => $combined_order->grand_total,
                "description" => "QvaShop order " . $combined_order->id,
                "remote_id" => (string) $combined_order->id,
                "webhook" => route('payment.qvapay')
            ];

            // Log del request para debugging
            \Log::info('QvaPay Request', [
                'order_id' => $combined_order->id,
                'amount' => $combined_order->grand_total,
                'data' => $data
            ]);

            // Realizar request con autenticación en headers (API v2)
            $response = Http::withHeaders([
                'app-id' => $this->app_key,
                'app-secret' => $this->app_secret
            ])->post($this->base_url, $data);

            // Log de la respuesta
            \Log::info('QvaPay Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Check if the response is successful
            if ($response->successful()) {
                // Get the response body
                $response_body = $response->json();
                // Check if the response body is successful (API v2 usa 'url')
                if (isset($response_body['url'])) {
                    return $response_body['url'];
                } else {
                    $errorMessage = 'QvaPay: url no encontrado en la respuesta';
                    \Log::error($errorMessage, [
                        'response' => $response_body
                    ]);
                    
                    // Reportar a Sentry
                    \Sentry\captureMessage($errorMessage, \Sentry\Severity::error(), [
                        'extra' => [
                            'response' => $response_body,
                            'order_id' => $combined_order->id
                        ]
                    ]);
                }
            } else {
                $errorMessage = 'QvaPay API Error - Status: ' . $response->status();
                \Log::error($errorMessage, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'order_id' => $combined_order->id
                ]);
                
                // Reportar a Sentry
                \Sentry\captureMessage($errorMessage, \Sentry\Severity::error(), [
                    'extra' => [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'order_id' => $combined_order->id
                    ]
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('QvaPay Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'order_id' => $combined_order->id
            ]);
            
            // Reportar excepción a Sentry
            \Sentry\captureException($e, [
                'extra' => [
                    'order_id' => $combined_order->id
                ]
            ]);
        }
        
        // Si algo salió mal, devolver null
        return null;
    }
}
