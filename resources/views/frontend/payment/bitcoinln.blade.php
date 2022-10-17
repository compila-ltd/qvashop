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
            <div class="col-lg-6 p-2 offset-lg-3">
                <div class="card rounded border-0 shadow-sm p-5 text-center">
                    
                    <div class="p-1">
                        @php
                        $removedXML = '<?xml version="1.0" encoding="UTF-8"?>';
                        @endphp
                        {!! str_replace($removedXML, '', QrCode::size(250)->generate($wallet['invoice'])) !!}
                    </div>
                    
                    <div class="mt-3">
                        <h5>Invoice:</h5>
                        {{ $wallet['invoice'] }}
                    </div>
                    
                    <div class="mt-2">
                        <h6>Cantidad:</h6>
                        {{ $wallet['btc_amount'] }}
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
@endsection