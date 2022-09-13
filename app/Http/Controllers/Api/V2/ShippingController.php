<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Resources\V2\AddressCollection;
use App\Http\Resources\V2\PickupPointResource;
use App\Models\Cart;
use App\Models\City;
use App\Models\PickupPoint;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function pickup_list()
    {
        $pickup_point_list = PickupPoint::where('pick_up_status', '=', 1)->get();

        return PickupPointResource::collection($pickup_point_list);
        // return response()->json(['result' => true, 'pickup_points' => $pickup_point_list], 200);
    }

    public function shipping_cost(Request $request)
    {
        $carts = Cart::where('user_id', auth()->user()->id)
            ->get();

        foreach ($request->seller_list as $key => $seller) {
            $seller['shipping_cost'] = 0;
            
            $carts = Cart::where('user_id', auth()->user()->id)->where("owner_id", $seller['seller_id'])
            ->get();


            foreach ($carts as $key => $cartItem) {
                $cartItem['shipping_cost'] = 0;
                
                if($seller['shipping_type'] == 'pickup_point') {
                    $cartItem['shipping_type'] = 'pickup_point';
                    $cartItem['pickup_point'] = $seller['shipping_id'];
                }else
                if ($seller['shipping_type'] == 'home_delivery') {
                    $cartItem['shipping_type'] = 'home_delivery';
                    $cartItem['pickup_point'] = 0;
                    
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key);
                }else
                if ($seller['shipping_type'] == 'carrier') {
                    $cartItem['shipping_type'] = 'carrier';
                    $cartItem['pickup_point'] = 0;
                    $cartItem['carrier_id'] = $seller['shipping_id'];
                    $cartItem['shipping_cost'] = getShippingCost($carts, $key,$seller['shipping_id']);
                }
    
                $cartItem->save();
            }
        }









        
        //Total shipping cost $calculate_shipping

       $total_shipping_cost = Cart::where('user_id', auth()->user()->id)->sum('shipping_cost');
        return response()->json(['result' => true, 'shipping_type' => get_setting('shipping_type'), 'value' => convert_price($total_shipping_cost), 'value_string' => format_price($total_shipping_cost)], 200);
    }

    
    public function getDeliveryInfo()
    {
        $owner_ids = Cart::where('user_id', auth()->user()->id)->select('owner_id')->groupBy('owner_id')->pluck('owner_id')->toArray();
        $currency_symbol = currency_symbol();
        $shops = [];
        if (!empty($owner_ids)) {
            foreach ($owner_ids as $owner_id) {
                $shop = array();
                $shop_items_raw_data = Cart::where('user_id', auth()->user()->id)->where('owner_id', $owner_id)->get()->toArray();
                $shop_items_data = array();
                if (!empty($shop_items_raw_data)) {
                    foreach ($shop_items_raw_data as $shop_items_raw_data_item) {
                        $product = Product::where('id', $shop_items_raw_data_item["product_id"])->first();
                        $shop_items_data_item["id"] = intval($shop_items_raw_data_item["id"]) ;
                        $shop_items_data_item["owner_id"] =intval($shop_items_raw_data_item["owner_id"]) ;
                        $shop_items_data_item["user_id"] =intval($shop_items_raw_data_item["user_id"]) ;
                        $shop_items_data_item["product_id"] =intval($shop_items_raw_data_item["product_id"]) ;
                        $shop_items_data_item["product_name"] = $product->getTranslation('name');
                        $shop_items_data_item["product_thumbnail_image"] = uploaded_asset($product->thumbnail_img);
/*
                        $shop_items_data_item["variation"] = $shop_items_raw_data_item["variation"];
                        $shop_items_data_item["price"] =(double) cart_product_price($shop_items_raw_data_item, $product, false, false);
                        $shop_items_data_item["currency_symbol"] = $currency_symbol;
                        $shop_items_data_item["tax"] =(double) cart_product_tax($shop_items_raw_data_item, $product,false);
                        $shop_items_data_item["shipping_cost"] =(double) $shop_items_raw_data_item["shipping_cost"];
                        $shop_items_data_item["quantity"] =intval($shop_items_raw_data_item["quantity"]) ;
                        $shop_items_data_item["lower_limit"] = intval($product->min_qty) ;
                        $shop_items_data_item["upper_limit"] = intval($product->stocks->where('variant', $shop_items_raw_data_item['variation'])->first()->qty) ;
*/
                        $shop_items_data[] = $shop_items_data_item;

                    }
                }


                $shop_data = Shop::where('user_id', $owner_id)->first();


                if ($shop_data) {
                    $shop['name'] = $shop_data->name;
                    $shop['owner_id'] =(int) $owner_id;
                    $shop['cart_items'] = $shop_items_data;
                    
                } else {
                    $shop['name'] = "Inhouse";
                    $shop['owner_id'] =(int) $owner_id;
                    $shop['cart_items'] = $shop_items_data;
                    
                }
                $shop['carriers'] = seller_base_carrier_list($owner_id);
                $pickup_point_list = PickupPoint::where('pick_up_status', '=', 1)->get();
                $shop['pickup_points']  = PickupPointResource::collection($pickup_point_list);

                $shops[] = $shop;
            }
        }

        //dd($shops);

        return response()->json($shops);
    }
}
