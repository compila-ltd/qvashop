<?php

namespace App\Http\Resources\V2\Seller;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $description = $this->quantity;
        if($this->variation) {
            $description = $this->quantity. ' x '. $this->variation;
        }
        return [
            'name' => optional($this->product)->name,
            'description' => $description,
            'delivery_status' => $this->delivery_status,
            'price' => format_price($this->price),
        ];

    
    }
}
