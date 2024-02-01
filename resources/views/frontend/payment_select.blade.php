@extends('frontend.layouts.app')

@section('content')
<section class="mb-4 pt-5">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col done">
                        <div class="text-success text-center">
                            <i class="la-3x las la-shopping-cart mb-2"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart') }}</h3>
                        </div>
                    </div>
                    <div class="col done">
                        <div class="text-success text-center">
                            <i class="la-3x las la-map mb-2"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('2. Shipping info') }}</h3>
                        </div>
                    </div>
                    <div class="col done">
                        <div class="text-success text-center">
                            <i class="la-3x las la-truck mb-2"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('3. Delivery info') }}</h3>
                        </div>
                    </div>
                    <div class="col active">
                        <div class="text-primary text-center">
                            <i class="la-3x las la-credit-card mb-2"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('4. Payment') }}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x las la-check-circle mb-2 opacity-50"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('5. Confirmation') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="mb-4">
    <div class="container text-left">
        <div class="row">
            <div class="col-lg-8">
                <form action="{{ route('payment.checkout') }}" class="form-default" role="form" method="POST" id="checkout-form">
                    @csrf
                    <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">


                    <div class="card rounded border-0 shadow-sm">
                        <div class="card-header p-3">
                            <h3 class="fs-16 fw-600 mb-0">
                                {{ translate('Any additional info?') }}
                            </h3>
                        </div>
                        <div class="form-group px-3 pt-3">
                            <textarea name="additional_info" rows="5" class="form-control" placeholder="{{ translate('Type your text') }}"></textarea>
                        </div>

                        <div class="card-header p-3">
                            <h3 class="fs-16 fw-600 mb-0">
                                {{ translate('Select a payment option') }}
                            </h3>
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-xxl-8 col-xl-10 mx-auto">
                                    <div class="row gutters-10">
                                        
                                        @if (get_setting('qvapay') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="qvapay" class="online_payment" type="radio" name="payment_option" checked 
                                                onchange="update_payment(this, 'mlc')"
                                                data-target="qvapay">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ asset('assets/img/cards/qvapay.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('QvaPay') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif

                                        @if (get_setting('cup_payment') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="cup_payment" class="online_payment" type="radio" name="payment_option" 
                                                onchange="update_payment(this, 'cup')"
                                                data-target="cup_payment">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ asset('assets/img/cards/cup.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">CUP</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif

                                        @if (get_setting('mlc_payment') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="mlc_payment" class="online_payment" type="radio" name="payment_option" 
                                                onchange="update_payment(this, 'mlc')"
                                                data-target="mlc_payment">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ asset('assets/img/cards/mlc.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">MLC</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif

                                        @if (get_setting('bitcoinln') == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="bitcoinln" class="online_payment" type="radio" name="payment_option">
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ asset('assets/img/cards/bitcoinln.jpg') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Bitcoin ⚡️') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif

                                        @if (get_setting('cash_payment') == 1)
                                            @php
                                                $digital = 0;
                                                $cod_on = 1;
                                                foreach ($carts as $cartItem) {
                                                    $product = \App\Models\Product::find($cartItem['product_id']);
                                                    if ($product['digital'] == 1) {
                                                        $digital = 1;
                                                    }
                                                    if ($product['cash_on_delivery'] == 0) {
                                                        $cod_on = 0;
                                                    }
                                                }
                                            @endphp
                                        @if ($digital != 1 && $cod_on == 1)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="cash_on_delivery" class="online_payment" type="radio" name="payment_option" checked>
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ asset('assets/img/cards/cod.png') }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ translate('Cash on Delivery') }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endif
                                        @endif
                                        @if (Auth::check())
                                        @if (addon_is_activated('offline_payment'))
                                        @foreach (\App\Models\ManualPaymentMethod::all() as $method)
                                        <div class="col-6 col-md-4">
                                            <label class="aiz-megabox d-block mb-3">
                                                <input value="{{ $method->heading }}" type="radio" name="payment_option" class="offline_payment_option" onchange="toggleManualPaymentData('{{ $method->id }}')" data-id="{{ $method->id }}" checked>
                                                <span class="d-block aiz-megabox-elem p-3">
                                                    <img src="{{ uploaded_asset($method->photo) }}" class="img-fluid mb-2">
                                                    <span class="d-block text-center">
                                                        <span class="d-block fw-600 fs-15">{{ $method->heading }}</span>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                        @endforeach

                                        @foreach (\App\Models\ManualPaymentMethod::all() as $method)
                                        <div id="manual_payment_info_{{ $method->id }}" class="d-none">
                                            @php echo $method->description @endphp
                                            @if ($method->bank_info != null)
                                            <ul>
                                                @foreach (json_decode($method->bank_info) as $key => $info)
                                                <li>{{ translate('Bank Name') }} -
                                                    {{ $info->bank_name }},
                                                    {{ translate('Account Name') }} -
                                                    {{ $info->account_name }},
                                                    {{ translate('Account Number') }} -
                                                    {{ $info->account_number }},
                                                    {{ translate('Routing Number') }} -
                                                    {{ $info->routing_number }}
                                                </li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </div>
                                        @endforeach
                                        @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if (addon_is_activated('offline_payment'))
                            <div class="d-none mb-3 rounded border bg-white p-3 text-left">
                                <div id="manual_payment_description">

                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-3">
                                        <label>{{ translate('Transaction ID')}} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control mb-3" name="trx_id" id="trx_id" placeholder="{{ translate('Transaction ID') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Photo') }}</label>
                                    <div class="col-md-9">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose image') }}</div>
                                            <input type="hidden" name="photo" class="selected-files">
                                        </div>
                                        <div class="file-preview box sm">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if (Auth::check() && get_setting('wallet_system') == 1)
                            <div class="separator mb-3">
                                <span class="bg-white px-3">
                                    <span class="opacity-60">{{ translate('Or') }}</span>
                                </span>
                            </div>
                            <div class="py-4 text-center">
                                <div class="h6 mb-3">
                                    <span class="opacity-80">{{ translate('Your wallet balance :') }}</span>
                                    <span class="fw-600">{{ single_price(Auth::user()->balance) }}</span>
                                </div>
                                @if (Auth::user()->balance < $total) <button type="button" class="btn btn-secondary" disabled>
                                    {{ translate('Insufficient balance') }}
                                    </button>
                                    @else
                                    <button type="button" onclick="use_wallet()" class="btn btn-primary fw-600">
                                        {{ translate('Pay with wallet') }}
                                    </button>
                                    @endif
                            </div>
                            @endif
                            
                            <span class="fw-600 d-none cup_mlc_payment_note" id="cup_mlc_payment_note">Al completar la orden se le proporcionará un enlace al Whatsapp de nuestro soporte para que realice el pago.</span> 
                    </tr>
                        </div>

                    </div>
                    <div class="pt-3">
                        <label class="aiz-checkbox">
                            <input type="checkbox" required id="agree_checkbox">
                            <span class="aiz-square-check"></span>
                            <span>{{ translate('I agree to the') }}</span>
                        </label>
                        <a href="{{ route('terms') }}">{{ translate('terms and conditions') }}</a>,
                        <a href="{{ route('returnpolicy') }}">{{ translate('return policy') }}</a> &
                        <a href="{{ route('privacypolicy') }}">{{ translate('privacy policy') }}</a>
                    </div>

                    <div class="row align-items-center pt-3">
                        <div class="col-6">
                            <a href="{{ route('checkout.shipping_info') }}" class="link link--style-3">
                                <i class="las la-arrow-left"></i>
                                Retornar a Información de envío
                            </a>
                        </div>
                        <div class="col-6 text-right">
                            <button type="button" onclick="submitOrder(this)" class="btn btn-primary fw-600">{{ translate('Complete Order') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4 mt-lg-0 mt-4" id="cart_summary">
                @include('frontend.partials.cart_summary')
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script type="text/javascript">
    function update_payment(el, type) {
        var value = $(el).val();

        $('.cup_mlc_payment_note, .qvapay, .cup_payment, .mlc_payment, .total_price_product, .total_price_product_cup, .total_price_product_mlc, .subtotal, .subtotal_cup, .subtotal_mlc, .shipping, .shipping_cup, .shipping_mlc').removeClass('d-block d-none');

        if (value == "qvapay") {
            $('#cup_mlc_payment_note, #cup_payment, #subtotal_cup, #shipping_cup, #mlc_payment, #subtotal_mlc, #shipping_mlc, #total_price_product_cup, #total_price_product_mlc').addClass('d-none');
        } else if (value == 'cup_payment') {
            $('#qvapay, #subtotal, #shipping, #total_price_product, #total_price_product_mlc, #mlc_payment, #subtotal_mlc, #shipping_mlc').addClass('d-none');
        } else if (value == 'mlc_payment') {
            $('#qvapay, #subtotal, #shipping, #total_price_product, #total_price_product_cup, #cup_payment, #subtotal_cup, #shipping_cup').addClass('d-none');
        }
    }



    $(document).ready(function() {
        $(".online_payment").click(function() {
            $('#manual_payment_description').parent().addClass('d-none');
        });
        toggleManualPaymentData($('input[name=payment_option]:checked').data('id'));
    });

    var minimum_order_amount_check = "{{ get_setting('minimum_order_amount_check') == 1 ? 1 : 0 }}";
    var minimum_order_amount = "{{ get_setting('minimum_order_amount_check') == 1 ? get_setting('minimum_order_amount') : 0 }}";

    function use_wallet() {
        $('input[name=payment_option]').val('wallet');
        if ($('#agree_checkbox').is(":checked")) {
            ;
            if (minimum_order_amount_check && $('#sub_total').val() < minimum_order_amount) {
                AIZ.plugins.notify('danger', "{{ translate('You order amount is less then the minimum order amount') }}");
            } else {
                $('#checkout-form').submit();
            }
        } else {
            AIZ.plugins.notify('danger', "{{ translate('You need to agree with our policies') }}");
        }
    }

    function submitOrder(el) {
        $(el).prop('disabled', true);
        if ($('#agree_checkbox').is(":checked")) {
            if (minimum_order_amount_check && $('#sub_total').val() < minimum_order_amount) {
                AIZ.plugins.notify('danger', "{{ translate('You order amount is less than the minimum order amount') }}");
            } else {
                var offline_payment_active = "{{ addon_is_activated('offline_payment') }}";
                if (offline_payment_active == 'true' && $('.offline_payment_option').is(":checked") && $('#trx_id').val() == '') {
                    AIZ.plugins.notify('danger', "{{ translate('You need to put Transaction id') }}");
                    $(el).prop('disabled', false);
                } else {
                    $('#checkout-form').submit();
                }
            }
        } else {
            AIZ.plugins.notify('danger', "{{ translate('You need to agree with our policies') }}");
            $(el).prop('disabled', false);
        }
    }

    function toggleManualPaymentData(id) {
        if (typeof id != 'undefined') {
            $('#manual_payment_description').parent().removeClass('d-none');
            $('#manual_payment_description').html($('#manual_payment_info_' + id).html());
        }
    }

    $(document).on("click", "#coupon-apply", function() {
        var data = new FormData($('#apply-coupon-form')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "POST",
            url: "{{ route('checkout.apply_coupon_code') }}",
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data, textStatus, jqXHR) {
                AIZ.plugins.notify(data.response_message.response, data.response_message.message);
                $("#cart_summary").html(data.html);
            }
        })
    });

    $(document).on("click", "#coupon-remove", function() {
        var data = new FormData($('#remove-coupon-form')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: "POST",
            url: "{{ route('checkout.remove_coupon_code') }}",
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data, textStatus, jqXHR) {
                $("#cart_summary").html(data);
            }
        })
    })
</script>
@endsection