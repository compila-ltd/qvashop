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
    private $base_url = "https://api.qvapay.com/v2/";
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
            // Parsear el payload
            $payload = $request->all();
            
            \Log::info('QvaPay Webhook recibido', [
                'payload' => $payload,
                'headers' => $request->headers->all()
            ]);

            // Verificar que tenga los campos requeridos
            if (!isset($payload['remote_id']) || !isset($payload['uuid'])) {
                \Log::error('QvaPay Webhook: Datos incompletos', [
                    'payload' => $payload
                ]);
                return response()->json(['error' => 'Missing required fields'], 400);
            }

            // IMPORTANTE: Verificar el estado de la transacción en QvaPay para evitar fraudes
            $isPaid = $this->checkTransactionStatus($payload['uuid']);
            
            if (!$isPaid) {
                \Log::warning('QvaPay Webhook: Transacción no pagada o inválida', [
                    'uuid' => $payload['uuid'],
                    'remote_id' => $payload['remote_id']
                ]);
                return response()->json(['message' => 'Payment not confirmed'], 200);
            }

            // Procesar el pago
            $payment_details = json_encode([
                'transaction_uuid' => $payload['uuid'],
                'method' => 'QvaPay',
                'amount' => $payload['amount'] ?? 0,
                'description' => $payload['description'] ?? '',
                'status' => 'paid'
            ]);

            // Completar la orden
            $result = (new CheckoutController)->checkout_done($payload['remote_id'], $payment_details);
            
            \Log::info('QvaPay Webhook procesado exitosamente', [
                'remote_id' => $payload['remote_id'],
                'uuid' => $payload['uuid'],
                'result' => $result
            ]);

            return response()->json(['message' => 'Webhook processed successfully'], 200);
            
        } catch (\Exception $e) {
            \Log::error('QvaPay Webhook Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            \Sentry\captureException($e);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Verificar el estado de una transacción en QvaPay
     * Retorna true si la transacción está pagada, false en caso contrario
     */
    private function checkTransactionStatus($uuid)
    {
        try {
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'app-id' => $this->app_key,
                'app-secret' => $this->app_secret
            ])->post($this->base_url . 'transactions/' . $uuid);

            if ($response->successful()) {
                $transaction = $response->json();
                
                \Log::info('QvaPay Transaction Status', [
                    'uuid' => $uuid,
                    'status' => $transaction['transaction']['status'] ?? 'unknown',
                    'response' => $transaction
                ]);
                
                if (isset($transaction['transaction']['status']) && $transaction['transaction']['status'] == 'paid') {
                    return true;
                }
            } else {
                \Log::error('QvaPay checkTransactionStatus: Error en la respuesta', [
                    'uuid' => $uuid,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            }
            
            return false;
        } catch (\Exception $e) {
            \Log::error('QvaPay checkTransactionStatus Exception', [
                'uuid' => $uuid,
                'message' => $e->getMessage()
            ]);
            
            \Sentry\captureException($e);
            return false;
        }
    }

    // Método legacy para retrocompatibilidad (si aún se usa)
    public function success(Request $request)
    {
        if ($request->has('remote_id') && $request->has('uuid')) {
            
            // Verificar el estado de la transacción en QvaPay
            $isPaid = $this->checkTransactionStatus($request->input('uuid'));
            
            if (!$isPaid) {
                \Log::warning('QvaPay Success (legacy): Transacción no pagada', [
                    'uuid' => $request->input('uuid'),
                    'remote_id' => $request->input('remote_id')
                ]);
                return redirect()->route('home');
            }

            $input = $request->all();
            $payment_details = json_encode([
                'transaction_uuid' => $request->input('uuid'),
                'id' => $request->input('id'),
                'method' => 'QvaPay',
                'amount' => $request->input('amount', ''),
                'status' => 'paid'
            ]);

            return (new CheckoutController)->checkout_done($input['remote_id'], $payment_details);
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
            $amount = $combined_order->grand_total;
            $amount = str_replace(',', '.', (string)$amount); // convierte 12,50 → 12.50
            $amount = (float)$amount;                         // ahora sí es 12.50 real
            $amount = round($amount, 2);                      // asegura 2 decimales
            
            $data = [
                "amount" => $amount,
                "description" => "QvaShop order " . $combined_order->id,
                "remote_id" => (string) $combined_order->id,
                "webhook" => route('payment.qvapay')
            ];

            // Log del request para debugging
            \Log::info('QvaPay Request', [
                'order_id' => $combined_order->id,
                'amount' => $amount,
                'data' => $data
            ]);

            // Realizar request con autenticación en headers (API v2)
            $response = Http::withHeaders([
                'accept' => 'application/json',
                'app-id' => $this->app_key,
                'app-secret' => $this->app_secret
            ])->post($this->base_url . 'create_invoice', $data);

            // Log de la respuesta
            \Log::info('QvaPay Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            // Check if the response is successful (status 200)
            if ($response->status() == 200) {
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
                    \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($errorMessage, $response_body, $combined_order) {
                        $scope->setContext('qvapay', [
                            'response' => $response_body,
                            'order_id' => $combined_order->id
                        ]);
                        \Sentry\captureMessage($errorMessage, \Sentry\Severity::error());
                    });
                }
            } else {
                $errorMessage = 'QvaPay API Error - Status: ' . $response->status();
                \Log::error($errorMessage, [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'order_id' => $combined_order->id
                ]);
                
                // Reportar a Sentry
                \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($errorMessage, $response, $combined_order) {
                    $scope->setContext('qvapay', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'order_id' => $combined_order->id
                    ]);
                    \Sentry\captureMessage($errorMessage, \Sentry\Severity::error());
                });
            }
        } catch (\Exception $e) {
            \Log::error('QvaPay Exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'order_id' => $combined_order->id
            ]);
            
            // Reportar excepción a Sentry
            \Sentry\withScope(function (\Sentry\State\Scope $scope) use ($e, $combined_order) {
                $scope->setContext('qvapay', [
                    'order_id' => $combined_order->id
                ]);
                \Sentry\captureException($e);
            });
        }
        
        // Si algo salió mal, devolver null
        return null;
    }
}
