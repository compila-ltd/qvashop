@extends('seller.layouts.app')

@section('panel_content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">Modificar costo de envío</h5>
</div>

<div class="row">
  <div class="col-lg-8 mx-auto">
      <div class="card">
          <div class="card-body p-0">
                <form class="p-4" action="{{ route('seller.cities.update', $city->city_id) }}" method="POST" enctype="multipart/form-data">
                  <input name="_method" type="hidden" value="PATCH">
                  @csrf
                  <div class="form-group mb-3">
                      <label for="name">{{ translate('Name')}}</label>
                      <input type="text" disabled placeholder="{{ translate('Name')}}" value="{{ $city->name }}" name="name" class="form-control" required>
                  </div>

                  <div class="form-group mb-3">
                      <label for="name">Costo</label>
                      <input type="number" min="0" step="0.01" placeholder="{{ translate('Cost')}}" name="cost" class="form-control" value="{{ $city->cost }}" required>
                  </div>


                  <div class="form-group mb-3 text-right">
                      <button type="submit" class="btn btn-primary">{{ translate('Update')}}</button>
                  </div>
              </form>
          </div>
      </div>
  </div>
</div>

@endsection
