@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Purchase History') }}</h5>
        </div>
        <div class="card-body row gutters-5">
            @if (count($orders) > 0)
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('Code')}}</th>
                            <th data-breakpoints="md">{{ translate('Date')}}</th>
                            <th>{{ translate('Amount')}}</th>
                            <th data-breakpoints="md">{{ translate('Delivery Status')}}</th>
                            <th data-breakpoints="md">{{ translate('Payment Status')}}</th>
                            <th class="text-right">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $key => $order)
                            @if (count($order->orderDetails) > 0)
                                <tr>
                                    <td>
                                        <a href="{{route('purchase_history.details', encrypt($order->id))}}">{{ $order->code }}</a>
                                    </td>
                                    <td>{{ date('d-m-Y', $order->date) }}</td>
                                    <td>
                                        {{ single_price($order->grand_total) }}
                                    </td>
                                    <td>
                                        @if ($order->payment_status == 'paid')
                                            @if ($order->delivery_status == 'delivered')
                                                <span class="badge badge-inline badge-success">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span>
                                            @elseif ($order->delivery_status == 'pending')
                                                <span class="badge badge-inline badge-danger">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span>
                                            @elseif ($order->delivery_status == 'in_progress')
                                                <span class="badge badge-inline badge-warning">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span>
                                            @elseif (($order->delivery_status == 'picked_up') || ($order->delivery_status == 'on_the_way'))
                                                <span class="badge badge-inline badge-info">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span>
                                            @else
                                                <span class="badge badge-inline badge-dark">{{ translate(ucfirst(str_replace('_', ' ', $order->delivery_status))) }}</span>
                                            @endif
                                            @if($order->delivery_viewed == 0)
                                                <span class="ml-2" style="color:green"><strong>*</strong></span>
                                            @endif
                                        @else
                                            <span class="badge badge-inline badge-dark">{{ translate('Unpaid')}}</span>
                                        @endif    
                                    </td>
                                    <td>
                                        @if ($order->payment_status == 'paid')
                                            <span class="badge badge-inline badge-success">{{ translate('Paid')}}</span>
                                        @else
                                            <span class="badge badge-inline badge-dark">{{ translate('Unpaid')}}</span>
                                        @endif
                                        @if($order->payment_status_viewed == 0)
                                            <span class="ml-2" style="color:green"><strong>*</strong></span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if ($order->payment_status == 'unpaid')
                                            <a href="javascript:void(0)" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('purchase_history.destroy', $order->id)}}" title="{{ translate('Cancel') }}">
                                                <i class="las la-trash"></i>
                                            </a>
                                        @endif
                                        <a href="{{route('purchase_history.details', encrypt($order->id))}}" class="btn btn-soft-info btn-icon btn-circle btn-sm" title="{{ translate('Order Details') }}">
                                            <i class="las la-eye"></i>
                                        </a>
                                        @if ($order->payment_status == 'paid')
                                            <a class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="{{ route('invoice.download', $order->id) }}" title="{{ translate('Download Invoice') }}">
                                                <i class="las la-download"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $orders->links() }}
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
