@extends('frontend.layouts.app')

@section('meta_title'){{ $shop->meta_title }}@stop

@section('meta_description'){{ $shop->meta_description }}@stop

@section('meta')
    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="{{ $shop->meta_title }}">
    <meta itemprop="description" content="{{ $shop->meta_description }}">
    <meta itemprop="image" content="{{ uploaded_asset($shop->logo) }}">

    <!-- Twitter Card data -->
    <meta name="twitter:card" content="website">
    <meta name="twitter:site" content="@publisher_handle">
    <meta name="twitter:title" content="{{ $shop->meta_title }}">
    <meta name="twitter:description" content="{{ $shop->meta_description }}">
    <meta name="twitter:creator" content="@author_handle">
    <meta name="twitter:image" content="{{ uploaded_asset($shop->meta_img) }}">

    <!-- Open Graph data -->
    <meta property="og:title" content="{{ $shop->meta_title }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ route('shop.visit', $shop->slug) }}" />
    <meta property="og:image" content="{{ uploaded_asset($shop->logo) }}" />
    <meta property="og:description" content="{{ $shop->meta_description }}" />
    <meta property="og:site_name" content="{{ $shop->name }}" />
@endsection

@section('content')
    <section class="pt-5 mb-4 bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="d-flex justify-content-center">
                        <img
                            height="70"
                            class="lazyload"
                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                            data-src="@if ($shop->logo !== null) {{ uploaded_asset($shop->logo) }} @else {{ static_asset('assets/img/placeholder.jpg') }} @endif"
                            alt="{{ $shop->name }}"
                        >
                        <div class="pl-4 text-left">
                            <h1 class="fw-600 h4 mb-0">{{ $shop->name }}
                                @if ($shop->verification_status == 1)
                                    <span class="ml-2"><i class="fa fa-check-circle" style="color:green"></i></span>
                                @else
                                    <span class="ml-2"><i class="fa fa-times-circle" style="color:red"></i></span>
                                @endif
                            </h1>
                            <div class="rating rating-sm mb-1">
                                {{ renderStarRating($shop->rating) }}
                            </div>
                            <div class="location opacity-60">{{ $shop->address }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="border-bottom mt-5"></div>
            <div class="row align-items-center">
                <div class="col-lg-6 order-2 order-lg-0">
                    <ul class="list-inline mb-0 text-center text-lg-left">
                        <li class="list-inline-item ">
                            <a class="text-reset d-inline-block fw-600 fs-15 p-3 @if(!isset($type)) border-bottom border-primary border-width-2 @endif" href="{{ route('shop.visit', $shop->slug) }}">{{ translate('Store Home')}}</a>
                        </li>
                        <li class="list-inline-item ">
                            <a class="text-reset d-inline-block fw-600 fs-15 p-3 @if(isset($type) && $type == 'top-selling') border-bottom border-primary border-width-2 @endif" href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'top-selling']) }}">{{ translate('Top Selling')}}</a>
                        </li>
                        <li class="list-inline-item ">
                            <a class="text-reset d-inline-block fw-600 fs-15 p-3 @if(isset($type) && $type == 'all-products') border-bottom border-primary border-width-2 @endif" href="{{ route('shop.visit.type', ['slug'=>$shop->slug, 'type'=>'all-products']) }}">{{ translate('All Products')}}</a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 order-1 order-lg-0">
                    <ul class="text-center text-lg-right mt-4 mt-lg-0 social colored list-inline mb-0">
                        @if ($shop->facebook != null)
                            <li class="list-inline-item">
                                <a href="{{ $shop->facebook }}" class="facebook" target="_blank">
                                    <i class="lab la-facebook-f"></i>
                                </a>
                            </li>
                        @endif
                        @if ($shop->instagram != null)
                            <li class="list-inline-item">
                                <a href="{{ $shop->instagram }}" class="instagram" target="_blank">
                                    <i class="lab la-instagram"></i>
                                </a>
                            </li>
                        @endif
                        @if ($shop->twitter != null)
                            <li class="list-inline-item">
                                <a href="{{ $shop->twitter }}" class="twitter" target="_blank">
                                    <i class="lab la-twitter"></i>
                                </a>
                            </li>
                        @endif
                        @if ($shop->google != null)
                            <li class="list-inline-item">
                                <a href="{{ $shop->google }}" class="google-plus" target="_blank">
                                    <i class="lab la-google"></i>
                                </a>
                            </li>
                        @endif
                        @if ($shop->youtube != null)
                            <li class="list-inline-item">
                                <a href="{{ $shop->youtube }}" class="youtube" target="_blank">
                                    <i class="lab la-youtube"></i>
                                </a>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </section>

    @if (!isset($type))
        <section class="mb-5">
            <div class="container">
                <div class="aiz-carousel dots-inside-bottom mobile-img-auto-height" data-arrows="true" data-dots="true" data-autoplay="true">
                    @if ($shop->sliders != null)
                        @foreach (explode(',',$shop->sliders) as $key => $slide)
                            <div class="carousel-box">
                                <img class="d-block w-100 lazyload rounded h-200px h-lg-380px img-fit" src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" data-src="{{ uploaded_asset($slide) }}" alt="{{ $key }} offer">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
        <section class="mb-4">
            <div class="container">
                <div class="text-center mb-4">
                    <h3 class="h3 fw-600 border-bottom">
                        <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">{{ translate('Featured Products')}}</span>
                    </h3>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="aiz-carousel gutters-10" data-items="6" data-xl-items="5" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-autoplay='true' data-infinute="true" data-dots="true">
                            @foreach ($shop->user->products->where('published', 1)->where('approved', 1)->where('seller_featured', 1) as $key => $product)
                                <div class="carousel-box">
                                    @include('frontend.partials.product_box_1',['product' => $product])
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="mb-4">
        <div class="container">
            <div class="mb-4">
                <h3 class="h3 fw-600 border-bottom">
                    <span class="border-bottom border-primary border-width-2 pb-3 d-inline-block">
                        @if (!isset($type))
                            {{ translate('New Arrival Products')}}
                        @elseif ($type == 'top-selling')
                            {{ translate('Top Selling')}}
                        @elseif ($type == 'all-products')
                            {{ translate('All Products')}}
                        @endif
                    </span>
                </h3>
            </div>
            <div class="row gutters-5 row-cols-xxl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
                @php
                    if (!isset($type)){
                        $products = \App\Models\Product::where('user_id', $shop->user->id)->where('published', 1)->where('approved', 1)->orderBy('created_at', 'desc')->paginate(24);
                    }
                    elseif ($type == 'top-selling'){
                        $products = \App\Models\Product::where('user_id', $shop->user->id)->where('published', 1)->where('approved', 1)->orderBy('num_of_sale', 'desc')->paginate(24);
                    }
                    elseif ($type == 'all-products'){
                        $products = \App\Models\Product::where('user_id', $shop->user->id)->where('published', 1)->where('approved', 1)->paginate(24);
                    }
                @endphp
                @foreach ($products as $key => $product)
                    <div class="col mb-3">
                        @include('frontend.partials.product_box_1',['product' => $product])
                    </div>
                @endforeach
            </div>
            <div class="aiz-pagination aiz-pagination-center mb-4">
                {{ $products->links() }}
            </div>
        </div>
    </section>


@endsection
