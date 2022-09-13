<?php

namespace App\Http\Controllers\Api\V2;

use App\Models\Cart;
use App\Models\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\V2\CarrierCollection;
use App\Models\Country;

class CarrierController extends Controller
{
    public function index()
    {
        $seller_wise_carrier_list = array();
        $carts = Cart::where('user_id', auth()->user()->id)->get();
        if (count($carts) > 0) {
            $zone = $carts[0]['address'] ? Country::where('id',$carts[0]['address']['country_id'])->first()->zone_id : null;
            $carrier_query = Carrier::query();
            $carrier_query->whereIn('id',function ($query) use ($zone) {
                $query->select('carrier_id')->from('carrier_range_prices')
                ->where('zone_id', $zone);
            })->orWhere('free_shipping', 1);
            $carriers_list = $carrier_query->active()->get();
            foreach($carts->unique('owner_id') as $cart) {
                $new_carrier_list = [];
                foreach($carriers_list as  $carrier_list) {
                    $new_carrier_list['id']            = $carrier_list->id;
                    $new_carrier_list['name']          = $carrier_list->name;
                    $new_carrier_list['logo']          = uploaded_asset($carrier_list->logo);
                    $new_carrier_list['transit_time']  = (integer) $carrier_list->transit_time;
                    $new_carrier_list['free_shipping'] = $carrier_list->free_shipping == 1 ? true : false;
                    $new_carrier_list['transit_price'] = carrier_base_price($carts, $carrier_list->id, $cart->owner_id);

                    $seller_wise_carrier_list[$cart->owner_id][] = $new_carrier_list;
                }
            }
        }
        return response()->json([
            'data'    => $seller_wise_carrier_list,
            'success' => true,
            'status'  => 200
        ]);
        // return (new CarrierCollection($carrier_list))->extra($request->owner_id);
    }
}
