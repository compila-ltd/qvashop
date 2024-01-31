@extends('frontend.layouts.app')

@section('content')
    <section class="pt-5 mb-4">
        <div class="container">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    <div class="row aiz-steps arrow-divider">
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-shopping-cart"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart')}}</h3>
                            </div>
                        </div>
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-map"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('2. Shipping info')}}</h3>
                            </div>
                        </div>
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-truck"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('3. Delivery info')}}</h3>
                            </div>
                        </div>
                        <div class="col done">
                            <div class="text-center text-success">
                                <i class="la-3x mb-2 las la-credit-card"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('4. Payment')}}</h3>
                            </div>
                        </div>
                        <div class="col active">
                            <div class="text-center text-primary">
                                <i class="la-3x mb-2 las la-check-circle"></i>
                                <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('5. Confirmation')}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-4">
        <div class="container text-left">
            <div class="row">
                <div class="col-xl-8 mx-auto">
                    @php
                        $first_order = $combined_order->orders->first();
                        //dd($first_order);
                    @endphp
                    <div class="text-center py-4 mb-4">
                        <i class="la la-check-circle la-3x text-success mb-3"></i>
                        <h1 class="h3 mb-3 fw-600">{{ translate('Thank You for Your Order!')}}</h1>
                        @if($first_order->payment_status == 'unpaid' && ($first_order->payment_type == 'cup_payment' || $first_order->payment_type == 'mlc_payment'))
                            @php 
                                $payment_type = "";
                                $total_cost = 0;
                                
                                if($first_order->payment_type == 'cup_payment')
                                {
                                    $payment_type = "CUP";
                                    $total_cost = single_price($combined_order->grand_total_cup);
                                }
                                
                                if($first_order->payment_type == 'mlc_payment')
                                {
                                    $payment_type = "MLC";
                                    $total_cost = single_price($combined_order->grand_total_mlc); 
                                }
                            @endphp
                            <p class="h4 opacity-70 font-italic">Usted aún necesita pagar su orden con código: <span class="fw-600"> {{strtotime($combined_order->created_at)}}</span>.</p> 
                            <p class="h4 opacity-70 font-italic">Contáctenos mediante <a href="https://wa.me/{{ get_setting('helpline_number') }}?text=<?php echo urlencode('Hola. Mi nombre de usuario en QvaShop es: '.json_decode($first_order->shipping_address)->name.' y quiero pagar la orden con código: '.strtotime($combined_order->created_at).' con un importe de '.$total_cost.' '.$payment_type.' '); ?>" target="_blank"><span class="fw-600">WhatsApp</span></a> para realizar su pago.</p>
                        @else
                            <p class="opacity-70 font-italic">{{  translate('A copy or your order summary has been sent to') }} {{ json_decode($first_order->shipping_address)->email }}</p>
                        @endif
                    </div>
                    <div class="mb-4 bg-white p-4 rounded shadow-sm">
                        <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Summary')}}</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Order date')}}:</td>
                                        <td>{{ date('d-m-Y H:i A', $first_order->date) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Name')}}:</td>
                                        <td>{{ json_decode($first_order->shipping_address)->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Email')}}:</td>
                                        <td>{{ json_decode($first_order->shipping_address)->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Shipping address')}}:</td>
                                        <td>{{ json_decode($first_order->shipping_address)->address }}, {{ json_decode($first_order->shipping_address)->city }}, {{ json_decode($first_order->shipping_address)->country }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table">
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Order status')}}:</td>
                                        @if($first_order->payment_status == 'unpaid' && ($first_order->payment_type == 'cup_payment' || $first_order->payment_type == 'mlc_payment'))
                                            <td>Pendiente a pago</td>
                                        @else    
                                            <td>{{ translate(ucfirst(str_replace('_', ' ', $first_order->delivery_status))) }}</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Total order amount')}}:</td>
                                        @if($first_order->payment_type == 'cup_payment')
                                            <td>{{ single_price($combined_order->grand_total_cup) }}</td>
                                        @else
                                            @if($first_order->payment_type == 'mlc_payment')
                                                <td>{{ single_price($combined_order->grand_total_mlc) }}</td>
                                            @else
                                                <td>{{ single_price($combined_order->grand_total) }}</td>
                                            @endif
                                        @endif
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Shipping')}}:</td>
                                        <td>{{ translate('Flat shipping rate')}}</td>
                                    </tr>
                                    <tr>
                                        <td class="w-50 fw-600">{{ translate('Payment method')}}:</td>
                                        @if($first_order->payment_type == 'cup_payment')
                                            <td>CUP</td>
                                        @else
                                            @if($first_order->payment_type == 'mlc_payment')
                                                <td>MLC</td>
                                            @else
                                                <td>{{ translate(ucfirst(str_replace('_', ' ', $first_order->payment_type))) }}</td>
                                            @endif
                                        @endif
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    @foreach ($combined_order->orders as $order)
                        <div class="card shadow-sm border-0 rounded">
                            <div class="card-body">
                                <div class="text-center py-4 mb-4">
                                    <h2 class="h5">{{ translate('Order Code:')}} <span class="fw-700 text-primary">{{ $order->code }}</span></h2>
                                </div>
                                <div>
                                    <h5 class="fw-600 mb-3 fs-17 pb-2">{{ translate('Order Details')}}</h5>
                                    <div>
                                        <table class="table table-responsive-md">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th width="30%">{{ translate('Product')}}</th>
                                                    <th>{{ translate('Variation')}}</th>
                                                    <th>{{ translate('Quantity')}}</th>
                                                    <th>{{ translate('Delivery Type')}}</th>
                                                    <th class="text-right">{{ translate('Price')}}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->orderDetails as $key => $orderDetail)
                                                    <tr>
                                                        <td>{{ $key+1 }}</td>
                                                        <td>
                                                            @if ($orderDetail->product != null)
                                                                <a href="{{ route('product', $orderDetail->product->slug) }}" target="_blank" class="text-reset">
                                                                    {{ $orderDetail->product->getTranslation('name') }}
                                                                    @php
                                                                        if($orderDetail->combo_id != null) {
                                                                            $combo = \App\ComboProduct::findOrFail($orderDetail->combo_id);

                                                                            echo '('.$combo->combo_title.')';
                                                                        }
                                                                    @endphp
                                                                </a>
                                                            @else
                                                                <strong>{{  translate('Product Unavailable') }}</strong>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{ $orderDetail->variation }}
                                                        </td>
                                                        <td>
                                                            {{ $orderDetail->quantity }}
                                                        </td>
                                                        <td>
                                                            @if ($order->shipping_type != null && $order->shipping_type == 'home_delivery')
                                                                {{  translate('Home Delivery') }}
                                                            @elseif ($order->shipping_type != null && $order->shipping_type == 'carrier')
                                                                {{  translate('Carrier') }}
                                                            @elseif ($order->shipping_type == 'pickup_point')
                                                                @if ($order->pickup_point != null)
                                                                    {{ $order->pickup_point->getTranslation('name') }} ({{ translate('Pickip Point') }})
                                                                @endif
                                                            @endif
                                                        </td>
                                                        
                                                        @if($order->payment_type == 'cup_payment')
                                                            <td class="text-right">{{ single_price($orderDetail->price_cup) }}</td>
                                                        @else
                                                            @if($order->payment_type == 'mlc_payment')
                                                                <td class="text-right">{{ single_price($orderDetail->price_mlc) }}</td>
                                                            @else
                                                                <td class="text-right">{{ single_price($orderDetail->price) }}</td>
                                                            @endif
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="col-xl-5 col-md-6 ml-auto mr-0">
                                            <table class="table ">
                                                <tbody>
                                                    <tr>
                                                        <th>{{ translate('Subtotal')}}</th>
                                                        @if($order->payment_type == 'cup_payment')
                                                            <td class="text-right">
                                                                <span class="fw-600">{{ single_price($order->orderDetails->sum('price_cup')) }}</span>
                                                            </td>
                                                        @else
                                                            @if($order->payment_type == 'mlc_payment')
                                                                <td class="text-right">
                                                                    <span class="fw-600">{{ single_price($order->orderDetails->sum('price_mlc')) }}</span>
                                                                </td>
                                                            @else
                                                                <td class="text-right">
                                                                    <span class="fw-600">{{ single_price($order->orderDetails->sum('price')) }}</span>
                                                                </td>
                                                            @endif
                                                        @endif
                                                        
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Shipping')}}</th>
                                                        @if($order->payment_type == 'cup_payment')
                                                            <td class="text-right">
                                                                <span class="font-italic">{{ single_price($order->orderDetails->sum('shipping_cost_cup')) }}</span>
                                                            </td>
                                                        @else
                                                            @if($order->payment_type == 'mlc_payment')
                                                                <td class="text-right">
                                                                    <span class="font-italic">{{ single_price($order->orderDetails->sum('shipping_cost_mlc')) }}</span>
                                                                </td>
                                                            @else
                                                                <td class="text-right">
                                                                    <span class="font-italic">{{ single_price($order->orderDetails->sum('shipping_cost')) }}</span>
                                                                </td>
                                                            @endif
                                                        @endif
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Tax')}}</th>
                                                        <td class="text-right">
                                                            <span class="font-italic">{{ single_price($order->orderDetails->sum('tax')) }}</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>{{ translate('Coupon Discount')}}</th>
                                                        @if($order->payment_type == 'cup_payment')
                                                            <td class="text-right">
                                                                <span class="font-italic">{{ single_price($order->coupon_discount_cup) }}</span>
                                                            </td>
                                                        @else
                                                            @if($order->payment_type == 'mlc_payment')
                                                                <td class="text-right">
                                                                    <span class="font-italic">{{ single_price($order->coupon_discount_mlc) }}</span>
                                                                </td>
                                                            @else
                                                                <td class="text-right">
                                                                    <span class="font-italic">{{ single_price($order->coupon_discount) }}</span>
                                                                </td>
                                                            @endif
                                                        @endif
                                                    </tr>
                                                    <tr>
                                                        <th><span class="fw-600">{{ translate('Total')}}</span></th>
                                                        @if($order->payment_type == 'cup_payment')
                                                            <td class="text-right">
                                                                <strong><span>{{ single_price($order->grand_total_cup) }}</span></strong>
                                                            </td>
                                                        @else
                                                            @if($order->payment_type == 'mlc_payment')
                                                            <td class="text-right">
                                                                <strong><span>{{ single_price($order->grand_total_mlc) }}</span></strong>
                                                            </td>
                                                            @else
                                                            <td class="text-right">
                                                                <strong><span>{{ single_price($order->grand_total) }}</span></strong>
                                                            </td>
                                                            @endif
                                                        @endif
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endsection
