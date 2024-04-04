@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Archived Sellers')}}</h1>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_sellers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('Sellers') }}</h5>
            </div>

            @can('delete_seller')
            <div class="dropdown mb-2 mb-md-0">
                <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                    {{ translate('Bulk Action')}}
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" onclick="bulk_delete()">{{ translate('Delete selection')}}</a>
                </div>
            </div>
            @endcan
        </div>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>

                        <th>
                            @if(auth()->user()->can('delete_seller'))
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                            @else
                            #
                            @endif
                        </th>
                        <th>{{ translate('Name')}}</th>
                        <th data-breakpoints="lg">{{ translate('Phone')}}</th>
                        <th data-breakpoints="lg">{{ translate('Email Address')}}</th>
                        <th data-breakpoints="lg">{{ translate('Verification Info')}}</th>
                        <th data-breakpoints="lg">{{ translate('Archived')}}</th>
                        <th data-breakpoints="lg">{{ translate('Num. of Products') }}</th>
                        <th data-breakpoints="lg">{{ translate('Due to seller') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($shops as $key => $shop)
                    <tr>
                        <td>
                            @if(auth()->user()->can('delete_seller'))
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-one" name="id[]" value="{{$shop->id}}">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                            @else
                            {{ ($key+1) + ($shops->currentPage() - 1)*$shops->perPage() }}
                            @endif
                        </td>
                        <td>@if($shop->user->banned == 1) <i class="fa fa-ban text-danger" aria-hidden="true"></i> @endif {{$shop->name}}</td>
                        <td>{{$shop->user->phone}}</td>
                        <td>{{$shop->user->email}}</td>
                        <td>
                            @if ($shop->verification_info != null)
                            <a href="{{ route('sellers.show_verification_request', $shop->id) }}">
                                <span class="badge badge-inline badge-info">{{ translate('Show')}}</span>
                            </a>
                            @endif
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_archive(this)" value="{{ $shop->id }}" type="checkbox" checked>
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>{{ $shop->user->products->count() }}</td>
                        <td>
                            @if ($shop->admin_to_pay >= 0)
                            {{ single_price($shop->admin_to_pay) }}
                            @else
                            {{ single_price(abs($shop->admin_to_pay)) }} ({{ translate('Due to Admin') }})
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $shops->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('script')
<script type="text/javascript">
    $(document).on("change", ".check-all", function() {
        if (this.checked) {
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

    function show_seller_payment_modal(id) {
        $.post("{{ route('sellers.payment_modal') }}", {
            _token: '{{ @csrf_token() }}',
            id: id
        }, function(data) {
            $('#payment_modal #payment-modal-content').html(data);
            $('#payment_modal').modal('show', {
                backdrop: 'static'
            });
            $('.demo-select2-placeholder').select2();
        });
    }

    function show_seller_profile(id) {
        $.post("{{ route('sellers.profile_modal') }}", {
            _token: '{{ @csrf_token() }}',
            id: id
        }, function(data) {
            $('#profile_modal #profile-modal-content').html(data);
            $('#profile_modal').modal('show', {
                backdrop: 'static'
            });
        });
    }

    function update_archive(el) {
        if (el.checked) {
            var status = 1;
        } else {
            var status = 0;
        }
        $.post("{{ route('sellers.archived') }}", {
            _token: '{{ csrf_token() }}',
            id: el.value,
            status: status
        }, function(data) {
            if (data == 1) {
                AIZ.plugins.notify('success', "{{ translate('Sellers updated successfully') }}");
                window.location.href = "{{ route('archived_sellers.index') }}";
            } else {
                AIZ.plugins.notify('danger', "{{ translate('Something went wrong') }}");
            }
        });
    }

    function sort_sellers(el) {
        $('#sort_sellers').submit();
    }

    function bulk_delete() {
        var data = new FormData($('#sort_sellers')[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('bulk-seller-delete') }}",
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response == 1) {
                    location.reload();
                }
            }
        });
    }
</script>
@endsection