@extends('seller.layouts.app')

@section('panel_content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-12">
            <h1 class="h3">Seleccione la(s) provincia(s) donde hará entrega</h1>
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
                            <th width="1%">#</th>
                            <th>{{ translate('Name')}}</th>
                            <th>Activar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($states as $key => $state)
                            <tr>
                                <td>{{ ($key+1) + ($states->currentPage() - 1)*$states->perPage() }}</td>
                                <td>{{ $state->name }}</td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input onchange="update_status(this)" value="{{ $state->id }}" type="checkbox" <?php if($state->status == 1) echo "checked";?> >
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="aiz-pagination">
                    {{ $states->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

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
            $.post('{{ route('seller.states.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', 'Se ha actualizado correctamente la provincia');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

    </script>
@endsection
