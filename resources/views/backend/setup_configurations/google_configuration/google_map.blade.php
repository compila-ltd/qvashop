@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('Google Map Setting')}}</h3>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('google-map.update') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="control-label">{{translate('Google Map')}}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input value="1" name="google_map" type="checkbox" @if (get_setting('google_map') == 1)
                                        checked
                                    @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MAP_API_KEY">
                            <div class="col-md-4">
                                <label class="control-label">{{translate('Map API KEY')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="MAP_API_KEY" value="{{  env('MAP_API_KEY') }}" placeholder="{{ translate('Map API KEY') }}">
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
