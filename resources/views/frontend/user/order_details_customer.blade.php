@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Order id') }}: {{ $order->code }}</h5>
        </div>
        
        <div class="card-header">
            <h5 class="h6 mb-0">{{ translate('Order Summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table-borderless table">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Code') }}:</td>
                            <td>{{ $order->code }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Customer') }}:</td>
                            @if(!empty(json_decode($order->shipping_address)))
                                <td>{{ json_decode($order->shipping_address)->name }}</td>
                            @else
                                <td>{{auth()->user()->name}}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Email') }}:</td>
                            @if ($order->user_id != null)
                                <td>{{ $order->user->email }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Shipping address') }}:</td>
                            @if(!empty(json_decode($order->shipping_address)))
                                <td>
                                    {{ json_decode($order->shipping_address)->address }},
                                    {{ json_decode($order->shipping_address)->city }},
                                    {{ json_decode($order->shipping_address)->postal_code }},
                                    {{ json_decode($order->shipping_address)->country }}
                                </td>
                            @else
                                <td>-</td>
                            @endif
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table-borderless table">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order date') }}:</td>
                            <td>{{ date('d-m-Y H:i A', $order->date) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order status') }}:</td>
                            @if ($order->payment_status != 'unpaid')
                                @if ($order->delivery_status == 'delivered')
                                    <td><span class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span></td>
                                @elseif ($order->delivery_status == 'pending')
                                    <td><span class="badge badge-inline badge-danger">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span></td>
                                @elseif ($order->delivery_status == 'in_progress')
                                    <td><span class="badge badge-inline badge-warning">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span></td>
                                @elseif (($order->delivery_status == 'picked_up') || ($order->delivery_status == 'on_the_way'))
                                    <td><span class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span></td>
                                @else
                                    <td><span class="badge badge-inline badge-secondary">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span></td>
                                @endif
                            @else
                                <td><span class="badge badge-inline badge-dark">{{ translate($order->payment_status) }}</span></td>
                            @endif
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total order amount') }}:</td>
                            <td>
                                {{ format_price(($order->orderDetails->sum('price') + $order->orderDetails->sum('tax') + $order->orderDetails->max('shipping_cost')) * $order->exchange_rate)}}                          
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Shipping method') }}:</td>
                            <td>{{ translate('Flat shipping rate') }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment method') }}:</td>
                            <td>
                                {{ $order->payment_type }}
                            </td>
                        </tr>

                        <tr>
                            <td class="text-main text-bold">{{ translate('Additional Info') }}</td>
                            <td class="">{{ $order->additional_info }}</td>
                        </tr>
                        @if ($order->tracking_code)
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Tracking code') }}:</td>
                                <td>{{ $order->tracking_code }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="h6 mb-0">{{ translate('Order Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="aiz-table table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th width="30%">{{ translate('Product') }}</th>
                                    <th data-breakpoints="md">{{ translate('Variation') }}</th>
                                    <th>{{ translate('Quantity') }}</th>
                                    <th data-breakpoints="md">{{ translate('Delivery Type') }}</th>
                                    <th>{{ translate('Price') }}</th>
                                    @if (addon_is_activated('refund_request'))
                                        <th data-breakpoints="md">{{ translate('Refund') }}</th>
                                    @endif
                                    <th data-breakpoints="md" class="text-right">{{ translate('Review') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderDetails as $key => $orderDetail)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            @if ($orderDetail->product != null && $orderDetail->product->auction_product == 0)
                                                <a href="{{ route('product', $orderDetail->product->slug) }}"
                                                    target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
                                            @elseif($orderDetail->product != null && $orderDetail->product->auction_product == 1)
                                                <a href="{{ route('auction-product', $orderDetail->product->slug) }}"
                                                    target="_blank">{{ $orderDetail->product->getTranslation('name') }}</a>
                                            @else
                                                <strong>{{ translate('Product Unavailable') }}</strong>
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
                                                {{ translate('Home Delivery') }}
                                            @elseif ($order->shipping_type == 'pickup_point')
                                                @if ($order->pickup_point != null)
                                                    {{ $order->pickup_point->name }} ({{ translate('Pickip Point') }})
                                                @else
                                                    {{ translate('Pickup Point') }}
                                                @endif
                                            @elseif($order->shipping_type == 'carrier')
                                                @if ($order->carrier != null)
                                                    {{ $order->carrier->name }} ({{ translate('Carrier') }})
                                                    <br>
                                                    {{ translate('Transit Time').' - '.$order->carrier->transit_time.' '.translate('days') }}
                                                @else
                                                    {{ translate('Carrier') }}
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            {{ format_price($orderDetail->price * $order->exchange_rate) }}
                                        </td>
                                        @if (addon_is_activated('refund_request'))
                                            @php
                                                $no_of_max_day = get_setting('refund_request_time');
                                                $last_refund_date = $orderDetail->created_at->addDays($no_of_max_day);
                                                $today_date = Carbon\Carbon::now();
                                            @endphp
                                            <td>
                                                @if ($orderDetail->product != null && $orderDetail->product->refundable != 0 && $orderDetail->refund_request == null && $today_date <= $last_refund_date && $orderDetail->payment_status == 'paid' && $orderDetail->delivery_status == 'delivered')
                                                    <a href="{{ route('refund_request_send_page', $orderDetail->id) }}"
                                                        class="btn btn-primary btn-sm">{{ translate('Send') }}</a>
                                                @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 0)
                                                    <b class="text-info">{{ translate('Pending') }}</b>
                                                @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 2)
                                                    <b class="text-success">{{ translate('Rejected') }}</b>
                                                @elseif ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 1)
                                                    <b class="text-success">{{ translate('Approved') }}</b>
                                                @elseif ($orderDetail->product->refundable != 0)
                                                    <b>{{ translate('N/A') }}</b>
                                                @else
                                                    <b>{{ translate('Non-refundable') }}</b>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="text-right">
                                            @php
                                                $product_now = DB::table('products')
                                                ->where('id', $orderDetail->product_id)
                                                ->first();
                                                
                                                $review_now = DB::table('reviews')
                                                ->where('user_id', Auth::user()->id)
                                                ->where('product_id', $product_now->id)
                                                ->first();
                                            @endphp

                                            @if ($orderDetail->delivery_status == 'delivered')
                                                @if($review_now == null)
                                                    <a href="javascript:void(0);"
                                                        onclick="product_review('{{ $orderDetail->product_id }}')"
                                                        class="btn btn-primary btn-sm"> {{ translate('Write a review') }} </a>
                                                @else
                                                    <a href="javascript:void(0);"
                                                        onclick="product_review('{{ $orderDetail->product_id }}')"
                                                        class="btn btn-success btn-sm"> {{ translate('My review') }} </a>
                                                @endif
                                            @elseif ($orderDetail->payment_status != 'unpaid')
                                                @if ($order->delivery_status == 'pending')
                                                    <span class="badge badge-inline badge-danger">{{ translate(ucfirst(str_replace('_', ' ', $orderDetail->delivery_status))) }}</span>
                                                @elseif ($order->delivery_status == 'in_progress')
                                                    <span class="badge badge-inline badge-warning">{{ translate(ucfirst(str_replace('_', ' ', $orderDetail->delivery_status))) }}</span>
                                                @elseif (($order->delivery_status == 'picked_up') || ($order->delivery_status == 'on_the_way'))
                                                    <span class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $orderDetail->delivery_status))) }}</span>
                                                @elseif ($order->delivery_status == 'cancelled')
                                                    <span class="badge badge-inline badge-secondary">{{ translate(ucfirst(str_replace('_', ' ', $orderDetail->delivery_status))) }}</span>
                                                @endif
                                            @else
                                                <span class="badge badge-inline badge-dark">{{ translate($orderDetail->payment_status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <b class="fs-15">{{ translate('Order Ammount') }}</b>
                    </div>
                    <div class="card-body pb-0">
                        <table class="table-borderless table">
                            <tbody>
                                <tr>
                                    <td class="w-50 fw-600">{{ translate('Subtotal') }}</td>
                                    <td class="text-right">
                                        <span class="strong-600">
                                            {{ format_price($order->orderDetails->sum('price') * $order->exchange_rate) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="w-50 fw-600">{{ translate('Shipping') }}</td>
                                    <td class="text-right">
                                        <span class="text-italic">
                                            {{ format_price($order->orderDetails->max('shipping_cost') * $order->exchange_rate) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="w-50 fw-600">{{ translate('Tax') }}</td>
                                    <td class="text-right">
                                        <span
                                            class="text-italic">{{ format_price($order->orderDetails->sum('tax') * $order->exchange_rate) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="w-50 fw-600">{{ translate('Coupon') }}</td>
                                    <td class="text-right">
                                        <span class="text-italic">
                                            {{ format_price($order->coupon_discount * $order->exchange_rate) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="w-50 fw-600">{{ translate('Total') }}</td>
                                    <td class="text-right">
                                        <strong><span>
                                            {{ format_price($order->grand_total * $order->exchange_rate) }}
                                        </span></strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($order->manual_payment && $order->manual_payment_data == null)
                    <button onclick="show_make_payment_modal({{ $order->id }})"
                        class="btn btn-block btn-primary">{{ translate('Make Payment') }}</button>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <!-- Product Review Modal -->
    <div class="modal fade" id="product-review-modal">
        <div class="modal-dialog">
            <div class="modal-content" id="product-review-modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div id="payment_modal_body">

                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        function show_make_payment_modal(order_id) {
            $.post('{{ route('checkout.make_payment') }}', {
                _token: '{{ csrf_token() }}',
                order_id: order_id
            }, function(data) {
                $('#payment_modal_body').html(data);
                $('#payment_modal').modal('show');
                $('input[name=order_id]').val(order_id);
            });
        }

        function product_review(product_id) {
            $.post('{{ route('product_review_modal') }}', {
                _token: '{{ @csrf_token() }}',
                product_id: product_id
            }, function(data) {
                $('#product-review-modal-content').html(data);
                $('#product-review-modal').modal('show', {
                    backdrop: 'static'
                });
                AIZ.extra.inputRating();
            });
        }
    </script>
@endsection
