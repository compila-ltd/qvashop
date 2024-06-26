@extends('frontend.layouts.app')

@section('content')

<section class="pt-5 mb-4">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 mx-auto">
                <div class="row aiz-steps arrow-divider">
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-shopping-cart"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('1. My Cart') }}</h3>
                        </div>
                    </div>
                    <div class="col done">
                        <div class="text-center text-success">
                            <i class="la-3x mb-2 las la-map"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('2. Shipping info') }}</h3>
                        </div>
                    </div>
                    <div class="col active">
                        <div class="text-center text-primary">
                            <i class="la-3x mb-2 las la-truck"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block">{{ translate('3. Delivery info') }}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-credit-card"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('4. Payment') }}</h3>
                        </div>
                    </div>
                    <div class="col">
                        <div class="text-center">
                            <i class="la-3x mb-2 opacity-50 las la-check-circle"></i>
                            <h3 class="fs-14 fw-600 d-none d-lg-block opacity-50">{{ translate('5. Confirmation') }}
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-4 gry-bg">
    <div class="container">
        <div class="row">
            <div class="col-xxl-8 col-xl-10 mx-auto">
                <form class="form-default" action="{{ route('checkout.store_delivery_info') }}" role="form"
                    method="POST">
                    @csrf
                    @php
                        $admin_products = [];
                        $seller_products = [];

                        $delivery_address = \App\Models\Address::where('id', $carts[0]['address_id'])->first();
                        //dd($delivery_address);

                        $all_cart_digitals_products = true;

                        $shops_delivery_errors = [];
                        $admin_delivery_address_error = false;
                        $admin_delivery_address_error_check = false;
                        $shop_delivery_address = '';

                        $negotiable_transportation_error = false;

                        $delivery_error = false;

                        foreach ($carts as $key => $cartItem){
                            $product = \App\Models\Product::find($cartItem['product_id']);

                            //dd($product);

                            if ($product->added_by == 'admin') {
                                array_push($admin_products, $cartItem['product_id']);

                                if($delivery_address != null)
                                    if (!$admin_delivery_address_error_check) {
                                        $ok_state = \App\Models\State::where('id', $delivery_address->state_id)
                                        ->where('status', 1)
                                        ->first();

                                        if ($ok_state) {
                                            $ok_city = \App\Models\City::where('id', $delivery_address->city_id)
                                            ->where('status', 1)
                                            ->first();

                                            if (!$ok_city) {
                                                array_push($shops_delivery_errors, 'QvaShop');
                                                $admin_delivery_address_error = true;
                                            }
                                        } else {
                                            $admin_delivery_address_error = true;
                                            array_push($shops_delivery_errors, 'QvaShop');
                                        }

                                        $admin_delivery_address_error_check = true;
                                    }
                            } else {
                                $product_ids = [];
                                
                                if (isset($seller_products[$product->user_id])) {
                                    $product_ids = $seller_products[$product->user_id];
                                }
                                
                                array_push($product_ids, $cartItem['product_id']);
                                $seller_products[$product->user_id] = $product_ids;

                                $shop = \App\Models\Shop::where('user_id', $product->user_id)->first();

                                if($delivery_address != null)
                                    if ($shop_delivery_address == '' || $shop_delivery_address != $shop->name) {
                                        $ok_state = \App\Models\ShopState::where('shop_id', $shop->id)
                                                    ->where('state_id', $delivery_address->state_id)
                                                    ->where('status', 1)
                                                    ->first();

                                        //dd($ok_state);

                                        if ($ok_state) {
                                            $ok_city = \App\Models\ShopCity::where('shop_id', $shop->id)
                                                        ->where('city_id', $delivery_address->city_id)
                                                        ->where('status', 1)
                                                        ->first();

                                            if (!$ok_city) {
                                                array_push($shops_delivery_errors, $shop->name);
                                            }
                                        }else{
                                            array_push($shops_delivery_errors, $shop->name);
                                        }

                                        $shop_delivery_address = $shop->name;
                                        
                                    }
                            }
                        }
                    @endphp

                    @if (!empty($admin_products))
                        @php
                            $pickup_point_list = [];
                            if (get_setting('pickup_point') == 1) {
                                $pickup_point_list = \App\Models\PickupPoint::where('pick_up_status', 1)
                                                    ->where('shop_id', 0)
                                                    ->get();
                            }

                            if($delivery_address != null)
                                $city_admin = \App\Models\City::where('id', $delivery_address->city_id)->first();

                        @endphp
                        <div class="card mb-3 shadow-sm border-0 rounded">
                            <div class="card-header p-3">
                                <h5 class="fs-16 fw-600 mb-0">{{ get_setting('site_name') }} {{ translate('Products') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    @php
                                        $product_count = count($admin_products);
                                        $all_digitals_products = true;
                                        $one_only_pickup_point = false;

                                        $negotiable_transportation_agreed = false;
                                        $negotiable_transportation_cost = 0;

                                        $nt = \App\Models\NegotiableTransportation::where('user_id', auth()->user()->id)->where('shop_id', 1)->where('status', 1)->first();

                                        if($nt){
                                            $negotiable_transportation_agreed = true;
                                            $negotiable_transportation_cost = $nt->cost;
                                        }

                                    @endphp
                                    @foreach ($admin_products as $key => $cartItem)
                                        @php
                                            $product_with_negotiable_transportation = false;
                                            $product_with_only_pickup_point = false;
                                            $product = \App\Models\Product::find($cartItem);
                                            if($product->digital != 1){
                                                $all_digitals_products = false;
                                                $all_cart_digitals_products = false;
                                            }

                                            if($product->only_pickup_point == 1){
                                                $one_only_pickup_point = true;
                                                $product_with_only_pickup_point = true;
                                            }

                                            if($product->negotiable_transportation == 1)
                                            {
                                                $product_with_negotiable_transportation = true;
                                                
                                                if(!$negotiable_transportation_agreed && count($pickup_point_list)==0)
                                                    $negotiable_transportation_error = true;
                                            }
                                        @endphp
                                        <li class="list-group-item">
                                            <div class="d-flex">
                                                <span class="mr-2">
                                                    <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                        class="img-fit size-60px rounded"
                                                        alt="{{ $product->getTranslation('name') }}">
                                                </span>
                                                <span class="fs-14 opacity-60">
                                                    {{ $product->getTranslation('name') }}
                                                    @php 
                                                        if($product->digital == 1)
                                                            echo "(" . translate('Free shipping by email') . ")";
                                                        else
                                                        if($product_with_only_pickup_point && $product->digital != 1)
                                                            echo "(" . translate('Warehouse pickup only') . ")";
                                                        else
                                                        if($product_with_negotiable_transportation)
                                                            echo "(" . translate('Negotiable transportation') . ")";
                                                    @endphp
                                                </span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="row border-top pt-3">
                                    <div class="col-md-5">
                                        <h6 class="fs-15 fw-600">{{ translate('Choose Delivery Type') }}</h6>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="row gutters-5">
                                            @if (get_setting('shipping_type') != 'carrier_wise_shipping')
                                                @if(($product_count == 1 && $product->digital == 1) || $all_digitals_products)
                                                    <div class="col-6">
                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                            <input type="radio" name="shipping_type_{{ $key }}"
                                                                value="home_delivery" onchange="show_pickup_point(this, {{ $key }})"
                                                                data-target=".pickup_point_id_{{ $key }}" checked>
                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                <span class="flex-grow-1 pl-3 fw-600">{{ translate('Free shipping by email') }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @else 
                                                    @if($one_only_pickup_point)
                                                        <div class="col-6">
                                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                                    {{ translate('Warehouse pickup only') }}
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @else
                                                        @if($negotiable_transportation_agreed)
                                                            <div class="col-6">
                                                                <label class="aiz-megabox d-block bg-white mb-0">
                                                                    <input type="radio"
                                                                        name="shipping_type_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                                        value="home_delivery" onchange="show_pickup_point(this, 'admin')"
                                                                        data-target=".pickup_point_id_admin" checked>
                                                                    <span class="d-flex p-3 aiz-megabox-elem">
                                                                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                        <span class="flex-grow-1 pl-3 fw-600">
                                                                            {{ translate('Home Delivery') }}: 
                                                                            @php   
                                                                                echo single_price($negotiable_transportation_cost);
                                                                            @endphp    
                                                                        </span>
                                                                </label>
                                                            </div>
                                                        @else
                                                            @if($product_with_negotiable_transportation)
                                                                <div class="col-6">
                                                                    <label class="aiz-megabox d-block bg-white mb-0">
                                                                        <span class="d-flex p-3 aiz-megabox-elem">
                                                                            {{ translate('Negotiable transportation') }} - <a href="https://wa.me/{{ get_setting('helpline_number') }}?text=<?php echo urlencode('Hola. Mi nombre de usuario y correo en QvaShop son: '.auth()->user()->name.', '.auth()->user()->email.' y quiero gestionar el costo de una transportación negociable.'); ?>" target="_blank"><span class="fw-600">Contáctanos</span></a>
                                                                        </span>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                @if (!in_array('QvaShop', $shops_delivery_errors))
                                                                    <div class="col-6">
                                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                                            <input type="radio"
                                                                                name="shipping_type_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                                                value="home_delivery" onchange="show_pickup_point(this, 'admin')"
                                                                                data-target=".pickup_point_id_admin" checked>
                                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                                <span class="flex-grow-1 pl-3 fw-600">
                                                                                    {{ translate('Home Delivery') }}: 
                                                                                    @php
                                                                                        if($delivery_address != null){
                                                                                            if($city_admin->cost == 0)
                                                                                                echo "Gratis";
                                                                                            else    
                                                                                                echo single_price($city_admin->cost);
                                                                                        } 
                                                                                        else
                                                                                            echo "Gratis";
                                                                                    @endphp    
                                                                                </span>
                                                                        </label>
                                                                    </div>
                                                                @else
                                                                    <div class="col-6">
                                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                                {{ translate('They do not deliver to your address') }}
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                <div class="col-6">
                                                    <label class="aiz-megabox d-block bg-white mb-0">
                                                        <input type="radio"
                                                            name="shipping_type_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                            value="carrier" onchange="show_pickup_point(this, 'admin')"
                                                            data-target=".pickup_point_id_admin" checked>
                                                        <span class="d-flex p-3 aiz-megabox-elem">
                                                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                            <span class="flex-grow-1 pl-3 fw-600">{{ translate('Carrier')
                                                                }}</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endif

                                            @if (count($pickup_point_list) > 0 && !$all_digitals_products)
                                            <div class="col-6">
                                                <label class="aiz-megabox d-block bg-white mb-0">
                                                    <input type="radio"
                                                        name="shipping_type_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                        value="pickup_point" 
                                                        @if (in_array("QvaShop", $shops_delivery_errors) || $one_only_pickup_point || $product_with_negotiable_transportation) checked @endif
                                                        onchange="show_pickup_point(this, 'admin')"
                                                        data-target=".pickup_point_id_admin">
                                                    <span class="d-flex p-3 aiz-megabox-elem">
                                                        <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                        <span class="flex-grow-1 pl-3 fw-600">{{ translate('Local Pickup') }}</span>
                                                    </span>
                                                </label>
                                            </div>
                                            @else
                                                @if(!$all_digitals_products)
                                                    <div class="col-6">
                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                No tiene punto de recogida activo
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif
                                            @endif

                                            @if ((count($pickup_point_list) == 0) && ($one_only_pickup_point))
                                                @php
                                                    $delivery_error = true;
                                                @endphp
                                            @endif

                                        </div>
                                        @if (count($pickup_point_list) > 0 && !$all_digitals_products)
                                        <div class="mt-4 pickup_point_id_admin @if (in_array('QvaShop', $shops_delivery_errors) || $one_only_pickup_point || $product_with_negotiable_transportation) d-block @else d-none @endif">
                                            <select class="form-control aiz-selectpicker"
                                                name="pickup_point_id_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                data-live-search="true">
                                                @foreach ($pickup_point_list as $pick_up_point)
                                                <option value="{{ $pick_up_point->id }}" data-content="<span class='d-block'>
                                                                        <span class='d-block fs-16 fw-600 mb-2'>{{ $pick_up_point->getTranslation('name') }}</span>
                                                                        <span class='d-block opacity-50 fs-12'><i class='las la-map-marker'></i> {{ $pick_up_point->getTranslation('address') }}</span>
                                                                        <span class='d-block opacity-50 fs-12'><i class='las la-phone'></i>{{ $pick_up_point->phone }}</span>
                                                                    </span>">
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @else
                                            @if (in_array('QvaShop', $shops_delivery_errors))
                                                @php 
                                                    if($product->digital != 1)
                                                        $delivery_error = true; 
                                                @endphp
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                @if (get_setting('shipping_type') == 'carrier_wise_shipping')
                                <div class="row pt-3 carrier_id_admin">
                                    @foreach ($carrier_list as $carrier_key => $carrier)
                                    <div class="col-md-12 mb-2">
                                        <label class="aiz-megabox d-block bg-white mb-0">
                                            <input type="radio"
                                                name="carrier_id_{{ \App\Models\User::where('user_type', 'admin')->first()->id }}"
                                                value="{{ $carrier->id }}" @if ($carrier_key==0) checked @endif>
                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                <span class="flex-grow-1 pl-3 fw-600">
                                                    <img src="{{ uploaded_asset($carrier->logo) }}" alt="Image"
                                                        class="w-50px img-fit">
                                                </span>
                                                <span class="flex-grow-1 pl-3 fw-700">{{ $carrier->name }}</span>
                                                <span class="flex-grow-1 pl-3 fw-600">{{ translate('Transit in') . ' ' .
                                                    $carrier->transit_time . ' ' . translate('days') }}</span>
                                                {{-- <span class="flex-grow-1 pl-3 fw-600">{{
                                                    Str::headline($carrier->carrier_ranges->first()->billing_type) }}</span>
                                                --}}
                                                <span class="flex-grow-1 pl-3 fw-600">{{
                                                    single_price(carrier_base_price($carts, $carrier->id,
                                                    \App\Models\User::where('user_type', 'admin')->first()->id)) }}</span>
                                            </span>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @if (!empty($seller_products))
                        @foreach ($seller_products as $key => $seller_product)
                            @php
                                $shop = \App\Models\Shop::where('user_id', $key)->first();

                                $pickup_point_list = [];
                                
                                if (get_setting('pickup_point') == 1) {
                                
                                    $pickup_point_list = \App\Models\PickupPoint::where('pick_up_status', 1)
                                                        ->where('shop_id', $shop->id)
                                                        ->get();
                                }
                                
                                if($delivery_address != null)
                                    $city_shop = \App\Models\ShopCity::where('shop_id', $shop->id)->where('city_id', $delivery_address->city_id)->first();

                            @endphp
                            <div class="card mb-3 shadow-sm border-0 rounded">
                                <div class="card-header p-3">
                                    <h5 class="fs-16 fw-600 mb-0">{{ $shop->name }} {{ translate('Products') }}
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        @php
                                            $product_count = count($seller_product);
                                            $all_digitals_products = true;
                                            $one_only_pickup_point = false;
                                            $one_only_negotiable_transportation = false;

                                            
                                            $negotiable_transportation_agreed = false;
                                            $negotiable_transportation_cost = 0;

                                            $nt = \App\Models\NegotiableTransportation::where('user_id', auth()->user()->id)->where('shop_id', $shop->id)->where('status', 1)->first();
                                            
                                            if($nt){
                                                $negotiable_transportation_agreed = true;
                                                $negotiable_transportation_cost = $nt->cost;
                                            }
                                        @endphp

                                        @foreach ($seller_product as $cartItem)
                                            @php
                                                $product_with_negotiable_transportation = false;
                                                $product_with_only_pickup_point = false;

                                                $product = \App\Models\Product::find($cartItem);
                                                if($product->digital != 1){
                                                    $all_digitals_products = false;
                                                    $all_cart_digitals_products = false;
                                                }

                                                if($product->only_pickup_point == 1){
                                                    $one_only_pickup_point = true;
                                                    $product_with_only_pickup_point = true;
                                                }

                                                if($product->negotiable_transportation == 1)
                                                {
                                                    $product_with_negotiable_transportation = true;
                                                    
                                                    if(!$negotiable_transportation_agreed && count($pickup_point_list)==0)
                                                        $negotiable_transportation_error = true;
                                                }
                                            @endphp
                                            <li class="list-group-item">
                                                <div class="d-flex">
                                                    <span class="mr-2">
                                                        <img src="{{ uploaded_asset($product->thumbnail_img) }}"
                                                            class="img-fit size-60px rounded"
                                                            alt="{{ $product->getTranslation('name') }}">
                                                    </span>
                                                    <span class="fs-14 opacity-60">
                                                        {{ $product->getTranslation('name') }}
                                                        @php 
                                                            if($product->digital == 1)
                                                                echo "(" . translate('Free shipping by email') . ")";
                                                            else
                                                            if($product_with_only_pickup_point && $product->digital != 1)
                                                                echo "(" . translate('Warehouse pickup only') . ")";
                                                            else
                                                            if($product_with_negotiable_transportation)
                                                                echo "(" . translate('Negotiable transportation') . ")";
                                                        @endphp
                                                    </span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <div class="row border-top pt-3">
                                        <div class="col-md-5">
                                            <h6 class="fs-15 fw-600">{{ translate('Choose Delivery Type') }}</h6>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="row gutters-5">
                                                @if (get_setting('shipping_type') != 'carrier_wise_shipping')
                                                    @if(($product_count == 1 && $product->digital == 1) || $all_digitals_products)
                                                        <div class="col-6">
                                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                                <input type="radio" name="shipping_type_{{ $key }}"
                                                                    value="home_delivery" onchange="show_pickup_point(this, {{ $key }})"
                                                                    data-target=".pickup_point_id_{{ $key }}" checked>
                                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                    <span class="flex-grow-1 pl-3 fw-600">{{ translate('Free shipping by email') }}</span>
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @else
                                                        @if($one_only_pickup_point)
                                                            <div class="col-6">
                                                                <label class="aiz-megabox d-block bg-white mb-0">
                                                                    <span class="d-flex p-3 aiz-megabox-elem">
                                                                        {{ translate('Warehouse pickup only') }}
                                                                    </span>
                                                                </label>
                                                            </div>
                                                        @else
                                                            @if($negotiable_transportation_agreed)
                                                                <div class="col-6">
                                                                    <label class="aiz-megabox d-block bg-white mb-0">
                                                                        <input type="radio"
                                                                            name="shipping_type_{{ $key }}"
                                                                            value="home_delivery" onchange="show_pickup_point(this, {{ $key }})"
                                                                            data-target=".pickup_point_id_admin" checked>
                                                                        <span class="d-flex p-3 aiz-megabox-elem">
                                                                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                            <span class="flex-grow-1 pl-3 fw-600">
                                                                                {{ translate('Home Delivery') }}: 
                                                                                @php   
                                                                                    echo single_price($negotiable_transportation_cost);
                                                                                @endphp    
                                                                            </span>
                                                                    </label>
                                                                </div>
                                                            @else
                                                                @if($product_with_negotiable_transportation)
                                                                    <div class="col-6">
                                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                                {{ translate('Negotiable transportation') }} - <a href="https://wa.me/{{ get_setting('helpline_number') }}?text=<?php echo urlencode('Hola. Mi nombre de usuario y correo en QvaShop son: '.auth()->user()->name.', '.auth()->user()->email.' y quiero gestionar el costo de una transportación negociable.'); ?>" target="_blank"><span class="fw-600">Contáctanos</span></a>
                                                                            </span>
                                                                        </label>
                                                                    </div>
                                                                @else
                                                                    @if ((!in_array($shop->name, $shops_delivery_errors))&&($city_shop))
                                                                        <div class="col-6">
                                                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                                                <input type="radio" name="shipping_type_{{ $key }}"
                                                                                    value="home_delivery" onchange="show_pickup_point(this, {{ $key }})"
                                                                                    data-target=".pickup_point_id_{{ $key }}" checked>
                                                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                                                    <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                                    <span class="flex-grow-1 pl-3 fw-600">
                                                                                        {{ translate('Home Delivery') }}: 
                                                                                        @php 
                                                                                            if($delivery_address != null){
                                                                                                if($city_shop->cost == 0)
                                                                                                    echo "Gratis";
                                                                                                else    
                                                                                                    echo single_price($city_shop->cost); 
                                                                                            }
                                                                                            else
                                                                                                echo "Gratis";
                                                                                        @endphp    
                                                                                    </span>
                                                                                </span>
                                                                            </label>
                                                                        </div>
                                                                    @else
                                                                        <div class="col-6">
                                                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                                                    {{ translate('They do not deliver to your address') }}
                                                                                </span>
                                                                            </label>

                                                                        </div>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                @else
                                                    <div class="col-6">
                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                            <input type="radio" name="shipping_type_{{ $key }}" value="carrier"
                                                                onchange="show_pickup_point(this, {{ $key }})"
                                                                data-target=".pickup_point_id_{{ $key }}" checked>
                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                <span class="flex-grow-1 pl-3 fw-600">{{ translate('Carrier')
                                                                    }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @endif

                                                @if (count($pickup_point_list) > 0 && !$all_digitals_products)
                                                    <div class="col-6">
                                                        <label class="aiz-megabox d-block bg-white mb-0">
                                                            <input type="radio" 
                                                                name="shipping_type_{{ $key }}" 
                                                                value="pickup_point"
                                                                @if (in_array($shop->name, $shops_delivery_errors) || $one_only_pickup_point || $product_with_negotiable_transportation) checked @endif
                                                                onchange="show_pickup_point(this, {{ $key }})"
                                                                data-target=".pickup_point_id_{{ $key }}">
                                                            <span class="d-flex p-3 aiz-megabox-elem">
                                                                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                                <span class="flex-grow-1 pl-3 fw-600">{{ translate('Local Pickup') }}</span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                @else
                                                    @if(!$all_digitals_products)
                                                        <div class="col-6">
                                                            <label class="aiz-megabox d-block bg-white mb-0">
                                                                <span class="d-flex p-3 aiz-megabox-elem">
                                                                    No tiene punto de recogida activo
                                                                </span>
                                                            </label>
                                                        </div>
                                                    @endif
                                                @endif

                                                @if ((count($pickup_point_list) == 0) && ($one_only_pickup_point))
                                                    @php
                                                        $delivery_error = true;
                                                    @endphp
                                                @endif
                                            </div>
                                            @if (count($pickup_point_list) > 0 && !$all_digitals_products)
                                                <div
                                                    class="mt-4 pickup_point_id_{{ $key }} @if (in_array($shop->name, $shops_delivery_errors) || $one_only_pickup_point || $product_with_negotiable_transportation) d-block @else d-none @endif">
                                                    <select class="form-control aiz-selectpicker" name="pickup_point_id_{{ $key }}"
                                                        data-live-search="true">
                                                        @foreach ($pickup_point_list as $pick_up_point)
                                                        <option value="{{ $pick_up_point->id }}" data-content="<span class='d-block'>
                                                                                                <span class='d-block fs-16 fw-600 mb-2'>{{ $pick_up_point->getTranslation('name') }}</span>
                                                                                                <span class='d-block opacity-50 fs-12'><i class='las la-map-marker'></i> {{ $pick_up_point->getTranslation('address') }}</span>
                                                                                                <span class='d-block opacity-50 fs-12'><i class='las la-phone'></i>{{ $pick_up_point->phone }}</span>
                                                                                            </span>">
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @else
                                                @if (in_array($shop->name, $shops_delivery_errors))
                                                    @php 
                                                        if($product->digital != 1)
                                                            $delivery_error = true; 
                                                    @endphp
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    @if (get_setting('shipping_type') == 'carrier_wise_shipping')
                                        <div class="row pt-3 carrier_id_{{ $key }}">
                                            @foreach ($carrier_list as $carrier_key => $carrier)
                                                <div class="col-md-12 mb-2">
                                                    <label class="aiz-megabox d-block bg-white mb-0">
                                                        <input type="radio" name="carrier_id_{{ $key }}" value="{{ $carrier->id }}" @if($carrier_key==0) checked @endif>
                                                        <span class="d-flex p-3 aiz-megabox-elem">
                                                            <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                                                            <span class="flex-grow-1 pl-3 fw-600">
                                                                <img src="{{ uploaded_asset($carrier->logo) }}" alt="Image"
                                                                    class="w-50px img-fit">
                                                            </span>
                                                            <span class="flex-grow-1 pl-3 fw-600">{{ $carrier->name }}</span>
                                                            <span class="flex-grow-1 pl-3 fw-600">{{ translate('Transit in') . ' ' .
                                                                $carrier->transit_time . ' ' . translate('days') }}</span>
                                                            {{-- <span class="flex-grow-1 pl-3 fw-600">{{
                                                                Str::headline($carrier->carrier_ranges->first()->billing_type) }}</span>
                                                            --}}
                                                            <span class="flex-grow-1 pl-3 fw-600">{{
                                                                single_price(carrier_base_price($carts, $carrier->id, $key)) }}</span>
                                                        </span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <div class="pt-4 d-flex justify-content-between align-items-center">
                        <a href="{{ route('cart') }}">
                            <i class="la la-angle-left"></i>
                            {{ translate('Return to cart') }}
                        </a>
                        
                        @if($all_cart_digitals_products)
                            <button type="submit" class="btn fw-600 btn-primary">{{
                                translate('Continue to Payment') }}
                            </button>
                        @else
                            @if(!$delivery_error && !$negotiable_transportation_error)
                                <button type="submit" class="btn fw-600 btn-primary">{{
                                    translate('Continue to Payment') }}
                                </button>
                            @else
                                <button type="submit" class="btn fw-600 btn-primary" disabled>{{
                                    translate('Continue to Payment') }}
                                </button>
                            @endif
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script type="text/javascript">
    function display_option(key) {

    }

    function show_pickup_point(el, type) {
        var value = $(el).val();
        var target = $(el).data('target');

        console.log(value);
        console.log(target);

        if (value == 'home_delivery' || value == 'carrier') {
            if (!$(target).hasClass('d-none')) {
                $(target).addClass('d-none');
            }
            $('.carrier_id_' + type).removeClass('d-none');
        } else {
            console.log('okokok');
            $(target).removeClass('d-none');
            $('.carrier_id_' + type).addClass('d-none');
        }
    }
</script>
@endsection