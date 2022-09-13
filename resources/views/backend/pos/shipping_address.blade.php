<div class="">
    @foreach (\App\Models\Address::where('user_id',$user_id)->get() as $key => $address)
        <label class="aiz-megabox d-block bg-white" style="display:block">
            <input type="radio" name="address_id" value="{{ $address->id }}" @if ($address->set_default) checked @endif required>
            <span class="d-flex p-3 pad-all aiz-megabox-elem">
                <span class="aiz-rounded-check flex-shrink-0 mt-1"></span>
                <span class="flex-grow-1 pl-3 pad-lft">
                    <div>
                        <span class="alpha-6">{{ translate('Address') }}:</span>
                        <span class="strong-600 ml-2">{{ $address->address }}</span>
                    </div>
                    <div>
                        <span class="alpha-6">{{ translate('Postal Code') }}:</span>
                        <span class="strong-600 ml-2">{{ $address->postal_code }}</span>
                    </div>
                    <div>
                        <span class="alpha-6">{{ translate('City') }}:</span>
                        <span class="strong-600 ml-2">{{ $address->city->name }}</span>
                    </div>
                    <div>
                        <span class="alpha-6">{{ translate('State') }}:</span>
                        <span class="strong-600 ml-2">{{ $address->state->name }}</span>
                    </div>
                    <div>
                        <span class="alpha-6">{{ translate('Country') }}:</span>
                        <span class="strong-600 ml-2">{{ $address->country->name }}</span>
                    </div>
                    <div>
                        <span class="alpha-6">{{ translate('Phone') }}:</span>
                        <span class="strong-600 ml-2">{{ $address->phone }}</span>
                    </div>
                </span>
            </span>
        </label>
    @endforeach
    <input type="hidden" id="customer_id" value="{{$user_id}}" >
    <div class="" onclick="add_new_address()">
        <div class="border p-3 rounded mb-3 bord-all pad-all c-pointer text-center bg-white">
            <i class="fa fa-plus fa-2x"></i>
            <div class="alpha-7">{{ translate('Add New Address') }}</div>
        </div>
    </div>
</div>
