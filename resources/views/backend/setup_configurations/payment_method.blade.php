    @extends('backend.layouts.app')

    @section('content')

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header ">
                    <h5 class="mb-0 h6">{{translate('Sslcommerz Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="sslcommerz">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="SSLCZ_STORE_ID">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Sslcz Store Id')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="SSLCZ_STORE_ID" value="{{  env('SSLCZ_STORE_ID') }}" placeholder="{{translate('Sslcz Store Id')}}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="SSLCZ_STORE_PASSWD">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Sslcz store password')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="SSLCZ_STORE_PASSWD" value="{{  env('SSLCZ_STORE_PASSWD') }}" placeholder="{{translate('Sslcz store password')}}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Sslcommerz Sandbox Mode')}}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input value="1" name="sslcommerz_sandbox" type="checkbox" @if (get_setting('sslcommerz_sandbox')==1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Iyzico Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="iyzico">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="IYZICO_API_KEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('IYZICO_API_KEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="IYZICO_API_KEY" value="{{  env('IYZICO_API_KEY') }}" placeholder="{{ translate('IYZICO API KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="IYZICO_SECRET_KEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('IYZICO_SECRET_KEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="IYZICO_SECRET_KEY" value="{{  env('IYZICO_SECRET_KEY') }}" placeholder="{{ translate('IYZICO SECRET KEY') }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('IYZICO Sandbox Mode')}}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input value="1" name="iyzico_sandbox" type="checkbox" @if (get_setting('iyzico_sandbox')==1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 ">{{translate('PayStack Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="paystack">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYSTACK_PUBLIC_KEY">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('PUBLIC KEY')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PAYSTACK_PUBLIC_KEY" value="{{  env('PAYSTACK_PUBLIC_KEY') }}" placeholder="{{ translate('PUBLIC KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYSTACK_SECRET_KEY">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('SECRET KEY')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PAYSTACK_SECRET_KEY" value="{{  env('PAYSTACK_SECRET_KEY') }}" placeholder="{{ translate('SECRET KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MERCHANT_EMAIL">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('MERCHANT EMAIL')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="MERCHANT_EMAIL" value="{{  env('MERCHANT_EMAIL') }}" placeholder="{{ translate('MERCHANT EMAIL') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYSTACK_CURRENCY_CODE">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('PAYSTACK CURRENCY CODE')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PAYSTACK_CURRENCY_CODE" value="{{  env('PAYSTACK_CURRENCY_CODE') }}" placeholder="{{ translate('PAYSTACK CURRENCY CODE') }}" required>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6 ">{{translate('Payhere Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="payhere">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYHERE_MERCHANT_ID">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('PAYHERE MERCHANT ID')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PAYHERE_MERCHANT_ID" value="{{  env('PAYHERE_MERCHANT_ID') }}" placeholder="{{ translate('PAYHERE MERCHANT ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYHERE_SECRET">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('PAYHERE SECRET')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PAYHERE_SECRET" value="{{  env('PAYHERE_SECRET') }}" placeholder="{{ translate('PAYHERE SECRET') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYHERE_CURRENCY">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('PAYHERE CURRENCY')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="PAYHERE_CURRENCY" value="{{  env('PAYHERE_CURRENCY') }}" placeholder="{{ translate('PAYHERE CURRENCY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Payhere Sandbox Mode')}}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input value="1" name="payhere_sandbox" type="checkbox" @if (get_setting('payhere_sandbox')==1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Ngenius Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="ngenius">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="NGENIUS_OUTLET_ID">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('NGENIUS OUTLET ID')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="NGENIUS_OUTLET_ID" value="{{  env('NGENIUS_OUTLET_ID') }}" placeholder="{{ translate('NGENIUS OUTLET ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="NGENIUS_API_KEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('NGENIUS API KEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="NGENIUS_API_KEY" value="{{  env('NGENIUS_API_KEY') }}" placeholder="{{ translate('NGENIUS API KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="NGENIUS_CURRENCY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('NGENIUS CURRENCY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="NGENIUS_CURRENCY" value="{{  env('NGENIUS_CURRENCY') }}" placeholder="{{ translate('NGENIUS CURRENCY') }}" required>
                                <br>
                                <div class="alert alert-primary" role="alert">
                                    Currency must be <b>AED</b> or <b>USD</b> or <b>EUR</b><br>
                                    If kept empty, <b>AED</b> will be used automatically
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Authorize Net --}}
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Authorize Net')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="authorizenet">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MERCHANT_LOGIN_ID">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MERCHANT_LOGIN_ID')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MERCHANT_LOGIN_ID" value="{{  env('MERCHANT_LOGIN_ID') }}" placeholder="{{ translate('MERCHANT LOGIN ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MERCHANT_TRANSACTION_KEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MERCHANT_TRANSACTION_KEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MERCHANT_TRANSACTION_KEY" value="{{  env('MERCHANT_TRANSACTION_KEY') }}" placeholder="{{ translate('MERCHANT TRANSACTION KEY') }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('Authorize Net Sandbox Mode')}}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input value="1" name="authorizenet_sandbox" type="checkbox" @if (get_setting('authorizenet_sandbox')==1) checked @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Payku')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="payku">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYKU_BASE_URL">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('PAYKU_BASE_URL')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="PAYKU_BASE_URL" value="{{  env('PAYKU_BASE_URL') }}" placeholder="{{ translate('PAYKU_BASE_URL') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYKU_PUBLIC_TOKEN">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('PAYKU_PUBLIC_TOKEN')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="PAYKU_PUBLIC_TOKEN" value="{{  env('PAYKU_PUBLIC_TOKEN') }}" placeholder="{{ translate('PAYKU_PUBLIC_TOKEN') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYKU_PRIVATE_TOKEN">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('PAYKU_PRIVATE_TOKEN')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="PAYKU_PRIVATE_TOKEN" value="{{  env('PAYKU_PRIVATE_TOKEN') }}" placeholder="{{ translate('PAYKU_PRIVATE_TOKEN') }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @endsection