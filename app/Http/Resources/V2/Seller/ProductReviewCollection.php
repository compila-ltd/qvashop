<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductReviewCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
       // dd($this);
        return [
            "data"=>$this->collection->map( function ($data){
                return [
                    "id"=> (int) $data->id,
                    "rating"=>(int) $data->rating,
                    "comment"=> $data->comment,
                    "status"=>(int) $data->status,
                    "updated_at"=> $data->updated_at,
                    "product_name"=> $data->product_name,
                    "user_id"=>(int)  $data->user_id,
                    "name"=> $data->name,
                    "avatar"=> $data->avatar
                ];

            }),

        ];
        
    }
}
