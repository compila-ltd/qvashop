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
                    <form action="{{ route('payment.checkout') }}" class="form-default" role="form" method="POST"
                        id="checkout-form">
                        @csrf
                        <input type="hidden" name="owner_id" value="{{ $carts[0]['owner_id'] }}">


                        <div class="card rounded border-0 shadow-sm">
                            <div class="card-header p-3">
                                <h3 class="fs-16 fw-600 mb-0">
                                    {{ translate('Any additional info?') }}
                                </h3>
                            </div>
                            <div class="form-group px-3 pt-3">
                                <textarea name="additional_info" rows="5" class="form-control"
                                    placeholder="{{ translate('Type your text') }}"></textarea>
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
                                            @if (get_setting('paypal_payment') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="paypal" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/paypal.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Paypal') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('stripe_payment') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="stripe" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/stripe.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Stripe') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('mercadopago_payment') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="mercadopago" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/mercadopago.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Mercadopago') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('sslcommerz_payment') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="sslcommerz" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/sslcommerz.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('sslcommerz') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('instamojo_payment') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="instamojo" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/instamojo.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Instamojo') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('razorpay') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="razorpay" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/rozarpay.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Razorpay') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('paystack') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="paystack" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/paystack.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Paystack') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('voguepay') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="voguepay" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/vogue.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('VoguePay') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('payhere') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="payhere" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/payhere.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('payhere') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('ngenius') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="ngenius" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/ngenius.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('ngenius') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('iyzico') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="iyzico" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/iyzico.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Iyzico') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('nagad') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="nagad" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/nagad.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Nagad') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('bkash') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="bkash" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/bkash.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Bkash') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('aamarpay') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="aamarpay" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/aamarpay.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Aamarpay') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('authorizenet') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="authorizenet" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/authorizenet.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Authorize Net') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (get_setting('payku') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="payku" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/payku.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Payku') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (addon_is_activated('african_pg'))
                                                @if (get_setting('mpesa') == 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="mpesa" class="online_payment" type="radio"
                                                                name="payment_option" checked>
                                                            <span class="d-block aiz-megabox-elem p-3">
                                                                <img src="{{ static_asset('assets/img/cards/mpesa.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('mpesa') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if (get_setting('flutterwave') == 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="flutterwave" class="online_payment" type="radio"
                                                                name="payment_option" checked>
                                                            <span class="d-block aiz-megabox-elem p-3">
                                                                <img src="{{ static_asset('assets/img/cards/flutterwave.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('flutterwave') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                                @if (get_setting('payfast') == 1)
                                                    <div class="col-6 col-md-4">
                                                        <label class="aiz-megabox d-block mb-3">
                                                            <input value="payfast" class="online_payment" type="radio"
                                                                name="payment_option" checked>
                                                            <span class="d-block aiz-megabox-elem p-3">
                                                                <img src="{{ static_asset('assets/img/cards/payfast.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('payfast') }}</span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                            @endif
                                            @if (addon_is_activated('paytm') && get_setting('paytm_payment') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="paytm" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/paytm.jpg') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('Paytm') }}</span>
                                                            </span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif
                                            @if (addon_is_activated('paytm') && get_setting('toyyibpay_payment') == 1)
                                                <div class="col-6 col-md-4">
                                                    <label class="aiz-megabox d-block mb-3">
                                                        <input value="toyyibpay" class="online_payment" type="radio"
                                                            name="payment_option" checked>
                                                        <span class="d-block aiz-megabox-elem p-3">
                                                            <img src="{{ static_asset('assets/img/cards/toyyibpay.png') }}"
                                                                class="img-fluid mb-2">
                                                            <span class="d-block text-center">
                                                                <span
                                                                    class="d-block fw-600 fs-15">{{ translate('ToyyibPay') }}</span>
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
                                                            <input value="cash_on_delivery" class="online_payment"
                                                                type="radio" name="payment_option" checked>
                                                            <span class="d-block aiz-megabox-elem p-3">
                                                                <img src="{{ static_asset('assets/img/cards/cod.png') }}"
                                                                    class="img-fluid mb-2">
                                                                <span class="d-block text-center">
                                                                    <span
                                                                        class="d-block fw-600 fs-15">{{ translate('Cash on Delivery') }}</span>
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
                                                                <input value="{{ $method->heading }}" type="radio"
                                                                    name="payment_option" class="offline_payment_option"
                                                                    onchange="toggleManualPaymentData({{ $method->id }})"
                                                                    data-id="{{ $method->id }}" checked>
                                                                <span class="d-block aiz-megabox-elem p-3">
                                                                    <img src="{{ uploaded_asset($method->photo) }}"
                                                                        class="img-fluid mb-2">
                                                                    <span class="d-block text-center">
                                                                        <span
                                                                            class="d-block fw-600 fs-15">{{ $method->heading }}</span>
                                                                    </span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endforeach

                                                    @foreach (\App\Models\ManualPaymentMethod::all() as $method)
                                                        <div id="manual_payment_info_{{ $method->id }}"
                                                            class="d-none">
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
                                                                            {{ $info->routing_number }}</li>
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
                                            <span
                                                class="fw-600">{{ single_price(Auth::user()->balance) }}</span>
                                        </div>
                                        @if (Auth::user()->balance < $total)
                                            <button type="button" class="btn btn-secondary" disabled>
                                                {{ translate('Insufficient balance') }}
                                            </button>
                                        @else
                                            <button type="button" onclick="use_wallet()" class="btn btn-primary fw-600">
                                                {{ translate('Pay with wallet') }}
                                            </button>
                                        @endif
                                    </div>
                                @endif
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
                                <a href="{{ route('home') }}" class="link link--style-3">
                                    <i class="las la-arrow-left"></i>
                                    {{ translate('Return to shop') }}
                                </a>
                            </div>
                            <div class="col-6 text-right">
                                <button type="button" onclick="submitOrder(this)"
                                    class="btn btn-primary fw-600">{{ translate('Complete Order') }}</button>
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
        $(document).ready(function() {
            $(".online_payment").click(function() {
                $('#manual_payment_description').parent().addClass('d-none');
            });
            toggleManualPaymentData($('input[name=payment_option]:checked').data('id'));
        });

        var minimum_order_amount_check = {{ get_setting('minimum_order_amount_check') == 1 ? 1 : 0 }};
        var minimum_order_amount =
            {{ get_setting('minimum_order_amount_check') == 1 ? get_setting('minimum_order_amount') : 0 }};

        function use_wallet() {
            $('input[name=payment_option]').val('wallet');
            if ($('#agree_checkbox').is(":checked")) {
                ;
                if (minimum_order_amount_check && $('#sub_total').val() < minimum_order_amount) {
                    AIZ.plugins.notify('danger',
                        '{{ translate('You order amount is less then the minimum order amount') }}');
                } else {
                    $('#checkout-form').submit();
                }
            } else {
                AIZ.plugins.notify('danger', '{{ translate('You need to agree with our policies') }}');
            }
        }

        function submitOrder(el) {
            $(el).prop('disabled', true);
            if ($('#agree_checkbox').is(":checked")) {
                if (minimum_order_amount_check && $('#sub_total').val() < minimum_order_amount) {
                    AIZ.plugins.notify('danger',
                        '{{ translate('You order amount is less then the minimum order amount') }}');
                } else {
                    var offline_payment_active = '{{ addon_is_activated('offline_payment') }}';
                    if (offline_payment_active == 'true' && $('.offline_payment_option').is(":checked") && $('#trx_id').val() == '') {
                        AIZ.plugins.notify('danger',
                            '{{ translate('You need to put Transaction id') }}');
                        $(el).prop('disabled', false);
                    } else {
                        $('#checkout-form').submit();
                    }
                }
            } else {
                AIZ.plugins.notify('danger', '{{ translate('You need to agree with our policies') }}');
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
