<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Address;
use App\Models\Carrier;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\NegotiableTransportation;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use App\Models\CombinedOrder;
use App\Utility\NotificationUtility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Payments\QvaPayController;
use App\Http\Controllers\Payments\LightningController;

class CheckoutController extends Controller
{
    // Check the selected payment gateway and redirect to that controller accordingly
    public function checkout(Request $request)
    {
        //dd($request);
        // Minumum order amount check
        if (get_setting('minimum_order_amount_check') == 1) {

            $subtotal = 0;

            foreach (Cart::where('user_id', Auth::user()->id)->get() as $key => $cartItem) {
                $product = Product::find($cartItem['product_id']);
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
            }

            if ($subtotal < get_setting('minimum_order_amount')) {
                return redirect()->route('home')->with('warning', translate('You order amount is less then the minimum order amount'));
            }
        }

        if ($request->payment_option != null) {
            $payment_method = PaymentMethod::where('short_name', $request->payment_option)->first();

            (new OrderController)->store($request);
            $request->session()->put('payment_type', 'cart_payment');
            $data['combined_order_id'] = $request->session()->get('combined_order_id');
            $request->session()->put('payment_data', $data);

            if ($request->session()->get('combined_order_id') != null) {
                // Pay with QvaPay
                if ($request->payment_option == 'qvapay') {
                    $qvapay = new QvaPayController;
                    return $qvapay->pay($request);

                // Pay with Bitocin LN
                } elseif ($request->payment_option == 'bitcoinln') {

                    $bitcoinln = new LightningController;
                    $wallet = $bitcoinln->pay($request);

                    // Chage this return for a view()
                    return view('frontend.payment.lncpayments', compact('wallet'));

                } elseif ($payment_method->automatic == 0) {
                    return redirect()->route('order_confirmed')->with('success', 'Su orden ha sido completada, pero aún necesita realizar el pago. Por favor, contáctenos');
                } else {

                    $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));
                    $manual_payment_data = array(
                        'name'   => $request->payment_option,
                        'amount' => $combined_order->grand_total,
                        'trx_id' => $request->trx_id,
                        'photo'  => $request->photo
                    );
                    foreach ($combined_order->orders as $order) {
                        $order->manual_payment = 1;
                        $order->manual_payment_data = json_encode($manual_payment_data);
                        $order->save();
                    }

                    return redirect()->route('order_confirmed')->with('success', translate('Your order has been placed successfully. Please submit payment information from purchase history'));
                }
            }
        }

        // Select at least one payment method
        return back()->with('warning', translate('Select Payment Option.'));
    }

    // Redirects to this method after a successfull checkout
    public function checkout_done($combined_order_id, $payment)
    {
        $combined_order = CombinedOrder::findOrFail($combined_order_id);

        foreach ($combined_order->orders as $order) {

            $order = Order::findOrFail($order->id);
            $order->payment_status = 'paid';
            $order->payment_details = $payment;
            $order->save();

            foreach ($order->orderDetails as $order_detail){
                $quantity = $order_detail->quantity;

                $product = $order_detail->product;
                $product->num_of_sale += $quantity;
                $product->current_stock -= $quantity;
                $product->save();
    
                $product_stock = $order_detail->product->stocks->first();
                $product_stock->qty -= $quantity;
                $product_stock->save();
    
                if ($product->added_by == 'seller') {
                    $shop = $product->user->shop;
                    $shop->num_of_sale += $quantity;
                    $shop->save();
                }
            }

            // pay to the seller, or affiliate, or Club Points
            calculateCommissionAffilationClubPoint($order);
        }

        Session::put('combined_order_id', $combined_order_id);
        return redirect()->route('order_confirmed');
    }

    // Get Shipping destination
    public function get_shipping_info(Request $request)
    {
        //dd('get_shipping_info');
        $carts = Cart::where('user_id', Auth::user()->id)->get();
        
        // if (Session::has('cart') && count(Session::get('cart')) > 0) {
        if ($carts && count($carts) > 0) {
            $categories = Category::all();
            //dd($categories);
            return view('frontend.shipping_info', compact('categories', 'carts'));
        }

        return back()->with('warning', translate('Your cart is empty'));
    }

    // Store Shipping destination
    public function store_shipping_info(Request $request)
    {           
        $all_digitals_products = false;

        if ($request->has('all_digitals_products')) 
            if($request->all_digitals_products == 1)
                $all_digitals_products = true;  

        if ($request->address_id == null){
            if($all_digitals_products == false)
                return back()->with('warning', translate('Please add shipping address'));
        }

        $carts = Cart::where('user_id', Auth::user()->id)->get();
        //dd($carts);
        if ($carts->isEmpty())
            return redirect()->route('home')->with('warning', translate('Your cart is empty'));

        foreach ($carts as $key => $cartItem) {
            $cartItem->address_id = $request->address_id;
            $cartItem->save();
        }

        $carrier_list = array();
        if (get_setting('shipping_type') == 'carrier_wise_shipping') {
            $zone = \App\Models\Country::where('id', $carts[0]['address']['country_id'])->first()->zone_id;

            $carrier_query = Carrier::query();
            $carrier_query->whereIn('id', function ($query) use ($zone) {
                $query->select('carrier_id')->from('carrier_range_prices')
                    ->where('zone_id', $zone);
            })->orWhere('free_shipping', 1);
            $carrier_list = $carrier_query->get();
        }

        return view('frontend.delivery_info', compact('carts', 'carrier_list', 'all_digitals_products'));
    }

    // Store Delivery Info
    public function store_delivery_info(Request $request)
    {
        $carts = Cart::where('user_id', Auth::user()->id)->orderBy('owner_id', 'desc')->get();
        //dd($carts);

        // If Cart is empty just redirect to home page
        if ($carts->isEmpty())
            return redirect()->route('home')->with('warning', translate('Your cart is empty'));

        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        $total = 0;
        $tax = 0;
        $shipping = 0;
        $subtotal = 0;

        if ($carts && count($carts) > 0) {
            foreach ($carts as $key => $cartItem) {
                $product = Product::find($cartItem['product_id']);
                $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];

                //dd($request['shipping_type_' . $product->user_id]);

                if (get_setting('shipping_type') != 'carrier_wise_shipping' || $request['shipping_type_' . $product->user_id] == 'pickup_point') {
                    if ($request['shipping_type_' . $product->user_id] == 'pickup_point') {
                        $cartItem['shipping_type'] = 'pickup_point';
                        $cartItem['pickup_point'] = $request['pickup_point_id_' . $product->user_id];
                    } else {
                        $cartItem['shipping_type'] = 'home_delivery';
                    }
                    $cartItem['shipping_cost'] = 0;
                    if ($cartItem['shipping_type'] == 'home_delivery') {
                        //dd($carts);
                        $cartItem['shipping_cost'] = getShippingCost($carts, $key);
                    }
                } else {
                    $cartItem['shipping_type'] = 'carrier';
                    $cartItem['carrier_id'] = $request['carrier_id_' . $product->user_id];
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key, $cartItem['carrier_id']);
                }

                if($cartItem['shipping_cost'] == -1)
                    return redirect()->route('home')->with('warning', translate('Contact support if you have products with negotiable transportation'));

                $shipping += $cartItem['shipping_cost'];
                $cartItem->save();
            }
            $total = $subtotal + $tax + $shipping;
            //dd($carts);
            return view('frontend.payment_select', compact('carts', 'shipping_info', 'total'));
        }

        // EMpty cart message
        return redirect()->route('home')->with('warning', translate('Your cart is empty'));
    }

    // Apply Coupon Code
    public function apply_coupon_code(Request $request)
    {
        $coupon = Coupon::where('code', $request->code)->first();
        $response_message = array();

        if ($coupon != null) {
            if (strtotime(date('d-m-Y')) >= $coupon->start_date && strtotime(date('d-m-Y')) <= $coupon->end_date) {
                if (CouponUsage::where('user_id', Auth::user()->id)->where('coupon_id', $coupon->id)->first() == null) {
                    $coupon_details = json_decode($coupon->details);

                    $carts = Cart::where('user_id', Auth::user()->id)
                        ->where('owner_id', $coupon->user_id)
                        ->get();

                    $coupon_discount = 0;

                    if ($coupon->type == 'cart_base') {
                        $subtotal = 0;
                        $tax = 0;
                        $shipping = 0;
                        foreach ($carts as $key => $cartItem) {
                            $product = Product::find($cartItem['product_id']);
                            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                            $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                            $shipping += $cartItem['shipping_cost'];
                        }
                        $sum = $subtotal + $tax + $shipping;
                        if ($sum >= $coupon_details->min_buy) {
                            if ($coupon->discount_type == 'percent') {
                                $coupon_discount = ($sum * $coupon->discount) / 100;
                                if ($coupon_discount > $coupon_details->max_discount) {
                                    $coupon_discount = $coupon_details->max_discount;
                                }
                            } elseif ($coupon->discount_type == 'amount') {
                                $coupon_discount = $coupon->discount;
                            }
                        }
                    } elseif ($coupon->type == 'product_base') {
                        foreach ($carts as $key => $cartItem) {
                            $product = Product::find($cartItem['product_id']);
                            foreach ($coupon_details as $key => $coupon_detail) {
                                if ($coupon_detail->product_id == $cartItem['product_id']) {
                                    if ($coupon->discount_type == 'percent') {
                                        $coupon_discount += (cart_product_price($cartItem, $product, false, false) * $coupon->discount / 100) * $cartItem['quantity'];
                                    } elseif ($coupon->discount_type == 'amount') {
                                        $coupon_discount += $coupon->discount * $cartItem['quantity'];
                                    }
                                }
                            }
                        }
                    }

                    if ($coupon_discount > 0) {
                        Cart::where('user_id', Auth::user()->id)
                            ->where('owner_id', $coupon->user_id)
                            ->update(
                                [
                                    'discount' => $coupon_discount / count($carts),
                                    'coupon_code' => $request->code,
                                    'coupon_applied' => 1
                                ]
                            );
                        $response_message['response'] = 'success';
                        $response_message['message'] = translate('Coupon has been applied');
                    } else {
                        $response_message['response'] = 'warning';
                        $response_message['message'] = translate('This coupon is not applicable to your cart products!');
                    }
                } else {
                    $response_message['response'] = 'warning';
                    $response_message['message'] = translate('You already used this coupon!');
                }
            } else {
                $response_message['response'] = 'warning';
                $response_message['message'] = translate('Coupon expired!');
            }
        } else {
            $response_message['response'] = 'danger';
            $response_message['message'] = translate('Invalid coupon!');
        }

        $carts = Cart::where('user_id', Auth::user()->id)->get();
        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        $returnHTML = view('frontend.partials.cart_summary', compact('coupon', 'carts', 'shipping_info'))->render();
        return response()->json(array('response_message' => $response_message, 'html' => $returnHTML));
    }

    // Remove Coupon Code
    public function remove_coupon_code(Request $request)
    {
        Cart::where('user_id', Auth::user()->id)
            ->update([
                'discount' => 0.00,
                'coupon_code' => '',
                'coupon_applied' => 0
            ]);

        $coupon = Coupon::where('code', $request->code)->first();
        $carts = Cart::where('user_id', Auth::user()->id)->get();

        $shipping_info = Address::where('id', $carts[0]['address_id'])->first();

        return view('frontend.partials.cart_summary', compact('coupon', 'carts', 'shipping_info'));
    }

    // Apply club points
    public function apply_club_point(Request $request)
    {
        if (addon_is_activated('club_point')) {
            $point = $request->point;
            if (Auth::user()->point_balance >= $point) {
                $request->session()->put('club_point', $point);
                flash(translate('Point has been redeemed'))->success();
            } else {
                flash(translate('Invalid point!'))->warning();
            }
        }
        
        return back();
    }

    // Remove Club Points
    public function remove_club_point(Request $request)
    {
        $request->session()->forget('club_point');
        return back();
    }

    // Order COnfirmed
    public function order_confirmed()
    {
        $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
        $order = Order::where('combined_order_id', Session::get('combined_order_id'))->first();

        $payment_method = PaymentMethod::where('short_name', $order->payment_type)->first();
        
        Cart::where('user_id', $combined_order->user_id)->delete();

        NegotiableTransportation::where('user_id', $combined_order->user_id)
                                ->where('status', 1)
                                ->update(['status' => 0]);

        if($payment_method->automatic == 1) 
            foreach ($combined_order->orders as $order) {
                NotificationUtility::sendOrderPlacedNotification($order);
            }

        
        return view('frontend.order_confirmed', compact('combined_order'));
    }
}
