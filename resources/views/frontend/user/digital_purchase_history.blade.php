@extends('frontend.layouts.user_panel')

@section('panel_content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Download Your Product') }}</h5>
        </div>
        <div class="card-body row gutters-5">
          <table class="table mb-0">
              <thead>
                  <tr>
                      <th>{{ translate('Product')}}</th>
                      <th width="20%">{{ translate('Option')}}</th>
                  </tr>
              </thead>
              <tbody>
                @forelse ($orders as $key => $order_id)
                    @php
                        $order = \App\Models\OrderDetail::find($order_id->id);
                    @endphp
                    <tr>
                        <td><a href="{{ route('product', $order->product->slug) }}">{{ $order->product->getTranslation('name') }}</a></td>
                        <td>
                        <a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{route('digital-products.download', encrypt($order->product->id))}}" title="{{ translate('Download') }}">
                            <i class="las la-download"></i>
                        </a>
                        </td>
                    </tr>
                @empty
                    <td class="col">
                        <div class="text-center bg-white p-4 rounded shadow">
                            <img class="mw-100 h-200px" src="{{ asset('assets/img/nothing.svg') }}" alt="Image">
                            <h5 class="mb-0 h5 mt-3">{{ translate("There isn't anything added yet")}}</h5>
                        </div>
                    </td>
                @endforelse
              </tbody>
          </table>
            {{ $orders->links() }}
        </div>
    </div>
@endsection
