@extends('frontend.layouts.app')

@section('content')
<section class="text-center py-6">
	<div class="container">
		<div class="row">
			<div class="col-lg-6 mx-auto">
				<img src="{{ static_asset('assets/img/403.svg') }}" class="mw-100 mx-auto mb-5" height="350">
			    <h1 class="fw-700">{{ translate('Access Denined!') }}</h1>
			    <p class="fs-16 opacity-60">{{ translate("You Do not Have The Right Permission To Access.") }}</p>
			</div>
		</div>
    </div>
</section>
@endsection
