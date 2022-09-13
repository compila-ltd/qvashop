<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrderCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id'                =>$data->id,
                    'order_code'        => $data->code,
                    'total'             => format_price($data->grand_total),
                    'order_date'        => date('d-m-Y', strtotime($data->created_at)),
                    'payment_status'    => $data->payment_status,
                    'delivery_status'   => $data->delivery_status
                ];
            })
        ];
    }
}
