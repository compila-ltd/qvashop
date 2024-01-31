<div class="card rounded border-0 shadow-sm">
    <div class="card-header">
        <h3 class="fs-16 fw-600 mb-0">{{ translate('Summary') }}</h3>
        <div class="text-right">
            <span class="badge badge-inline badge-primary">
                {{ count($carts) }}
                {{ translate('Items') }}
            </span>
            @php
                $coupon_discount = 0;
            @endphp
            @if (Auth::check() && get_setting('coupon_system') == 1)
                @php
                    $coupon_code = null;
                @endphp

                @foreach ($carts as $key => $cartItem)
                    @php
                        $product = \App\Models\Product::find($cartItem['product_id']);
                    @endphp
                    @if ($cartItem->coupon_applied == 1)
                        @php
                            $coupon_code = $cartItem->coupon_code;
                            break;
                        @endphp
                    @endif
                @endforeach

                @php
                    $coupon_discount = carts_coupon_discount($coupon_code);
                @endphp
            @endif

            @php $subtotal_for_min_order_amount = 0; @endphp
            @foreach ($carts as $key => $cartItem)
                @php $subtotal_for_min_order_amount += cart_product_price($cartItem, $cartItem->product, false, false) * $cartItem['quantity']; @endphp
            @endforeach

            @if (get_setting('minimum_order_amount_check') == 1 && $subtotal_for_min_order_amount < get_setting('minimum_order_amount'))
                <span class="badge badge-inline badge-primary">
                    {{ translate('Minimum Order Amount') . ' ' . single_price(get_setting('minimum_order_amount')) }}
                </span>
            @endif
        </div>
    </div>

    @php
        $currencies = \App\Models\Currency::where('status', 1)->get();
        //dd($currencies);

        $cup_exchange_rate = -1;
        $mlc_exchange_rate = -1;

        $currency = $currencies->where('code', 'MLC')->first();

        if ($currency) 
            $mlc_exchange_rate = $currency->exchange_rate;

        $currency = $currencies->where('code', 'CUP')->first();

        if ($currency) 
            $cup_exchange_rate = $currency->exchange_rate;
                        
        //dd($cup_exchange_rate);

    @endphp    

    <div class="card-body">
        @if (addon_is_activated('club_point'))
            @php
                $total_point = 0;
            @endphp
            @foreach ($carts as $key => $cartItem)
                @php
                    $product = \App\Models\Product::find($cartItem['product_id']);
                    $total_point += $product->earn_point * $cartItem['quantity'];
                @endphp
            @endforeach

            <div class="bg-soft-primary border-soft-primary mb-2 rounded border px-2">
                {{ translate('Total Club point') }}:
                <span class="fw-700 float-right">{{ $total_point }}</span>
            </div>
        @endif
        <table class="table">
            <thead>
                <tr>
                    <th class="product-name">{{ translate('Product') }}</th>
                    <th class="product-total text-right">{{ translate('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $subtotal = 0;
                    $tax = 0;
                    $shipping = 0;
                    $product_shipping_cost = 0;
                    $shipping_region = $shipping_info['city'];
                @endphp
                @foreach ($carts as $key => $cartItem)
                    @php
                        $product = \App\Models\Product::find($cartItem['product_id']);
                        $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                        $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                        $product_shipping_cost = $cartItem['shipping_cost'];
                        
                        $shipping += $product_shipping_cost;
                        
                        $product_name_with_choice = $product->getTranslation('name');
                        if ($cartItem['variant'] != null) {
                            $product_name_with_choice = $product->getTranslation('name') . ' - ' . $cartItem['variant'];
                        }
                    @endphp
                    <tr class="cart_item">
                        <td class="product-name">
                            {{ $product_name_with_choice }}
                            <strong class="product-quantity">
                                × {{ $cartItem['quantity'] }}
                            </strong>
                        </td>
                        <td class="product-total text-right total_price_product" id="total_price_product">
                            <span
                                class="pl-4 pr-0">{{ single_price(cart_product_price($cartItem, $cartItem->product, false, false) * $cartItem['quantity']) }}</span>
                        </td>
                        <td class="product-total text-right total_price_product_cup d-none" id="total_price_product_cup">
                            <span
                                class="pl-4 pr-0">{{ single_price((cart_product_price($cartItem, $cartItem->product, false, false) * $cartItem['quantity']) * $cup_exchange_rate ) }}</span>
                        </td>
                        <td class="product-total text-right total_price_product_mlc d-none" id="total_price_product_mlc">
                            <span
                                class="pl-4 pr-0">{{ single_price((cart_product_price($cartItem, $cartItem->product, false, false) * $cartItem['quantity']) * $mlc_exchange_rate) }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <input type="hidden" id="sub_total" value="{{ $subtotal }}">
        <table class="table">

            <tfoot>
                <tr class="cart-subtotal subtotal" id="subtotal">
                    <th>{{ translate('Subtotal') }} USD</th>
                    <td class="text-right">
                        <span class="fw-600">{{ single_price($subtotal) }}</span>
                    </td>
                </tr>
                <tr class="cart-subtotal subtotal_cup d-none" id="subtotal_cup">
                    <th>{{ translate('Subtotal') }} CUP</th>
                    <td class="text-right">
                        <span class="fw-600">{{ single_price($subtotal * $cup_exchange_rate) }}</span>
                    </td>
                </tr>
                <tr class="cart-subtotal subtotal_mlc d-none" id="subtotal_mlc">
                    <th>{{ translate('Subtotal') }} MLC</th>
                    <td class="text-right">
                        <span class="fw-600">{{ single_price($subtotal * $mlc_exchange_rate) }}</span>
                    </td>
                </tr>                

                <tr class="cart-shipping">
                    <th>{{ translate('Tax') }}</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($tax) }}</span>
                    </td>
                </tr>

                <tr class="cart-shipping shipping" id="shipping">
                    <th>{{ translate('Total Shipping') }} USD</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($shipping) }}</span>
                    </td>
                </tr>
                <tr class="cart-shipping shipping_cup d-none" id="shipping_cup">
                    <th>{{ translate('Total Shipping') }} CUP</th>
                    <td class="text-right">  
                        <span class="font-italic">{{ single_price($shipping * $cup_exchange_rate) }}</span>
                    </td>
                </tr>
                <tr class="cart-shipping shipping_mlc d-none" id="shipping_mlc">
                    <th>{{ translate('Total Shipping') }} MLC</th>
                    <td class="text-right">
                        <span class="font-italic">{{ single_price($shipping * $mlc_exchange_rate) }}</span>
                    </td>
                </tr>

                @if (Session::has('club_point'))
                    <tr class="cart-shipping">
                        <th>{{ translate('Redeem point') }}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price(Session::get('club_point')) }}</span>
                        </td>
                    </tr>
                @endif

                @if ($coupon_discount > 0)
                    <tr class="cart-shipping">
                        <th>{{ translate('Coupon Discount') }}</th>
                        <td class="text-right">
                            <span class="font-italic">{{ single_price($coupon_discount) }}</span>
                        </td>
                    </tr>
                @endif

                @php
                    $total = $subtotal + $tax + $shipping;
                    if (Session::has('club_point')) {
                        $total -= Session::get('club_point');
                    }
                    if ($coupon_discount > 0) {
                        $total -= $coupon_discount;
                    }
                @endphp

                <tr class="cart-total qvapay" id="qvapay">
                    <th><span class="strong-600">{{ translate('Total') }} USD</span></th>
                    <td class="text-right">
                        <strong><span>{{ single_price($total) }}</span></strong>
                    </td>
                </tr>
                
                @if (get_setting('cup_payment') == 1)
                    <tr class="cart-total cup_payment d-none" id="cup_payment">
                        <th><span class="strong-600">{{ translate('Total') }} CUP</span></th>
                        <td class="text-right">
                            <strong><span>{{ single_price($total * $cup_exchange_rate) }} </span></strong>
                        </td>
                    </tr>
                @endif

                @if (get_setting('mlc_payment') == 1)
                    <tr class="cart-total mlc_payment d-none" id="mlc_payment">
                        <th><span class="strong-600">{{ translate('Total') }} MLC</span></th>
                        <td class="text-right">
                            <strong><span>{{ single_price($total * $mlc_exchange_rate) }}</span></strong>
                        </td>
                    </tr>
                @endif
            </tfoot>
        </table>

        @if (addon_is_activated('club_point'))
            @if (Session::has('club_point'))
                <div class="mt-3">
                    <form class="" action="{{ route('checkout.remove_club_point') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <div class="form-control">{{ Session::get('club_point') }}</div>
                            <div class="input-group-append">
                                <button type="submit"
                                    class="btn btn-primary">{{ translate('Remove Redeem Point') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        @endif

        @if (Auth::check() && get_setting('coupon_system') == 1)
            @if ($coupon_discount > 0 && $coupon_code)
                <div class="mt-3">
                    <form class="" id="remove-coupon-form" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <div class="form-control">{{ $coupon_code }}</div>
                            <div class="input-group-append">
                                <button type="button" id="coupon-remove"
                                    class="btn btn-primary">{{ translate('Change Coupon') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="mt-3">
                    <form class="" id="apply-coupon-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">
                        <div class="input-group">
                            <input type="text" class="form-control" name="code"
                                onkeydown="return event.key != 'Enter';"
                                placeholder="{{ translate('Have coupon code? Enter here') }}" required>
                            <div class="input-group-append">
                                <button type="button" id="coupon-apply"
                                    class="btn btn-primary">{{ translate('Apply') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
        @endif

    </div>
</div>
