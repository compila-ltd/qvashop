@extends('backend.layouts.app')


@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('All Carriers') }}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('carriers.create') }}" class="btn btn-primary">
                    <span>{{ translate('Add New Carrier') }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header row gutters-5">
                    <div class="col text-center text-md-left">
                        <h5 class="mb-md-0 h6">{{ translate('Carriers') }}</h5>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Logo') }}</th>
                                <th>{{ translate('Name') }}</th>
                                <th>{{ translate('Transit Time') }}</th>
                                <th>{{ translate('Status') }}</th>
                                <th style="text-align: right;">{{ translate('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($carriers as $key => $carrier)
                                <tr>
                                    <td>
                                        {{ $carriers->firstItem() + $key }}
                                    </td>
                                    <td>
                                        <img src="{{ uploaded_asset($carrier->logo) }}" alt="{{translate('Carrier')}}" class="h-50px">
                                    </td>
                                    <td>{{ $carrier->name }}</td>
                                    <td>{{ $carrier->transit_time }}</td>
                                    <td>
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input onchange="update_status(this)" value="{{ $carrier->id }}" type="checkbox" <?php if($carrier->status == 1) echo "checked";?> >
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td style="text-align: right;">
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('carriers.edit', $carrier->id) }}" title="{{ translate('Edit') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('carriers.destroy', $carrier->id)}}" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination">
                        {{ $carriers->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        
        function update_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('carriers.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Carrier Status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Carrier Status went wrong') }}');
                }
            });
        }

    </script>
@endsection
