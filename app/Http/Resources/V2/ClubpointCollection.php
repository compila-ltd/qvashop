<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ClubpointCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {

                $points = number_format($data->points, 2, '.', '');
                $points = floatval($points);

                return [
                    'id'        => (int) $data->id,                    
                    'user_id'    => (int) $data->user_id,
                    'order_code' =>$data->order->code,
                    'points'    => floatval($points),
                    'convert_status' => (int) $data->convert_status,
                    'date'      => date('d-m-Y', strtotime($data->created_at)),
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
