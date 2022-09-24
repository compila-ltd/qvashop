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
</div>

@endsection