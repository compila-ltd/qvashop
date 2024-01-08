@extends('seller.layouts.app')

@section('panel_content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h1 class="h3">Seleccione el municipio y el costo de envío</h1>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th data-breakpoints="lg" width="1%">#</th>
                            <th width="20%">{{ translate('State')}}</th>
                            <th>Municipio</th>
                            <th class="text-center">Costo de envío (USD)</th>
                            <th>Activar</th>
                            <th data-breakpoints="lg" class="text-center">{{ translate('Options')}}</th>
                        </tr>
                    </thead>
                    <tbody>                       
                        @foreach($cities as $key => $city)
                            <tr>
                                <td>{{ ($key+1) + ($cities->currentPage() - 1) * $cities->perPage() }}</td>
                                <td>{{ $city->state->name }}</td>
                                <td>{{ $city->name }}</td>
                                <td class="text-center">{{ single_price($city->cost) }}</td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_status(this)" value="{{ $city->id }}" type="checkbox" <?php if($city->status == 1) echo "checked";?> >
                                        <span class="slider round"></span>
                                    </label>
                                    </td>
                                <td class="text-center">
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('seller.cities.edit', ['id'=>$city->id, 'lang'=>env('DEFAULT_LANGUAGE')]) }}" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $cities->appends(request()->input())->links() }}
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
        function sort_cities(el){
            $('#sort_cities').submit();
        }

        function update_status(el){

            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('seller.cities.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Country status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

    </script>
@endsection
