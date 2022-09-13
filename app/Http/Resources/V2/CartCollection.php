<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CartCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id' => $data->id,
                    'seller_id' => $data->seller_id,
                    'product' => [
                        'name' => $data->product->name,
                        'image' => uploaded_asset($data->product->thumbnail_img)
                    ],
                    'variation' => $data->variation,
                    'price' => (double) cart_product_price($data, $data->product, false, false),
                    'tax' => (double)cart_product_tax($data,  $data->product ,false),
                    'shipping_cost' => (double) $data->shipping_cost,
                    'quantity' => (integer) $data->quantity,
                    'date' => $data->created_at->diffForHumans()
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
