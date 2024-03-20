@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col text-right">
            <a href="{{ route('payment_method.create') }}" class="btn btn-circle btn-info">
                <span>{{ translate('Add New')}}</span>
            </a>
        </div>
    </div>
</div>
<br>

<div class="row">
    @foreach($payment_methods as $payment_method)
        @php 
            $currency = \App\Models\Currency::where('id', $payment_method->currency_id)->first();
        @endphp
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="col-11">
                        <h3 class="mb-0 h5 text-start">{{ $payment_method->name }} - {{$currency->code}}</h3>
                    </div>
                    <div class="col-1">
                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('payment_method.edit', $payment_method->id) }}" title="{{ translate('Edit') }}">
                            <i class="las la-edit"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body text-center">
                    <div class="clearfix">
                        <img 
                            class="lazyload float-left"
                            src="{{ asset('assets/img/placeholder.jpg') }}"
                            data-src="{{ uploaded_asset($payment_method->photo) }}"
                            onerror="this.onerror=null;this.src='{{ asset('assets/img/placeholder.jpg') }}';"
                            height="30"
                        >
                        <label class="aiz-switch aiz-switch-success mb-0 float-right"> {{ translate('Status') }}
                            <input type="checkbox" onchange="updatePaymentMethodStatus(this, '{{ $payment_method->id }}', '{{ $payment_method->short_name }}')" <?php if ($payment_method->status == 1) echo "checked"; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="text-left mb-0 mt-0 h6 pt-3 pt-6 pl-2">{{ translate('Exchange rate') }}: {{ $payment_method->exchange_rate }}</div>
                    <label class="aiz-switch aiz-switch-success mb-0 float-right"> {{ translate('Automatic') }}
                        <input type="checkbox" onchange="updatePaymentMethodAutomatic(this, '{{ $payment_method->id }}', '{{ $payment_method->short_name }}')" <?php if ($payment_method->automatic == 1) echo "checked"; ?>>
                        <span class="slider round"></span>
                    </label>
                </div>
            </div>
        </div>
    @endforeach
</div>


@endsection

@section('script')
<script type="text/javascript">
    function updatePaymentMethodStatus(el, id, type) {
        if ($(el).is(':checked')) {
            var status = 1;
        } else {
            var status = 0;
        }      

        $.post("{{ route('payment_method.activation') }}", {
                _token: '{{ csrf_token() }}',
                id: id,
                type: type,
                status: status
            },
            function(data) {
                if (data == '1') {
                    AIZ.plugins.notify('success', "{{ translate('Settings updated successfully ') }}");
                } else {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
    }
    function updatePaymentMethodAutomatic(el, id, type) {
        if ($(el).is(':checked')) {
            var status = 1;
        } else {
            var status = 0;
        }      

        $.post("{{ route('payment_method.automatic') }}", {
                _token: '{{ csrf_token() }}',
                id: id,
                type: type,
                status: status
            },
            function(data) {
                if (data == '1') {
                    AIZ.plugins.notify('success', "{{ translate('Settings updated successfully ') }}");
                } else {
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
    }
</script>
@endsection