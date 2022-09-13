@extends('frontend.layouts.app')

@section('content')

    <section class="gry-bg py-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="card">
                        <div class="align-items-center card-header d-flex justify-content-center text-center" >
                            <h3 class="d-inline-block heading-4 mb-0 mr-3 strong-600" >{{translate('Payment Details')}}</h3>
                            <img loading="lazy"  class="img-fluid" srcna="http://i76.imgup.net/accepted_c22e0.png" height="30">
                        </div>
                        <div class="card-body">
                            <form action="{{ route('mpesa.pay') }}" method="POST">
                                @csrf
                                <input type="hidden" name="CommandID" class="form-control" value="CustomerPayBillOnline" required>
                                <div class='form-row'>
                                    <div class='col-12 form-group required'>
                                        <label class='control-label'>{{translate('Enter Mobile Number')}}</label>
                                        <input type="text" name="Msisdn" class="form-control" placeholder="{{ translate('Enter Mobile Number') }}" required>
                                        <small class="text-warning">{{ translate('KINDLY PLEASE PROVIDE YOUR SAFARICOM M-PESA NUMBER START WITH 254') }}</small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        @if (Session::get('payment_type') == 'cart_payment')
                                            <button class="btn btn-base-1 btn-block" type="submit">{{translate('Pay Now')}} (Ksh{{ $combined_order->grand_total }})</button>
                                        @elseif(Session::get('payment_type') == 'wallet_payment')
                                            <button class="btn btn-base-1 btn-block" type="submit">{{translate('Pay Now')}} (Ksh{{ Session::get('payment_data')['amount'] }})</button>
                                        @elseif(Session::get('payment_type') == 'customer_package_payment')
                                            <button class="btn btn-base-1 btn-block" type="submit">{{translate('Pay Now')}} (Ksh{{ $customer_package->amount }})</button>
                                        @elseif(Session::get('payment_type') == 'seller_package_payment')
                                            <button class="btn btn-base-1 btn-block" type="submit">{{translate('Pay Now')}} (Ksh{{ $seller_package->amount }})</button>
                                        @endif
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
