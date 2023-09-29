@extends('frontend.layouts.user_panel')

@section('panel_content')

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Compare')}}</h5>
        </div>
    

        <div class="card-body">
            <div class="bg-white shadow-sm rounded">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div class="fs-15 fw-600">{{ translate('Comparison')}}</div>
                    <a href="{{ route('compare.reset') }}" style="text-decoration: none;" class="btn btn-soft-primary btn-sm fw-600">{{ translate('Reset Compare List')}}</a>
                </div>
                @if(Session::has('compare'))
                    @if(count(Session::get('compare')) > 0)
                        <div class="p-3">
                            <table class="table table-responsive table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width:16%" class="font-weight-bold">
                                            {{ translate('Name')}}
                                        </th>
                                        @foreach (Session::get('compare') as $key => $item)
                                            <th scope="col" style="width:28%" class="font-weight-bold">
                                                <a class="text-reset fs-15" href="{{ route('product', \App\Models\Product::find($item)->slug) }}">{{ \App\Models\Product::find($item)->getTranslation('name') }}</a>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">{{ translate('Image')}}</th>
                                        @foreach (Session::get('compare') as $key => $item)
                                            <td>
                                                <img loading="lazy" src="{{ uploaded_asset(\App\Models\Product::find($item)->thumbnail_img) }}" alt="{{ translate('Product Image') }}" class="img-fluid py-4">
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ translate('Price')}}</th>
                                        @foreach (Session::get('compare') as $key => $item)
                                            @php
                                                $product = \App\Models\Product::find($item);
                                            @endphp
                                            <td>
                                                @if(home_base_price($product) != home_discounted_base_price($product))
                                                    <del class="fw-600 opacity-50 mr-1">{{ home_base_price($product) }}</del>
                                                @endif
                                                <span class="fw-700 text-primary">{{ home_discounted_base_price($product) }}</span>
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ translate('Brand')}}</th>
                                        @foreach (Session::get('compare') as $key => $item)
                                            <td>
                                                @if (\App\Models\Product::find($item)->brand != null)
                                                    {{ \App\Models\Product::find($item)->brand->getTranslation('name') }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th scope="row">{{ translate('Category')}}</th>
                                        @foreach (Session::get('compare') as $key => $item)
                                            <td>
                                                @if (\App\Models\Product::find($item)->category != null)
                                                    {{ \App\Models\Product::find($item)->category->getTranslation('name') }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        <th scope="row"></th>
                                        @foreach (Session::get('compare') as $key => $item)
                                            <td class="text-center py-4">
                                                <button type="button" class="btn btn-primary fw-600" onclick="showAddToCartModal({{ $item }})">
                                                    {{ translate('Add to cart')}}
                                                </button>
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="col">
                        <div class="text-center bg-white p-4 rounded shadow">
                            <img class="mw-100 h-200px" src="{{ asset('assets/img/nothing.svg') }}" alt="Image">
                            <h5 class="mb-0 h5 mt-3">{{ translate("There isn't anything added yet")}}</h5>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>


@endsection
