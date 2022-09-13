<?php

namespace App\Http\Resources\V2;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CarrierCollection extends ResourceCollection
{
    protected $ownerId;

    public function extra($owner_id){
        $this->ownerId = $owner_id;
        return $this;
    }
    
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function($data) {
                return [
                    'id'            => $data->id,
                    'name'          => $data->name,
                    'logo'          => uploaded_asset($data->logo),
                    'transit_time'  => (integer) $data->transit_time,
                    'free_shipping' => $data->free_shipping == 1 ? true : false,
                    'transit_price' => single_price(carrier_base_price(auth()->user()->carts, $data->id, $this->ownerId)),
                ];
            })
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status'  => 200
        ];
    }
}
