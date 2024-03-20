@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-5 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Negociable transportation')}}</h5>
            </div>
            <form action="{{ route('negotiable_transportation.store') }}" method="POST">
            	@csrf
                <div class="card-body">
                    <div class="form-group row" id="user">
                        <label class="col-md-3 col-from-label">{{ translate('User') }}</label>
                        <div class="col-md-9">
                            <select class="form-control aiz-selectpicker" name="user_id" id="user_id" data-live-search="true" required>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->email }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="cartProducts">
                        <!-- Product carts -->
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

@section('script')
<script>
    document.getElementById('user_id').addEventListener('change', function() {
        var userId = this.value;
        if(userId){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('negotiable_transportation.get_cart_products') }}",
                data: {
                    userId: userId
                },
                success: function(response) {
                    $('#cartProducts').empty();
                    
                    var shopIds = [];
                    var shopNames = [];
                    var shippingCosts = {}; 

                    var address = $('<h6>').text('{{ translate('Shipping Address')}}: ' + response.address);
                    $('#cartProducts').append(address);

                    var user = $('#user_id option:selected').text(); 
                    var cartHeader = $('<h6>').text('{{ translate('Shopping cart for user') }} ' + user);
                    $('#cartProducts').append(cartHeader);

                    var table = $('<table>').addClass('table');
                    table.append('<thead><tr><th>{{ translate('Shop name') }}</th><th>{{ translate('Product') }}</th><th>{{ translate('Quantity') }}</th><th>TN</th></tr></thead>');
                    var tbody = $('<tbody>');

                    $.each(response.cart_products, function(ownerId, products) {
                        var shopNameAdded = false;

                        $.each(products, function(index, product) {
                            var row = $('<tr>');

                            if (!shopNameAdded) {
                                row.append('<td class="align-middle" rowspan="' + products.length + '">' + product.shop_name + '</td>');
                                shopNameAdded = true;
                            }

                            row.append('<td>' + product.product_name + '</td>');
                            row.append('<td>' + product.quantity + '</td>');

                            var negotiableTransportation = product.product_negotiable_transportation == 1 ? 'Si' : 'No';
                            row.append('<td>' + negotiableTransportation + '</td>');

                            if (product.product_negotiable_transportation == 1) {
                                if (!shippingCosts.hasOwnProperty(product.shop_name)) {
                                    shippingCosts[product.shop_name] = $('<input type="text" class="form-control transportation-price" size=3 required placeholder="Costo">');
                                }
                                row.append('<td></td>'); 
                            } else {
                                row.append('<td></td>');
                            }

                            tbody.append(row);
                        });

                        var hasNegotiableTransportation = products.some(function(product) {
                            return product.product_negotiable_transportation == 1;
                        });

                        if (hasNegotiableTransportation) {
                            shopIds.push(products[0].shop_id);
                            shopNames.push(products[0].shop_name);
                        }
                    });

                    table.append(tbody);
                    $('#cartProducts').append(table);

                var secondTableHeader = $('<h6>').text('{{ translate('Define negotiable transportation cost') }}');
                $('#cartProducts').append(secondTableHeader);

                var secondTable = $('<table>').addClass('table');
                var secondTbody = $('<tbody>');

                $.each(shopIds, function(index, shopId) {
                    var costRow = $('<tr>'); 

                    costRow.append($('<td class="align-middle">').text(shopNames[index]));

                    var costInput = $('<input>').attr({
                        type: 'text',
                        class: 'form-control transportation-price',
                        size: '1',
                        required: 'required',
                        placeholder: 'Costo',
                        name: 'shipping_costs[]', 
                        'data-shop-id': shopId 
                    });

                    costRow.append($('<td>').append(costInput)); 

                    secondTbody.append(costRow);
                });

                secondTable.append(secondTbody);

                $('#cartProducts').append(secondTable);

                $.each(shopIds, function(index, shopId) {
                    $('#cartProducts').append('<input type="hidden" name="shop_ids[]" value="' + shopId + '">');
                });

                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                }
            });
        }
    });
</script>

@endsection