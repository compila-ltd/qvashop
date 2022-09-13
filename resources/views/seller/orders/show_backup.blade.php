<div class="modal-header">
    <h5 class="modal-title strong-600 heading-5">{{ translate('Order id') }}: {{ $order->code }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

@php
$status = $order->delivery_status;
$payment_status = $order->orderDetails->where('seller_id', Auth::user()->id)->first()->payment_status;
@endphp

<div class="modal-body gry-bg px-3 pt-0">
    @if (get_setting('product_manage_by_admin') == 0)
        <div class="row mt-5">
            @if ($order->payment_type == 'cash_on_delivery')
                <div class="offset-lg-2 col-lg-4 col-sm-6">
                    <div class="form-group">
                        <select class="form-control aiz-selectpicker form-control-sm"
                            data-minimum-results-for-search="Infinity" id="update_payment_status">
                            <option value="unpaid" @if ($payment_status == 'unpaid') selected @endif>
                                {{ translate('Unpaid') }}</option>
                            <option value="paid" @if ($payment_status == 'paid') selected @endif>
                                {{ translate('Paid') }}</option>
                        </select>
                        <label>{{ translate('Payment Status') }}</label>
                    </div>
                </div>
            @endif
            <div class="col-lg-4 col-sm-6">
                <div class="form-group">
                    <select class="form-control aiz-selectpicker form-control-sm"
                        data-minimum-results-for-search="Infinity" id="update_delivery_status">
                        <option value="pending" @if ($status == 'pending') selected @endif>
                            {{ translate('Pending') }}</option>
                        <option value="confirmed" @if ($status == 'confirmed') selected @endif>
                            {{ translate('Confirmed') }}</option>
                        <option value="picked_up" @if ($status == 'picked_up') selected @endif>
                            {{ translate('Picked Up') }}</option>
                        <option value="on_the_way" @if ($status == 'on_the_way') selected @endif>
                            {{ translate('On The Way') }}</option>
                        <option value="delivered" @if ($status == 'delivered') selected @endif>
                            {{ translate('Delivered') }}</option>
                        <option value="cancelled" @if ($status == 'cancelled') selected @endif>
                            {{ translate('Cancel') }}</option>
                    </select>
                    <label>{{ translate('Delivery Status') }}</label>
                </div>
            </div>
        </div>
    @endif

    <div class="card mt-4">
        <div class="card-header">
            <b class="fs-15">{{ translate('Order Summary') }}</b>
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
                            <td>{{ json_decode($order->shipping_address)->name }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Email') }}:</td>
                            @if ($order->user_id != null)
                                <td>{{ $order->user->email }}</td>
                            @endif
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Shipping address') }}:</td>
                            <td>{{ json_decode($order->shipping_address)->address }},
                                {{ json_decode($order->shipping_address)->city }},
                                {{ json_decode($order->shipping_address)->postal_code }},
                                {{ json_decode($order->shipping_address)->country }}</td>
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
                            <td>{{ translate($status) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Total order amount') }}:</td>
                            <td>{{ single_price($order->grand_total) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Contact') }}:</td>
                            <td>{{ json_decode($order->shipping_address)->phone }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment method') }}:</td>
                            <td>{{ translate(ucfirst(str_replace('_', ' ', $order->payment_type))) }}</td>
                        </tr>

                        <tr>
                            <td class="text-main text-bold">{{ translate('Additional Info') }}</td>
                            <td class="text-right">{{ $order->additional_info }}</td>
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
    </div>

    <div class="row">
        <div class="col-lg-9">
            <div class="card mt-4">
                <div class="card-header">
                    <b class="fs-15">{{ translate('Order Details') }}</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table-borderless table-responsive table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th width="40%">{{ translate('Product') }}</th>
                                <th>{{ translate('Variation') }}</th>
                                <th>{{ translate('Quantity') }}</th>
                                <th>{{ translate('Delivery Type') }}</th>
                                <th>{{ translate('Price') }}</th>
                                @if (addon_is_activated('refund_request'))
                                    <th>{{ translate('Refund') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        @if ($orderDetail->product != null)
                                            <a href="{{ route('product', $orderDetail->product->slug) }}"
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
                                                {{ $order->pickup_point->getTranslation('name') }}
                                                ({{ translate('Pickip Point') }})
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ $orderDetail->price }}</td>
                                    @if (addon_is_activated('refund_request'))
                                        <td>
                                            @if ($orderDetail->refund_request != null && $orderDetail->refund_request->refund_status == 0)
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
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card mt-4">
                <div class="card-header">
                    <b class="fs-15">{{ translate('Order Ammount') }}</b>
                </div>
                <div class="card-body pb-0">
                    <table class="table-borderless table">
                        <tbody>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Subtotal') }}</th>
                                <td class="text-right">
                                    <span
                                        class="strong-600">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('price')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Shipping') }}</th>
                                <td class="text-right">
                                    <span
                                        class="text-italic">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('shipping_cost')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Tax') }}</th>
                                <td class="text-right">
                                    <span
                                        class="text-italic">{{ single_price($order->orderDetails->where('seller_id', Auth::user()->id)->sum('tax')) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Coupon') }}</th>
                                <td class="text-right">
                                    <span class="text-italic">{{ single_price($order->coupon_discount) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="w-50 fw-600">{{ translate('Total') }}</th>
                                <td class="text-right">
                                    <strong>
                                        <span>{{ single_price($order->grand_total) }}
                                        </span>
                                    </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#update_delivery_status').on('change', function() {
        var order_id = {{ $order->id }};
        var status = $('#update_delivery_status').val();
        $.post('{{ route('orders.update_delivery_status') }}', {
            _token: '{{ @csrf_token() }}',
            order_id: order_id,
            status: status
        }, function(data) {
            $('#order_details').modal('hide');
            AIZ.plugins.notify('success', '{{ translate('Order status has been updated') }}');
            location.reload().setTimeOut(500);
        });
    });

    $('#update_payment_status').on('change', function() {
        var order_id = {{ $order->id }};
        var status = $('#update_payment_status').val();
        $.post('{{ route('orders.update_payment_status') }}', {
            _token: '{{ @csrf_token() }}',
            order_id: order_id,
            status: status
        }, function(data) {
            $('#order_details').modal('hide');
            //console.log(data);
            AIZ.plugins.notify('success', '{{ translate('Payment status has been updated') }}');
            location.reload().setTimeOut(500);
        });
    });
</script>
