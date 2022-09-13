@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{translate('All Role')}}</h1>
		</div>
        @can('add_staff_role')
            <div class="col-md-6 text-md-right">
                <a href="{{ route('roles.create') }}" class="btn btn-circle btn-info">
                    <span>{{translate('Add New Role')}}</span>
                </a>
            </div>
        @endcan
	</div>
</div>
{{-- <div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Add New Permission')}}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('roles.permission') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label for="name">{{translate('Name')}}</label>
                        <input type="text" id="name" name="name" placeholder="{{ translate('Permission') }}" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="name">{{translate('Parent')}}</label>
                        <input type="text" id="parent" name="parent" placeholder="{{ translate('Parent') }}" class="form-control" required>
                    </div>
                    <div class="form-group mb-3 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> --}}

<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Roles')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table">
            <thead>
                <tr>
                    <th width="10%">#</th>
                    <th>{{translate('Name')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($roles as $key => $role)
                    <tr>
                        <td>{{ ($key+1) + ($roles->currentPage() - 1)*$roles->perPage() }}</td>
                        <td>{{ $role->name}}</td>
                        <td class="text-right">
                            @can('edit_staff_role')
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('roles.edit', ['id'=>$role->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                            @endcan
                            @if($role->id != 1 && auth()->user()->can('delete_staff_role'))
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('roles.destroy', $role->id)}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $roles->appends(request()->input())->links() }}
        </div>
    </div>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
