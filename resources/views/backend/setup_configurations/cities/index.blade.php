@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
    			<h1 class="h3">{{translate('All cities')}}</h1>
    		</div>
    	</div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <form class="" id="sort_cities" action="" method="GET">
                    <div class="card-header row gutters-5">
                        <div class="col text-center text-md-left">
                            <h5 class="mb-md-0 h6">{{ translate('Cities') }}</h5>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="sort_city" name="sort_city" @isset($sort_city) value="{{ $sort_city }}" @endisset placeholder="{{ translate('Type city name & Enter') }}">
                        </div>
                        <div class="col-md-4">
                            <select class="form-control aiz-selectpicker" data-live-search="true" id="sort_state" name="sort_state">
                                <option value="">{{ translate('Select State') }}</option>
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}" @if ($sort_state == $state->id) selected @endif {{$sort_state}}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-primary" type="submit">{{ translate('Filter') }}</button>
                        </div>
                    </div>
                </form>
                <div class="card-body">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th data-breakpoints="lg">#</th>
                                <th>{{translate('Name')}}</th>
                                <th>{{translate('State')}}</th>
                                <th>{{translate('Area Wise Shipping Cost')}}</th>
                                <th>{{translate('Show/Hide')}}</th>
                                <th data-breakpoints="lg" class="text-right">{{translate('Options')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cities as $key => $city)
                                <tr>
                                    <td>{{ ($key+1) + ($cities->currentPage() - 1) * $cities->perPage() }}</td>
                                    <td>{{ $city->getTranslation('name') }}</td>
                                    <td>{{ $city->state->name }}</td>
                                    <td>{{ single_price($city->cost) }}</td>
                                    <td>
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                          <input onchange="update_status(this)" value="{{ $city->id }}" type="checkbox" <?php if($city->status == 1) echo "checked";?> >
                                          <span class="slider round"></span>
                                        </label>
                                      </td>
                                    <td class="text-right">
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('cities.edit', ['id'=>$city->id, 'lang'=>env('DEFAULT_LANGUAGE')]) }}" title="{{ translate('Edit') }}">
                                            <i class="las la-edit"></i>
                                        </a>
                                        <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('cities.destroy', $city->id)}}" title="{{ translate('Delete') }}">
                                            <i class="las la-trash"></i>
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
        <div class="col-md-5">
    		<div class="card">
    			<div class="card-header">
    				<h5 class="mb-0 h6">{{ translate('Add New city') }}</h5>
    			</div>
    			<div class="card-body">
    				<form action="{{ route('cities.store') }}" method="POST">
    					@csrf
    					<div class="form-group mb-3">
    						<label for="name">{{translate('Name')}}</label>
    						<input type="text" placeholder="{{translate('Name')}}" name="name" class="form-control" required>
    					</div>

                        <div class="form-group">
                            <label for="country">{{translate('State')}}</label>
                            <select class="select2 form-control aiz-selectpicker" name="state_id" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">
                                @foreach ($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
    						<label for="name">{{translate('Cost')}}</label>
    						<input type="number" min="0" step="0.01" placeholder="{{translate('Cost')}}" name="cost" class="form-control" required>
    					</div>
    					<div class="form-group mb-3 text-right">
    						<button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
    					</div>
    				</form>
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
            $.post('{{ route('cities.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
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
