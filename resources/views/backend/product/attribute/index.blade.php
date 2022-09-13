@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="align-items-center">
		<h1 class="h3">{{translate('All Attributes')}}</h1>
	</div>
</div>

<div class="row">
	<div class="@if(auth()->user()->can('add_product_attribute')) col-lg-7 @else col-lg-12 @endif">
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0 h6">{{ translate('Attributes')}}</h5>
			</div>
			<div class="card-body">
				<table class="table aiz-table mb-0">
					<thead>
						<tr>
							<th>#</th>
							<th>{{ translate('Name')}}</th>
							<th>{{ translate('Values')}}</th>
							<th class="text-right">{{ translate('Options')}}</th>
						</tr>
					</thead>
					<tbody>
						@foreach($attributes as $key => $attribute)
							<tr>
								<td>{{$key+1}}</td>
								<td>{{$attribute->getTranslation('name')}}</td>
								<td>
									@foreach($attribute->attribute_values as $key => $value)
									<span class="badge badge-inline badge-md bg-soft-dark">{{ $value->value }}</span>
									@endforeach
								</td>
								<td class="text-right">
									@can('view_product_attribute_values')
										<a class="btn btn-soft-info btn-icon btn-circle btn-sm" href="{{route('attributes.show', $attribute->id)}}" title="{{ translate('Attribute values') }}">
											<i class="las la-cog"></i>
										</a>
									@endcan
									@can('edit_product_attribute')
										<a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('attributes.edit', ['id'=>$attribute->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
											<i class="las la-edit"></i>
										</a>
									@endcan
									@can('delete_product_attribute')
										<a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('attributes.destroy', $attribute->id)}}" title="{{ translate('Delete') }}">
											<i class="las la-trash"></i>
										</a>
									@endcan
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
	@can('add_product_attribute')
		<div class="col-md-5">
			<div class="card">
				<div class="card-header">
						<h5 class="mb-0 h6">{{ translate('Add New Attribute') }}</h5>
				</div>
				<div class="card-body">
					<form action="{{ route('attributes.store') }}" method="POST">
						@csrf
						<div class="form-group mb-3">
							<label for="name">{{translate('Name')}}</label>
							<input type="text" placeholder="{{ translate('Name')}}" id="name" name="name" class="form-control" required>
						</div>
						<div class="form-group mb-3 text-right">
							<button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	@endcan
</div>


@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection
