<div class="modal-body p-4 added-to-cart">
    <div class="text-center text-success mb-4">
        <i class="las la-check-circle la-3x"></i>
        <h3>{{ translate('Item added to your cart!')}}</h3>
    </div>
    <div class="media mb-4">
        <img src="{{ static_asset('assets/img/placeholder.jpg') }}" data-src="{{ uploaded_asset($product->thumbnail_img) }}" class="mr-3 lazyload size-100px img-fit rounded" alt="Product Image">
        <div class="media-body pt-3 text-left">
            <h6 class="fw-600">
                {{  $product->getTranslation('name')  }}
            </h6>
            <div class="row mt-3">
                <div class="col-sm-2 opacity-60">
                    <div>{{ translate('Price')}}:</div>
                </div>
                <div class="col-sm-10">
                    <div class="h6 text-primary">
                        <strong>
                            {{ single_price(($data['price'] + $data['tax']) * $data['quantity']) }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-white rounded shadow-sm">
        <div class="border-bottom p-3">
            <h3 class="fs-16 fw-600 mb-0">
                <span class="mr-4">{{ translate('Frequently Bought Together')}}</span>
            </h3>
        </div>
        <div class="p-3">
            <div class="aiz-carousel gutters-5 half-outside-arrow" data-items="2" data-xl-items="3" data-lg-items="4"  data-md-items="3" data-sm-items="2" data-xs-items="2" data-arrows='true' data-infinite='true'>
                @foreach (filter_products(\App\Models\Product::where('category_id', $product->category_id)->where('id', '!=', $product->id))->limit(10)->get() as $key => $related_product)
                <div class="carousel-box">
                    <div class="aiz-card-box border border-light rounded hov-shadow-md my-2 has-transition">
                        <div class="">
                            <a href="{{ route('product', $related_product->slug) }}" class="d-block">
                                <img
                                    class="img-fit lazyload mx-auto h-140px h-md-210px"
                                    src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                    data-src="{{ uploaded_asset($related_product->thumbnail_img) }}"
                                    alt="{{ $related_product->getTranslation('name') }}"
                                    onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                >
                            </a>
                        </div>
                        <div class="p-md-3 p-2 text-left">
                            <div class="fs-15">
                                @if(home_base_price($related_product) != home_discounted_base_price($related_product))
                                    <del class="fw-600 opacity-50 mr-1">{{ home_base_price($related_product) }}</del>
                                @endif
                                <span class="fw-700 text-primary">{{ home_discounted_base_price($related_product) }}</span>
                            </div>
                            <div class="rating rating-sm mt-1">
                                {{ renderStarRating($related_product->rating) }}
                            </div>
                            <h3 class="fw-600 fs-13 text-truncate-2 lh-1-4 mb-0 h-35px">
                                <a href="{{ route('product', $related_product->slug) }}" class="d-block text-reset">{{ $related_product->getTranslation('name') }}</a>
                            </h3>
                            @if (addon_is_activated('club_point'))
                                <div class="rounded px-2 mt-2 bg-soft-primary border-soft-primary border">
                                    {{ translate('Club Point') }}:
                                    <span class="fw-700 float-right">{{ $related_product->earn_point }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="text-center">
        <button class="btn btn-outline-primary mb-3 mb-sm-0" data-dismiss="modal">{{ translate('Back to shopping')}}</button>
        <a href="{{ route('cart') }}" class="btn btn-primary mb-3 mb-sm-0">{{ translate('Proceed to Checkout')}}</a>
    </div>
</div>
