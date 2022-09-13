@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
    	<div class="row align-items-center">
    		<div class="col-md-12">
    			<h1 class="h3">{{translate('All States')}}</h1>
    		</div>
    	</div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <form class="" id="sort_cities" action="" method="GET">
                    <div class="card-header row gutters-5">
                        <div class="col text-center text-md-left">
                            <h5 class="mb-md-0 h6">{{ translate('States') }}</h5>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="sort_state" name="sort_state" @isset($sort_state) value="{{ $sort_state }}" @endisset placeholder="{{ translate('Type state name') }}">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control aiz-selectpicker" data-live-search="true" id="sort_country" name="sort_country">
                                <option value="">{{ translate('Select Country') }}</option>
                                @foreach (\App\Models\Country::where('status', 1)->get() as $country)
                                    <option value="{{ $country->id }}" @if ($sort_country == $country->id) selected @endif {{$sort_country}}>
                                        {{ $country->name }}
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
                                <th width="10%">#</th>
                                <th>{{translate('Name')}}</th>
                                <th>{{translate('Country')}}</th>
                                <th>{{translate('Show/Hide')}}</th>
                                <th class="text-right">{{translate('Action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($states as $key => $state)
                                <tr>
                                    <td>{{ ($key+1) + ($states->currentPage() - 1)*$states->perPage() }}</td>
                                    <td>{{ $state->name }}</td>
                                    <td>{{ $state->country->name }}</td>
                                    <td>
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input onchange="update_status(this)" value="{{ $state->id }}" type="checkbox" <?php if($state->status == 1) echo "checked";?> >
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td class="text-right">
                                        <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{ route('states.edit', $state->id) }}" title="{{ translate('Edit') }}">
                                            <i class="las la-edit"></i>
                                        </a>
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
        <div class="col-md-5">
    		<div class="card">
    			<div class="card-header">
    				<h5 class="mb-0 h6">{{ translate('Add New State') }}</h5>
    			</div>
    			<div class="card-body">
    				<form action="{{ route('states.store') }}" method="POST">
    					@csrf
    					<div class="form-group mb-3">
    						<label for="name">{{translate('Name')}}</label>
    						<input type="text" placeholder="{{translate('Name')}}" name="name" class="form-control" required>
    					</div>

                        <div class="form-group">
                            <label for="country">{{translate('Country')}}</label>
                            <select class="select2 form-control aiz-selectpicker" name="country_id" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">
                                @foreach (\App\Models\Country::where('status', 1)->get() as $country)
                                    <option value="{{ $country->id }}">
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
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

@section('script')
    <script type="text/javascript">

        function update_status(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('states.status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
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
