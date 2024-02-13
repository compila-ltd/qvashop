@extends('seller.layouts.app')

@section('panel_content')

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Pickup Point Information')}}</h5>
            </div>
            <form action="{{ route('seller.pick_up_points.store') }}" method="POST">
            	@csrf
                <div class="card-body">
                    <div class="form-group row row">
                        <label class="col-sm-3 col-from-label" for="name">{{ translate('Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ translate('Name')}}" id="name" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row row">
                        <label class="col-sm-3 col-from-label" for="address">{{ translate('Location')}}</label>
                        <div class="col-sm-9">
                            <textarea name="address" rows="8" class="form-control" required></textarea>
                        </div>
                    </div>
                    <div class="form-group row row">
                        <label class="col-sm-3 col-from-label" for="phone">{{ translate('Phone')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{ translate('Phone')}}" id="phone" name="phone" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group row row">
                        <label class="col-sm-3 col-from-label">{{ translate('Pickup Point Status')}}</label>
                        <div class="col-sm-3">
                            <label class="aiz-switch aiz-switch-success mb-0" style="margin-top:5px;">
                        		<input value="1" type="checkbox" name="pick_up_status">
                        		<span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    <div class="form-group row row">
                        <label class="col-sm-3 col-from-label" for="manager">{{ translate('Pick-up Point Manager')}}</label>
                        <div class="col-sm-9">
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{ translate('Name')}}" id="manager" name="manager" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{ translate('Save')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
