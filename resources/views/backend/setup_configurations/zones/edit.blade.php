@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Add New Zone') }}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('zones.index') }}" class="btn btn-primary">
                    <span>{{ translate('Back') }}</span>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Zone Information') }}</h5>
                </div>

                <form action="{{ route('zones.update', $zone->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label>{{ translate('Name') }}</label>
                            <input type="text" name="name" class="form-control" placeholder="{{ translate('Zone Name') }}" value="{{ $zone->name }}">

                            @error('name')
                                <span class="text-danger"> {{ $message }}</span>
                            @enderror

                        </div>
                        <div class="form-group">
                            <label>{{ translate('Select Country') }}</label>

                            <select name="country_id[]" class="aiz-selectpicker form-control" data-live-search="true" multiple>
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @if(isset($country->zone) && $country->zone->id == $zone->id) selected @endif>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group mb-3 text-right">
                            <button type="submit" class="btn btn-primary">{{ translate('Submit') }}</button>
                        </div>
                    </div>

                </form>
            </div>

        </div>

    </div>
@endsection
