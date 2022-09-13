@extends('frontend.layouts.app')

@section('content')

    <section class="mb-4 pt-3">
        <div class="container">
            <h1 class="d-block text-center h2 my-5 fw-700">{{ translate('Inhouse products') }}</h1>
            <div class="row gutters-5 row-cols-xxl-4 row-cols-xl-3 row-cols-lg-4 row-cols-md-3 row-cols-2">
                @foreach ($products as $key => $product)
                    <div class="col">
                        @include('frontend.partials.product_box_1',['product' => $product])
                    </div>
                @endforeach
            </div>
            <div class="aiz-pagination aiz-pagination-center mt-4">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </section>

@endsection

