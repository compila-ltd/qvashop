@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-5 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Negotiable transportation')}}</h5>
            </div>
            <form action="{{ route('negotiable_transportation.update', $negotiable_transportation->id) }}" method="POST">
            	@csrf
                <div class="card-body">
                    <div class="form-group row" id="currency">
                        <label class="col-md-3 col-from-label">{{ translate('User') }}</label>
                        <div class="col-md-9">
                        <input type="text" value="{{ $negotiable_transportation->user->email }}" id="user_name" name="user_name" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-group row" id="currency">
                        <label class="col-md-3 col-from-label">{{ translate('Shop name') }}</label>
                        <div class="col-md-9">
                        @php
                            $shop_name = 'QvaShop';
                            
                            $shop = \App\Models\Shop::where('id', $negotiable_transportation->shop_id)->first();
                            
                            if($shop)
                                $shop_name = $shop->name;

                        @endphp
                        <input type="text" value="{{ $shop_name }}" id="shop_name" name="shop_name" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-group row row">
                        <label class="col-sm-3 col-from-label" for="shipping_cost">{{ translate('Shipping cost')}}</label>
                        <div class="col-2">
                            <input type="text" placeholder="{{ translate('Shipping cost')}}" value="{{ $negotiable_transportation->cost }}" id="shipping_cost" name="shipping_cost" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection