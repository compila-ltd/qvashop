@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
	<div class="row align-items-center">
		<div class="col-md-6">
			<h1 class="h3">{{ translate('Negociable transportation')}}</h1>
		</div>
		<div class="col-md-6 text-md-right">
			<a href="{{ route('negotiable_transportation.create') }}" class="btn btn-circle btn-info">
				<span>{{ translate('Negociable transportation')}}</span>
			</a>
		</div>
	</div>
</div>

<div class="card">
    <form class="" id="sort_negotiable_transportation" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Negociable transportation') }}</h5>
            </div>
            <div class="col-md-4">
                <select class="form-control aiz-selectpicker" name="user_search_id" id="user_search_id" data-live-search="true" onchange="sort_negotiable_transportation()">
                    <option value="">{{ translate('Filter by user')}}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" @if ($user_search_id == $user->id) selected @endif>{{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg" width="2%">#</th>
                        <th width="15%">{{ translate('Date')}}</th>
                        <th width="20%">{{ translate('User')}}</th>
                        <th width="20%">{{ translate('Email')}}</th>
                        <th width="20%">{{ translate('Shop name')}}</th>
                        <th data-breakpoints="lg" width="10%">{{ translate('Costo')}}</th>
                        <th>{{ translate('Status')}}</th>
                        <th width="10%" class="text-right">{{ translate('Options')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($negotiable_transportations as $key => $negotiable_transportation)
                        @php
                            $shop_name = 'QvaShop';
                            
                            $shop = \App\Models\Shop::where('id', $negotiable_transportation->shop_id)->first();
                            
                            if($shop)
                                $shop_name = $shop->name;

                        @endphp
                        <tr>
                            <td class="align-middle">{{ $key+1 }}</td>
                            <td class="align-middle">{{ $negotiable_transportation->created_at->format('d-m-Y H:i:s') }}</td>
                            <td class="align-middle">{{ $negotiable_transportation->user->name }}</td>
                            <td class="align-middle">{{ $negotiable_transportation->user->email }}</td>
                            <td class="align-middle">{{ $shop_name }}</td>
                            <td class="align-middle">{{ $negotiable_transportation->cost }}</td>
                            <td class="align-middle">
                                @if ($negotiable_transportation->status == 1)
                                    <div class="badge badge-inline badge-danger">
                                        {{ translate('Not used') }}
                                    </div>
                                @else
                                    <div class="badge badge-inline badge-success">
                                        {{ translate('Used') }}
                                    </div>
                                @endif
                            </td>
                            @if ($negotiable_transportation->status == 1)
                                <td class="text-right">
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('negotiable_transportation.edit', ['id'=>$negotiable_transportation->id] )}}" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('negotiable_transportation.destroy', $negotiable_transportation->id)}}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                </td>
                            @else
                                <td class="text-right">
                                    -
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $negotiable_transportations->appends(request()->input())->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function sort_negotiable_transportation(el){
            $('#sort_negotiable_transportation').submit();
        }
    </script>
@endsection
