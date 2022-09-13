<div class="modal-header bord-btm">
    <h4 class="modal-title h6">{{ translate('Select variation') }} - {{ $stocks->first()->product->getTranslation('name') }}</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
</div>
<div class="modal-body">
    <div class="row gutters-5">
        @foreach ($stocks as $key => $stock)
            <div class="col-lg-3 col-sm-6">
                <label class="aiz-megabox d-block">
                    <input type="radio" name="variant" value="{{ $stock->variant }}" @if ($stock->qty <= 0)
                        disabled
                    @endif>
                    <span class="d-flex p-2 pad-all aiz-megabox-elem">
                        <span class="aiz-rounded-check flex-shrink-0 @if ($stock->qty > 0)
                            mt-1
                        @endif"></span>
                        <span class="flex-grow-1 pad-lft pl-2">
                            <span class="d-block strong-600">{{ $stock->variant }}</span>
                            <span class="d-block">Price: {{ single_price($stock->price) }}</span>
                            <span class="badge badge-inline @if ($stock->qty <= 0)
                                badge-secondary
                            @else
                                badge-success
                            @endif">Stock: {{ $stock->qty }}</span>
                        </span>
                    </span>
                </label>
            </div>
        @endforeach
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-styled btn-base-3" data-dismiss="modal">Close</button>
    <button type="button" onclick="addVariantProductToCart({{ $stocks->first()->product->id }})" class="btn btn-primary btn-styled btn-base-1">Add Product</button>
</div>
