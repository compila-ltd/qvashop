@extends('backend.layouts.app')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Seller Withdraw Request')}}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th data-breakpoints="lg">{{translate('Date')}}</th>
                        <th>{{translate('Seller')}}</th>
                        <th data-breakpoints="lg">{{translate('Total Amount to Pay')}}</th>
                        <th>{{translate('Requested Amount')}}</th>
                        <th data-breakpoints="lg" width="40%">{{ translate('Message') }}</th>
                        <th data-breakpoints="lg">{{ translate('Status') }}</th>
                        <th data-breakpoints="lg" width="15%" class="text-right">{{translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($seller_withdraw_requests as $key => $seller_withdraw_request)
                        @php $user = \App\Models\User::find($seller_withdraw_request->user_id); @endphp
                        @if ($user && $user->shop)
                            <tr>
                                <td>{{ ($key+1) + ($seller_withdraw_requests->currentPage() - 1)*$seller_withdraw_requests->perPage() }}</td>
                                <td>{{ $seller_withdraw_request->created_at }}</td>
                                <td>{{ $user->name }} ({{ $user->shop->name }})</td>
                                <td>{{ single_price($user->shop->admin_to_pay) }}</td>
                                <td>{{ single_price($seller_withdraw_request->amount) }}</td>
                                <td>
                                    {{ $seller_withdraw_request->message }}
                                </td>
                                <td>
                                    @if ($seller_withdraw_request->status == 1)
                                    <span class="badge badge-inline badge-success">{{translate('Paid')}}</span>
                                    @else
                                    <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <a onclick="show_seller_payment_modal('{{$seller_withdraw_request->user_id}}','{{ $seller_withdraw_request->id }}');" class="btn btn-soft-warning btn-icon btn-circle btn-sm" href="javascript:void(0);" title="{{ translate('Pay Now') }}">
                                        <i class="las la-money-bill"></i>
                                    </a>
                                    @can('pay_to_seller')
                                        <a onclick="show_message_modal('{{ $seller_withdraw_request->id }}');" class="btn btn-soft-success btn-icon btn-circle btn-sm" href="javascript:void(0);" title="{{ translate('Message View') }}">
                                            <i class="las la-eye"></i>
                                        </a>
                                    @endcan
                                    @can('seller_payment_history')
                                        <a href="{{route('sellers.payment_history', encrypt($seller_withdraw_request->user_id))}}" class="btn btn-soft-primary btn-icon btn-circle btn-sm"  title="{{ translate('Payment History') }}">
                                            <i class="las la-history"></i>
                                        </a>
                                    @endcan
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $seller_withdraw_requests->links() }}
            </div>
        </div>
    </div>

@endsection

@section('modal')
<!-- payment Modal -->
<div class="modal fade" id="payment_modal">
  <div class="modal-dialog">
    <div class="modal-content" id="payment-modal-content">

    </div>
  </div>
</div>


<!-- Message View Modal -->
<div class="modal fade" id="message_modal">
  <div class="modal-dialog">
    <div class="modal-content" id="message-modal-content">

    </div>
  </div>
</div>


@endsection



@section('script')
  <script type="text/javascript">
      function show_seller_payment_modal(id, seller_withdraw_request_id){
          $.post('{{ route('withdraw_request.payment_modal') }}',{_token:'{{ @csrf_token() }}', id:id, seller_withdraw_request_id:seller_withdraw_request_id}, function(data){
              $('#payment-modal-content').html(data);
              $('#payment_modal').modal('show', {backdrop: 'static'});
              $('.demo-select2-placeholder').select2();
          });
      }

      function show_message_modal(id){
          $.post('{{ route('withdraw_request.message_modal') }}',{_token:'{{ @csrf_token() }}', id:id}, function(data){
              $('#message-modal-content').html(data);
              $('#message_modal').modal('show', {backdrop: 'static'});
          });
      }
  </script>

@endsection
