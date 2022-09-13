@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Role Information')}}</h5>
</div>


<div class="col-lg-12 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill border-light">
      				@foreach (\App\Models\Language::all() as $key => $language)
      					<li class="nav-item">
      						<a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('roles.edit', ['id'=>$role->id, 'lang'=> $language->code] ) }}">
      							<img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
      							<span>{{$language->name}}</span>
      						</a>
      					</li>
    	            @endforeach
      			</ul>
            <form class="p-4" action="{{ route('roles.update', $role->id) }}" method="POST">
                <input name="_method" type="hidden" value="PATCH">
                <input type="hidden" name="lang" value="{{ $lang }}">
            	   @csrf
                <div class="form-group row">
                    <label class="col-md-3 col-from-label" for="name">{{translate('Name')}} <i class="las la-language text-danger" title="{{translate('Translatable')}}"></i></label>
                    <div class="col-md-9">
                        @php $roleForTranslation = \App\Models\Role::where('id',$role->id)->first(); @endphp
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{ $roleForTranslation->getTranslation('name', $lang) }}" required>
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
                    <div class="bd-example">
                        <ul class="list-group">
                            <li class="list-group-item bg-light" aria-current="true">{{ translate(Str::headline($permission_group[0]['section'])) }}</li>
                            <li class="list-group-item">
                                <div class="row">
                                    @foreach ($permission_group as $key => $permission)
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
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                            <div class="p-2 border mt-1 mb-2">
                                                <label class="control-label d-flex">{{ translate(Str::headline($permission->name))}}</label>
                                                <label class="aiz-switch aiz-switch-success">
                                                    <input type="checkbox" name="permissions[]" class="form-control demo-sw" value="{{ $permission->id }}"
                                                        @if ($role->hasPermissionTo($permission->name))
                                                            checked
                                                        @endif >
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                </div>
                            </li>
                        </ul>
                    </div>
                <br>
                @endforeach
                <div class="form-group mb-3 mt-3 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
