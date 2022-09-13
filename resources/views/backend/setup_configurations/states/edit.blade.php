@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{ translate('State Information') }}</h5>
</div>

<div class="row">
  <div class="col-lg-6 mx-auto">
      <div class="card">
            <div class="card-header">
    			<h5 class="mb-0 h6">{{ translate('Edit State') }}</h5>
    	    </div>
            <div class="card-body p-0">
                <form class="p-4" action="{{ route('states.update', $state->id) }}" method="POST" enctype="multipart/form-data">
                    <input name="_method" type="hidden" value="PATCH">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name">{{ translate('Name') }}</label>
                        <input type="text" placeholder="{{ translate('Name') }}" value="{{ $state->name }}" name="name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="state_id">{{translate('Country')}}</label>
                        <select class="select2 form-control aiz-selectpicker" name="country_id" data-selected="{{ $state->country_id }}" data-toggle="select2" data-placeholder="Choose ..." data-live-search="true">
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3 text-right">
                        <button type="submit" class="btn btn-primary">
                            {{ translate('Update') }}
                            </button>
                    </div>
                </form>
            </div>
      </div>
  </div>
</div>

@endsection
