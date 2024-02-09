@extends('backend.layouts.app')

@section('content')

<div class="card">
    <form class="" action="" id="sort_orders" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('Combined orders') }}</h5>
            </div>
            <div class="col-lg-2">
                <div class="form-group mb-0">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                </div>
            </div>
            <div class="col-auto">
                <div class="form-group mb-0">
                    <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                </div>
            </div>
        </div>

        @php 
            //dd($orders);
        @endphp

        <div class="card-body">
            @if (count($combined_orders) > 0)
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th width="5%">{{ translate('Code')}}</th>
                            <th width="15%" class='text-center' data-breakpoints="md">{{ translate('Date')}}</th>
                            <th width="20%" class='text-center' data-breakpoints="md">{{ translate('Customer') }}</th>
                            <th width="10%" class='text-center' data-breakpoints="md">Importe</th>
                            <th width="10%" class='text-center' data-breakpoints="md">{{ translate('Payment Method')}}</th>
                            <th width="10%" class='text-center' data-breakpoints="md">{{ translate('Payment Status')}}</th>
                            <th width="10%" class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($combined_orders as $key => $combined_order)
                            @php 
                                $order = \App\Models\Order::where('combined_order_id', $combined_order->id)->first();
                                //dd($combined_orders);
                            @endphp
                            @if($order->payment_type == 'qvapay' && $order->payment_status == 'unpaid')
                                @else
                                <tr>
                                    <td>
                                        {{ strtotime($combined_order->created_at); }}
                                    </td>
                                    <td class='text-center'>{{ $combined_order->created_at }}</td>
                                    <td class='text-center'>
                                        @if ($order->user != null)
                                            {{ $order->user->name }}
                                        @else
                                            Guest ({{ $order->guest_id }})
                                        @endif
                                    </td>
                                    <td class='text-center'>
                                        {{ single_price($combined_order->grand_total * $combined_order->exchange_rate) }}
                                    </td>
                                    <td class='text-center'>
                                        {{ $order->payment_type }}
                                    </td>
                                    <td class='text-center'>
                                        @if ($order->payment_status == 'paid')
                                            <span class="badge badge-inline badge-success">{{ translate('Paid')}}</span>
                                        @else
                                            <span class="badge badge-inline badge-dark">{{ translate('Unpaid')}}</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($order->payment_status == 'unpaid')
                                            @php 
                                                $product_availability = true;
                                                $orders = \App\Models\Order::where('combined_order_id', $combined_order->id)->get();
                                                foreach($orders as $single_order){
                                                    $order_detail = \App\Models\OrderDetail::where('order_id', $single_order->id)->first();
                                                    $product_stuck = \App\Models\ProductStock::where('product_id', $order_detail->product_id)->first();
                                                    if($product_stuck->qty <= $order_detail->quantity){
                                                        $product_availability = false;
                                                        break;
                                                    } 
                                                }                                                
                                            @endphp
                                            @if($product_availability)
                                                <a href="#" class="btn btn-soft-success btn-icon btn-circle btn-sm payment-confirm-complete" data-href="{{route('orders.confirm_payment', $combined_order->id)}}" title="{{ translate('Confirm Payment') }}">
                                                    <i class="las la-money-bill"></i>
                                                </a>
                                            @else
                                                <a class="btn btn-soft-danger btn-icon btn-circle btn-sm" title="Existen productos que ya no estan disponibles en esta orden">
                                                    <i class="las la-exclamation-triangle"></i>
                                                </a>
                                            @endif
                                        @endif
                                        <a href="{{route('view_combined_order_orders.index', encrypt($combined_order->id))}}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Order Details') }}">
                                            <i class="las la-eye"></i>
                                        </a>
                                    </td>
                                    
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $combined_orders->links() }}
                </div>
            @else
                <div class="col">
                        <div class="text-center bg-white p-4 rounded shadow">
                            <img class="mw-100 h-200px" src="{{ asset('assets/img/nothing.svg') }}" alt="Image">
                            <h5 class="mb-0 h5 mt-3">{{ translate("There isn't anything added yet")}}</h5>
                        </div>
                </div>
            @endif
        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.confirm_payment_modal')
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });

//        function change_status() {
//            var data = new FormData($('#order_form')[0]);
//            $.ajax({
//                headers: {
//                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//                },
//                url: "{{route('bulk-order-status')}}",
//                type: 'POST',
//                data: data,
//                cache: false,
//                contentType: false,
//                processData: false,
//                success: function (response) {
//                    if(response == 1) {
//                        location.reload();
//                    }
//                }
//            });
//        }

        function bulk_delete() {
            var data = new FormData($('#sort_orders')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('bulk-order-delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

        function sort_orders(el){
            $('#sort_orders').submit();
        }

    </script>
@endsection
