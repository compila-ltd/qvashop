@extends('backend.layouts.app')

@section('content')

<section class="">
    <form class="" action="" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row gutters-5">
            <div class="col-md">
                <div class="row gutters-5 mb-3">
                    <div class="col-md-6 mb-2 mb-md-0">
                        <div class="form-group mb-0">
                            <input class="form-control form-control-lg" type="text" name="keyword" placeholder="{{ translate('Search by Product Name/Barcode') }}" onkeyup="filterProducts()">
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="poscategory" class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value="">{{ translate('All Categories') }}</option>
                            @foreach (\App\Models\Category::all() as $key => $category)
                                <option value="category-{{ $category->id }}">{{ $category->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <select name="brand"  class="form-control form-control-lg aiz-selectpicker" data-live-search="true" onchange="filterProducts()">
                            <option value="">{{ translate('All Brands') }}</option>
                            @foreach (\App\Models\Brand::all() as $key => $brand)
                                <option value="{{ $brand->id }}">{{ $brand->getTranslation('name') }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="aiz-pos-product-list c-scrollbar-light">
                    <div class="d-flex flex-wrap justify-content-center" id="product-list">

                    </div>
                    <div id="load-more" class="text-center">
                        <div class="fs-14 d-inline-block fw-600 btn btn-soft-primary c-pointer" onclick="loadMoreProduct()">{{ translate('Loading..') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-auto w-md-350px w-lg-400px w-xl-500px">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex border-bottom pb-3">
                            <div class="flex-grow-1">
                                <select name="user_id" class="form-control aiz-selectpicker pos-customer" data-live-search="true" onchange="getShippingAddress()">
                                    <option value="">{{translate('Walk In Customer')}}</option>
                                    @foreach ($customers as $key => $customer)
										<option value="{{ $customer->id }}" data-contact="{{ $customer->email }}">
											{{ $customer->name }}
										</option>
									@endforeach
                                </select>
                            </div>
                            <button type="button" class="btn btn-icon btn-soft-dark ml-3 mr-0" data-target="#new-customer" data-toggle="modal">
								<i class="las la-truck"></i>
							</button>
                        </div>
                    
                        <div class="" id="cart-details">
                            <div class="aiz-pos-cart-list mb-4 mt-3 c-scrollbar-light">
                                @php
                                    $subtotal = 0;
                                    $tax = 0;
                                @endphp
                                @if (Session::has('pos.cart'))
                                    <ul class="list-group list-group-flush">
                                    @forelse (Session::get('pos.cart') as $key => $cartItem)
                                        @php
                                            $subtotal += $cartItem['price']*$cartItem['quantity'];
                                            $tax += $cartItem['tax']*$cartItem['quantity'];
                                            $stock = \App\Models\ProductStock::find($cartItem['stock_id']);
                                        @endphp
                                        <li class="list-group-item py-0 pl-2">
                                            <div class="row gutters-5 align-items-center">
                                                <div class="col-auto w-60px">
                                                    <div class="row no-gutters align-items-center flex-column aiz-plus-minus">
                                                        <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="plus" data-field="qty-{{ $key }}">
                                                            <i class="las la-plus"></i>
                                                        </button>
                                                        <input type="text" name="qty-{{ $key }}" id="qty-{{ $key }}" class="col border-0 text-center flex-grow-1 fs-16 input-number" placeholder="1" value="{{ $cartItem['quantity'] }}" min="{{ $stock->product->min_qty }}" max="{{ $stock->qty }}" onchange="updateQuantity({{ $key }})">
                                                        <button class="btn col-auto btn-icon btn-sm fs-15" type="button" data-type="minus" data-field="qty-{{ $key }}">
                                                            <i class="las la-minus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="text-truncate-2">{{ $stock->product->name }}</div>
                                                    <span class="span badge badge-inline fs-12 badge-soft-secondary">{{ $cartItem['variant'] }}</span>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="fs-12 opacity-60">{{ single_price($cartItem['price']) }} x {{ $cartItem['quantity'] }}</div>
                                                    <div class="fs-15 fw-600">{{ single_price($cartItem['price']*$cartItem['quantity']) }}</div>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-circle btn-icon btn-sm btn-soft-danger ml-2 mr-0" onclick="removeFromCart({{ $key }})">
                                                        <i class="las la-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="list-group-item">
                                            <div class="text-center">
                                                <i class="las la-frown la-3x opacity-50"></i>
                                                <p>{{ translate('No Product Added') }}</p>
                                            </div>
                                        </li>
                                    @endforelse
                                    </ul>
                                @else
                                    <div class="text-center">
                                        <i class="las la-frown la-3x opacity-50"></i>
                                        <p>{{ translate('No Product Added') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Sub Total')}}</span>
                                    <span>{{ single_price($subtotal) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Tax')}}</span>
                                    <span>{{ single_price($tax) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Shipping')}}</span>
                                    <span>{{ single_price(Session::get('pos.shipping', 0)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 mb-2 opacity-70">
                                    <span>{{translate('Discount')}}</span>
                                    <span>{{ single_price(Session::get('pos.discount', 0)) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fw-600 fs-18 border-top pt-2">
                                    <span>{{translate('Total')}}</span>
                                    <span>{{ single_price($subtotal+$tax+Session::get('pos.shipping', 0) - Session::get('pos.discount', 0)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pos-footer mar-btm">
                    <div class="d-flex flex-column flex-md-row justify-content-between">
                        <div class="d-flex">
                            <div class="dropdown mr-3 ml-0 dropup">
                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                    {{translate('Shipping')}}
                                </button>
                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                    <div class="input-group">
                                        <input type="number" min="0" placeholder="Amount" name="shipping" class="form-control" value="{{ Session::get('pos.shipping', 0) }}" required onchange="setShipping()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ translate('Flat') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown dropup">
                                <button class="btn btn-outline-dark btn-styled dropdown-toggle" type="button" data-toggle="dropdown">
                                    {{translate('Discount')}}
                                </button>
                                <div class="dropdown-menu p-3 dropdown-menu-lg">
                                    <div class="input-group">
                                        <input type="number" min="0" placeholder="Amount" name="discount" class="form-control" value="{{ Session::get('pos.discount', 0) }}" required onchange="setDiscount()">
                                        <div class="input-group-append">
                                            <span class="input-group-text">{{ translate('Flat') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="my-2 my-md-0">
                            <button type="button" class="btn btn-primary btn-block" onclick="orderConfirmation()">{{ translate('Place Order') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

@endsection

@section('modal')
    <!-- Address Modal -->
    <div id="new-customer" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{translate('Shipping Address')}}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form id="shipping_form">
                    <div class="modal-body" id="shipping_address">


                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal" id="close-button">{{translate('Close')}}</button>
                    <button type="button" class="btn btn-primary btn-styled btn-base-1" id="confirm-address" data-dismiss="modal">{{translate('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- new address modal -->
    <div id="new-address-modal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{translate('Shipping Address')}}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <form class="form-horizontal" action="{{ route('addresses.store') }}" method="POST" enctype="multipart/form-data">
                	@csrf
                    <div class="modal-body">
                        <input type="hidden" name="customer_id" id="set_customer_id" value="">
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="address">{{translate('Address')}}</label>
                                <div class="col-sm-10">
                                    <textarea placeholder="{{translate('Address')}}" id="address" name="address" class="form-control" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label">{{translate('Country')}}</label>
                                <div class="col-sm-10">
                                    <select class="form-control aiz-selectpicker" data-live-search="true" data-placeholder="{{ translate('Select your country') }}" name="country_id" required>
                                        <option value="">{{ translate('Select your country') }}</option>
                                        @foreach (\App\Models\Country::where('status', 1)->get() as $key => $country)
                                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2 control-label"">
                                    <label>{{ translate('State')}}</label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="state_id" required>
                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>{{ translate('City')}}</label>
                                </div>
                                <div class="col-sm-10">
                                    <select class="form-control mb-3 aiz-selectpicker" data-live-search="true" name="city_id" required>
                        
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="postal_code">{{translate('Postal code')}}</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" placeholder="{{translate('Postal code')}}" id="postal_code" name="postal_code" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" row">
                                <label class="col-sm-2 control-label" for="phone">{{translate('Phone')}}</label>
                                <div class="col-sm-10">
                                    <input type="number" min="0" placeholder="{{translate('Phone')}}" id="phone" name="phone" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal">{{translate('Close')}}</button>
                        <button type="submit" class="btn btn-primary btn-styled btn-base-1">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="order-confirm" class="modal fade">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom modal-xl">
            <div class="modal-content" id="variants">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{translate('Order Summary')}}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body" id="order-confirmation">
                    <div class="p-4 text-center">
                        <i class="las la-spinner la-spin la-3x"></i>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-base-3" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="button" onclick="oflinePayment()" class="btn btn-base-1 btn-warning">{{translate('Offline Payment')}}</button>
                    <button type="button" onclick="submitOrder('cash_on_delivery')" class="btn btn-base-1 btn-info">{{translate('Confirm with COD')}}</button>
                    <button type="button" onclick="submitOrder('cash')" class="btn btn-base-1 btn-success">{{translate('Confirm with Cash')}}</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Offline Payment Modal --}}
    <div id="offlin_payment" class="modal fade" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-dialog-zoom" role="document">
            <div class="modal-content">
                <div class="modal-header bord-btm">
                    <h4 class="modal-title h6">{{translate('Offline Payment Info')}}</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class=" row">
                            <label class="col-sm-3 control-label" for="offline_payment_method">{{translate('Payment Method')}}</label>
                            <div class="col-sm-9">
                                <input placeholder="{{translate('Name')}}" id="offline_payment_method" name="offline_payment_method" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class=" row">
                            <label class="col-sm-3 control-label" for="offline_payment_amount">{{translate('Amount')}}</label>
                            <div class="col-sm-9">
                                <input placeholder="{{translate('Amount')}}" id="offline_payment_amount" name="offline_payment_amount" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <label class="col-sm-3 control-label" for="trx_id">{{translate('Transaction ID')}}</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control mb-3" id="trx_id" name="trx_id" placeholder="{{ translate('Transaction ID') }}" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label">{{ translate('Payment Proof') }}</label>
                        <div class="col-md-9">
                            <div class="input-group" data-toggle="aizuploader" data-type="image">
                                <div class="input-group-prepend">
                                    <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                </div>
                                <div class="form-control file-amount">{{ translate('Choose image') }}</div>
                                <input type="hidden" name="payment_proof" class="selected-files">
                            </div>
                            <div class="file-preview box sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-base-3" data-dismiss="modal">{{translate('Close')}}</button>
                    <button type="button" onclick="submitOrder('offline_payment')" class="btn btn-styled btn-base-1 btn-success">{{translate('Confirm')}}</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('script')
    <script type="text/javascript">

        var products = null;

        $(document).ready(function(){
            $('body').addClass('side-menu-closed');
            $('#product-list').on('click','.add-plus:not(.c-not-allowed)',function(){
                var stock_id = $(this).data('stock-id');
                $.post('{{ route('pos.addToCart') }}',{_token:AIZ.data.csrf, stock_id:stock_id}, function(data){
                    if(data.success == 1){
                        updateCart(data.view);
                    }else{
                        AIZ.plugins.notify('danger', data.message);
                    }
                    
                });
            });
            filterProducts();
            getShippingAddress();
        });
        
        $("#confirm-address").click(function (){
            var data = new FormData($('#shipping_form')[0]);
            
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': AIZ.data.csrf
                },
                method: "POST",
                url: "{{route('pos.set-shipping-address')}}",
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data, textStatus, jqXHR) {
                }
            })
        });

        function updateCart(data){
            $('#cart-details').html(data);
            AIZ.extra.plusMinus();
        }

        function filterProducts(){
            var keyword = $('input[name=keyword]').val();
            var category = $('select[name=poscategory]').val();
            var brand = $('select[name=brand]').val();
            $.get('{{ route('pos.search_product') }}',{keyword:keyword, category:category, brand:brand}, function(data){
                products = data;
                $('#product-list').html(null);
                setProductList(data);
            });
        }

        function loadMoreProduct(){
            if(products != null && products.links.next != null){
                $('#load-more').find('.btn').html('{{ translate('Loading..') }}');
                $.get(products.links.next,{}, function(data){
                    products = data;
                    setProductList(data);
                });
            }
        }

        function setProductList(data){
            for (var i = 0; i < data.data.length; i++) {
                $('#product-list').append(
                    `<div class="w-140px w-xl-180px w-xxl-210px mx-2">
                        <div class="card bg-white c-pointer product-card hov-container">
                            <div class="position-relative">
                                <span class="absolute-top-left mt-1 ml-1 mr-0">
                                    ${data.data[i].qty > 0
                                        ? `<span class="badge badge-inline badge-success fs-13">{{ translate('In stock') }}`
                                        : `<span class="badge badge-inline badge-danger fs-13">{{ translate('Out of stock') }}` }
                                    : ${data.data[i].qty}</span>
                                </span>
                                ${data.data[i].variant != null
                                    ? `<span class="badge badge-inline badge-warning absolute-bottom-left mb-1 ml-1 mr-0 fs-13 text-truncate">${data.data[i].variant}</span>`
                                    : '' }
                                <img src="${data.data[i].thumbnail_image }" class="card-img-top img-fit h-120px h-xl-180px h-xxl-210px mw-100 mx-auto" >
                            </div>
                            <div class="card-body p-2 p-xl-3">
                                <div class="text-truncate fw-600 fs-14 mb-2">${data.data[i].name}</div>
                                <div class="">
                                    ${data.data[i].price != data.data[i].base_price
                                        ? `<del class="mr-2 ml-0">${data.data[i].base_price}</del><span>${data.data[i].price}</span>`
                                        : `<span>${data.data[i].base_price}</span>`
                                    }
                                </div>
                            </div>
                            <div class="add-plus absolute-full rounded overflow-hidden hov-box ${data.data[i].qty <= 0 ? 'c-not-allowed' : '' }" data-stock-id="${data.data[i].stock_id}">
                                <div class="absolute-full bg-dark opacity-50">
                                </div>
                                <i class="las la-plus absolute-center la-6x text-white"></i>
                            </div>
                        </div>
                    </div>`
                );
            }
            if (data.links.next != null) {
                $('#load-more').find('.btn').html('{{ translate('Load More.') }}');
            }
            else {
                $('#load-more').find('.btn').html('{{ translate('Nothing more found.') }}');
            }
        }

        function removeFromCart(key){
            $.post('{{ route('pos.removeFromCart') }}', {_token:AIZ.data.csrf, key:key}, function(data){
                updateCart(data);
            });
        }

        function addToCart(product_id, variant, quantity){
            $.post('{{ route('pos.addToCart') }}',{_token:AIZ.data.csrf, product_id:product_id, variant:variant, quantity, quantity}, function(data){
                $('#cart-details').html(data);
                $('#product-variation').modal('hide');
            });
        }

        function updateQuantity(key){
            $.post('{{ route('pos.updateQuantity') }}',{_token:AIZ.data.csrf, key:key, quantity: $('#qty-'+key).val()}, function(data){
                if(data.success == 1){
                    updateCart(data.view);
                }else{
                    AIZ.plugins.notify('danger', data.message);
                }
            });
        }

        function setDiscount(){
            var discount = $('input[name=discount]').val();
            $.post('{{ route('pos.setDiscount') }}',{_token:AIZ.data.csrf, discount:discount}, function(data){
                updateCart(data);
            });
        }

        function setShipping(){
            var shipping = $('input[name=shipping]').val();
            $.post('{{ route('pos.setShipping') }}',{_token:AIZ.data.csrf, shipping:shipping}, function(data){
                updateCart(data);
            });
        }

        function getShippingAddress(){
            $.post('{{ route('pos.getShippingAddress') }}',{_token:AIZ.data.csrf, id:$('select[name=user_id]').val()}, function(data){
                $('#shipping_address').html(data);
            });
        }

        function add_new_address(){
            var customer_id = $('#customer_id').val();
            $('#set_customer_id').val(customer_id);
            $('#new-address-modal').modal('show');
            $("#close-button").click();
        }

        function orderConfirmation(){
            $('#order-confirmation').html(`<div class="p-4 text-center"><i class="las la-spinner la-spin la-3x"></i></div>`);
            $('#order-confirm').modal('show');
            $.post('{{ route('pos.getOrderSummary') }}',{_token:AIZ.data.csrf}, function(data){
                $('#order-confirmation').html(data);
            });
        }

        function oflinePayment(){
            $('#offlin_payment').modal('show');
        }

        function submitOrder(payment_type){
            var user_id = $('select[name=user_id]').val();
            var shipping = $('input[name=shipping]:checked').val();
            var discount = $('input[name=discount]').val();
            var shipping_address = $('input[name=address_id]:checked').val();
            var offline_payment_method = $('input[name=offline_payment_method]').val();
            var offline_payment_amount = $('input[name=offline_payment_amount]').val();
            var offline_trx_id = $('input[name=trx_id]').val();
            var offline_payment_proof = $('input[name=payment_proof]').val();
            
            $.post('{{ route('pos.order_place') }}',{
                _token                  : AIZ.data.csrf, 
                user_id                 : user_id,
                shipping_address        : shipping_address, 
                payment_type            : payment_type, 
                shipping                : shipping, 
                discount                : discount,
                offline_payment_method  : offline_payment_method,
                offline_payment_amount  : offline_payment_amount,
                offline_trx_id          : offline_trx_id,
                offline_payment_proof   : offline_payment_proof
                
            }, function(data){
                if(data.success == 1){
                    AIZ.plugins.notify('success', data.message );
                    location.reload();
                }
                else{
                    AIZ.plugins.notify('danger', data.message );
                }
            });
        }


        //address
        $(document).on('change', '[name=country_id]', function() {
            var country_id = $(this).val();
            get_states(country_id);
        });

        $(document).on('change', '[name=state_id]', function() {
            var state_id = $(this).val();
            get_city(state_id);
        });
        
        function get_states(country_id) {
            $('[name="state"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-state')}}",
                type: 'POST',
                data: {
                    country_id  : country_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="state_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }

        function get_city(state_id) {
            $('[name="city"]').html("");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('get-city')}}",
                type: 'POST',
                data: {
                    state_id: state_id
                },
                success: function (response) {
                    var obj = JSON.parse(response);
                    if(obj != '') {
                        $('[name="city_id"]').html(obj);
                        AIZ.plugins.bootstrapSelect('refresh');
                    }
                }
            });
        }
    </script>
@endsection
