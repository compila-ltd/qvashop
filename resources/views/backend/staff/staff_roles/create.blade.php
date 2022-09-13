@extends('backend.layouts.app')

@section('content')

<div class="col-lg-12 mx-auto">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{translate('Role Information')}}</h5>
        </div>
        <form action="{{ route('roles.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Name')}}</label>
                    <div class="col-md-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                    </div>
                </div>
                <div class="card-header">
                    <h5 class="mb-0 h6">{{ translate('Permissions') }}</h5>
                </div>
                <br>
                @php
                    $permission_groups =  \App\Models\Permission::all()->groupBy('section');
                @endphp
                @foreach ($permission_groups as $key => $permission_group)
                    @php
                        $check = true;
                        $addons = array("offline_payment", "club_point", "pos_system", "paytm", "seller_subscription", "otp_system", "refund_request", "affiliate_system", "african_pg", "delivery_boy", "auction", "wholesale");
                        
                        if(in_array($permission_group[0]['section'], $addons)){

                            if (addon_is_activated($permission_group[0]['section']) == false) {
                                $check = false;
                            }
                        }
                    @endphp
                    @if($check)
                        <div class="bd-example">
                            <ul class="list-group">
                              <li class="list-group-item bg-light" aria-current="true">{{ translate(Str::headline($permission_group[0]['section'])) }}</li>
                              <li class="list-group-item">
                                  <div class="row">
                                    @foreach ($permission_group as $key => $permission)
                                          <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                              <div class="p-2 border mt-1 mb-2">
                                                  <label class="control-label d-flex">{{ translate(Str::headline($permission->name)) }}</label>
                                                  <label class="aiz-switch aiz-switch-success">
                                                      <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="{{ $permission->id }}">
                                                      <span class="slider round"></span>
                                                  </label>
                                              </div>
                                          </div>
                                    @endforeach
                                </div>
                            </li>
                          </ul>
                    @endif
                  </div>
                  <br>
                @endforeach

                <div class="form-group mb-3 mt-3 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
