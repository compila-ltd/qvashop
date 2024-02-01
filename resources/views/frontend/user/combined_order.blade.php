@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Combined Orders') }}</h5>
        </div>
        <div class="card-body">
            @if (count($combined_orders) > 0)
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th width="13%">{{ translate('Code')}}</th>
                            <th width="15%" class='text-center' data-breakpoints="md">{{ translate('Date')}}</th>
                            <th class='text-center'>Importe</th>
                            <th class='text-center' data-breakpoints="md">{{ translate('Payment Method')}}</th>
                            <th class='text-center' data-breakpoints="md">{{ translate('Payment Status')}}</th>
                            <th class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($combined_orders as $key => $combined_order)
                            @php 
                                $order = \App\Models\Order::where('combined_order_id', $combined_order->id)->first();

                                $payment_type = "";
                                $total_cost = 0;
                            @endphp
                            @if($order != null)
                                @if($order->payment_type == 'qvapay' && $order->payment_status == 'unpaid')
                                    @else
                                    <tr>
                                        <td>
                                            {{ strtotime($combined_order->created_at); }}
                                        </td>
                                        <td class='text-center'>{{ $combined_order->created_at->format('d-m-Y H:i:s') }}</td>
                                        @if($order->payment_type == 'cup_payment')
                                            <td class='text-center'>
                                                {{ single_price($combined_order->grand_total_cup) }}
                                            </td>
                                            <td class='text-center'>
                                                CUP
                                            </td>
                                            @php 
                                                $payment_type = "CUP";
                                                $total_cost = single_price($combined_order->grand_total_cup);
                                            @endphp
                                        @else
                                            @if($order->payment_type == 'mlc_payment')
                                                <td class='text-center'>
                                                    {{ single_price($combined_order->grand_total_mlc) }}
                                                </td>
                                                <td class='text-center'>
                                                    MLC
                                                </td>
                                                @php 
                                                    $payment_type = "MLC";
                                                    $total_cost = single_price($combined_order->grand_total_mlc);
                                                @endphp
                                            @else
                                                <td class='text-center'>
                                                    {{ single_price($combined_order->grand_total) }}
                                                </td>
                                                <td class='text-center'>
                                                    Qvapay
                                                </td>
                                            @endif
                                        @endif
                                        <td class='text-center'>
                                            @if ($order->payment_status == 'paid')
                                                <span class="badge badge-inline badge-success">{{ translate('Paid')}}</span>
                                            @else
                                                <span class="badge badge-inline badge-dark">{{ translate('Unpaid')}}</span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            @if ($order->payment_status == 'unpaid')
                                                <a href="https://wa.me/{{ get_setting('helpline_number') }}?text=<?php echo urlencode('Hola. Mi nombre de usuario en QvaShop es: '.json_decode($order->shipping_address)->name.' y quiero pagar la orden con código: '.strtotime($combined_order->created_at).' con un importe de '.$total_cost.' '.$payment_type.' '); ?>" target="_blank" class="btn btn-soft-success btn-icon btn-circle btn-sm" title="Pagar orden">
                                                    <i class="las la-money-bill"></i>
                                                </a>
                                            @endif
                                            <a href="{{route('purchase_history.orders', encrypt($combined_order->id))}}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Order Details') }}">
                                                <i class="las la-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
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

    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')

    <div class="modal fade" id="order_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div id="order-details-modal-body">

                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script type="text/javascript">
        $('#order_details').on('hidden.bs.modal', function () {
            location.reload();
        })
    </script>

@endsection
