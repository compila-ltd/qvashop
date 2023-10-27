@extends('backend.layouts.app')

@section('content')

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Select Shipping Method')}}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('shipping_configuration.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="shipping_type">
                    <div class="radio mar-btm">
                        <input id="product-shipping" class="magic-radio" type="radio" name="shipping_type" value="product_wise_shipping" @if(get_setting('shipping_type') == 'product_wise_shipping') checked @endif>
                        <label for="product-shipping">
                            <span>Costo de envío del producto (Product Wise Shipping)</span>
                            <span></span>
                        </label>
                    </div>
                    <div class="radio mar-btm">
                        <input id="flat-shipping" class="magic-radio" type="radio" name="shipping_type" value="flat_rate" @if(get_setting('shipping_type') == 'flat_rate') checked @endif>
                        <label for="flat-shipping">{{ translate('Flat Rate Shipping Cost')}} (Flat Rate Shipping Cost)</label>
                    </div>
                    <div class="radio mar-btm">
                        <input id="seller-shipping" class="magic-radio" type="radio" name="shipping_type" value="seller_wise_shipping" @if(get_setting('shipping_type') == 'seller_wise_shipping') checked @endif>
                        <label for="seller-shipping">Costo de envío fijo según el vendedor (Seller Wise Shipping)</label>
                    </div>
                    <div class="radio mar-btm">
                        <input id="area-shipping" class="magic-radio" type="radio" name="shipping_type" value="area_wise_shipping" @if(get_setting('shipping_type') == 'area_wise_shipping') checked @endif>
                        <label for="area-shipping">Costo de envío fijo por área (Area Wise Shipping)</label>
                    </div>
                    <div class="radio mar-btm">
                        <input id="weight-shipping" class="magic-radio" type="radio" name="shipping_type" value="carrier_wise_shipping" @if(get_setting('shipping_type') == 'carrier_wise_shipping') checked @endif>
                        <label for="weight-shipping">
                            Costo de envío según el transportista (Carrier Wise Shipping)
                        </label>
                    </div>
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Note')}}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        1. Cálculo del costo de envío del producto: el costo de envío se calcula sumando el costo de envío de cada producto.
                    </li>
                    <li class="list-group-item">
                        2. Cálculo del costo de envío de tarifa fija: no importa cuántos productos compra un cliente. El costo de envío es fijo.
                    </li>
                    <li class="list-group-item">
                        3. Cálculo del costo de envío fijo del vendedor: tarifa fija para cada vendedor. Si los clientes compran 2 productos de dos vendedores, el costo de envío se calcula sumando el costo de envío fijo de cada vendedor.
                    </li>
                    <li class="list-group-item">
                        4. Cálculo del costo de envío fijo por área: tarifa fija para cada área. Si los clientes compran varios productos de un vendedor, el costo de envío lo calcula el área de envío del cliente. Para configurar el costo de envío por área, vaya a <a href="{{ route('cities.index') }}"> Ciudades de envío</a>.
                    </li>
                    <li class="list-group-item">
                        5. Cálculo del costo de envío según el transportista: el costo de envío se calcula además con el transportista. En cada transportista puede establecer el costo de envío gratuito o puede establecer el rango de peso o el costo de envío del rango de precios. Para configurar el costo de envío según el transportista, vaya a <a href="{{ route('carriers.index') }}"> Transportistas</a>.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Flat Rate Cost')}}</h5>
            </div>
            <form action="{{ route('shipping_configuration.update') }}" method="POST" enctype="multipart/form-data">
              <div class="card-body">
                  @csrf
                  <input type="hidden" name="type" value="flat_rate_shipping_cost">
                  <div class="form-group">
                      <div class="col-lg-12">
                          <input class="form-control" type="text" name="flat_rate_shipping_cost" value="{{ get_setting('flat_rate_shipping_cost') }}">
                      </div>
                  </div>
                  <div class="form-group mb-0 text-right">
                      <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save')}}</button>
                  </div>
              </div>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Note')}}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        El costo de envío de tarifa plana se aplica si el envío de tarifa plana está habilitado.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Shipping Cost for Admin Products')}}</h5>
            </div>
            <form action="{{ route('shipping_configuration.update') }}" method="POST" enctype="multipart/form-data">
              <div class="card-body">
                  @csrf
                  <input type="hidden" name="type" value="shipping_cost_admin">
                  <div class="form-group">
                      <div class="col-lg-12">
                          <input class="form-control" type="text" name="shipping_cost_admin" value="{{ get_setting('shipping_cost_admin') }}">
                      </div>
                  </div>
                  <div class="form-group mb-0 text-right">
                      <button type="submit" class="btn btn-sm btn-primary">{{ translate('Save')}}</button>
                  </div>
              </div>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{ translate('Note')}}</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <li class="list-group-item">
                        El costo de envío para el administrador se aplica si el costo de envío del vendedor está habilitado.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection
