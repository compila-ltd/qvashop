@extends('backend.layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('Google Firebase Setting')}}</h3>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('google-firebase.update') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="control-label">{{translate('Google Firebase')}}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input value="1" name="google_firebase" type="checkbox" @if (get_setting('google_firebase') == 1)
                                        checked
                                    @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="FCM_SERVER_KEY">
                            <div class="col-md-4">
                                <label class="control-label">{{translate('FCM SERVER KEY')}}</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control" name="FCM_SERVER_KEY" value="{{  env('FCM_SERVER_KEY') }}" placeholder="{{ translate('FCM SERVER KEY') }}">
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
