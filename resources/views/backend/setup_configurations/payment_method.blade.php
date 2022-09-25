@extends('backend.layouts.app')

@section('content')

<div class="row">

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
</div>

@endsection